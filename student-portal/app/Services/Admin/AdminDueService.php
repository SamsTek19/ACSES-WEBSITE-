<?php

namespace App\Services\Admin;

use App\Models\DefaultDueConfig;
use App\Models\Due;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use ZipArchive;

class AdminDueService
{
    public const STATUS_OPTIONS = [
        'owing' => 'Owing',
        'pending_verification' => 'Pending verification',
        'paid' => 'Paid',
    ];

    public function __construct(private readonly StudentAccountService $studentAccounts)
    {
    }

    public function list(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        $query = Due::query()
            ->with(['student:user_id,fullname,username,email,class,year,index_number']);

        $query = $this->applyFilters($query, $filters)
            ->orderByDesc('created_at');

        return $query->paginate($perPage)->withQueryString();
    }

    public function totals(array $filters): array
    {
        $query = $this->applyFilters(Due::query(), $filters);

        $working = clone $query;
        $totalAmount = (float) (clone $working)->sum('amount');
        $outstandingAmount = (float) (clone $working)->whereIn('payment_status', ['owing', 'pending_verification'])->sum('amount');
        $paidAmount = (float) (clone $working)->where('payment_status', 'paid')->sum('amount');

        $count = (int) (clone $working)->count();

        return [
            'total' => $totalAmount,
            'outstanding' => $outstandingAmount,
            'paid' => $paidAmount,
            'count' => $count,
        ];
    }

    public function export(array $filters): StreamedResponse
    {
        $query = $this->applyFilters(
            Due::query()->with(['student:user_id,fullname,username,email,class,year,index_number']),
            $filters
        )->orderBy('due_id');

        $worksheetXml = $this->buildWorksheetXml($query);
        $xlsxBinary = $this->makeExcelFile($worksheetXml);

        $fileName = 'dues-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($xlsxBinary) {
            echo $xlsxBinary;
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function statistics(array $filters): array
    {
        $paidStatuses = ['paid'];
        $outstandingStatuses = ['owing', 'pending_verification'];

        $aggregates = [
            'total_paid' => 0.0,
            'total_outstanding' => 0.0,
            'count_paid' => 0,
            'count_total' => 0,
            'class_paid' => [],
            'year_paid' => [],
            'class_year_paid' => [],
        ];

        $query = $this->applyFilters(
            Due::query()
                ->with(['student:user_id,class,year'])
                ->orderBy('due_id'),
            $filters
        );

        $query->chunkById(500, function (Collection $dues) use (&$aggregates, $paidStatuses, $outstandingStatuses) {
            foreach ($dues as $due) {
                $aggregates['count_total']++;

                $status = $due->payment_status;
                $amount = (float) $due->amount;
                $student = $due->student;

                if (in_array($status, $outstandingStatuses, true)) {
                    $aggregates['total_outstanding'] += $amount;
                }

                if (! in_array($status, $paidStatuses, true)) {
                    continue;
                }

                $aggregates['count_paid']++;
                $aggregates['total_paid'] += $amount;

                $classKey = $student?->class ?? 'Unassigned';
                $yearRaw = $student?->year;
                $yearKey = $yearRaw !== null ? (string) $yearRaw : 'Unassigned';

                $aggregates['class_paid'][$classKey] = ($aggregates['class_paid'][$classKey] ?? 0) + $amount;
                $aggregates['year_paid'][$yearKey] = ($aggregates['year_paid'][$yearKey] ?? 0) + $amount;

                if ($yearRaw !== null) {
                    $aggregates['class_year_paid'][$classKey][$yearKey] = ($aggregates['class_year_paid'][$classKey][$yearKey] ?? 0) + $amount;
                }
            }
        }, 'due_id');

        $classCollection = collect($aggregates['class_paid'])->sortDesc();
        $yearCollection = collect($aggregates['year_paid'])->sortDesc();

        $classYearPairs = collect($aggregates['class_year_paid'])
            ->flatMap(function (array $years, string $class) {
                return collect($years)->mapWithKeys(fn ($total, $year) => [$class . '|||' . $year => $total]);
            })
            ->sortDesc();

        $bestClassKey = $classCollection->keys()->first();
        $bestYearKey = $yearCollection->keys()->first();
        $bestClassYearKey = $classYearPairs->keys()->first();

        $lowestClassCollection = $classCollection->filter(fn ($amount) => $amount > 0)->sort();
        $lowestClassKey = $lowestClassCollection->keys()->first();

        $leaders = [
            'best_class' => $bestClassKey !== null ? [
                'label' => $bestClassKey,
                'amount' => $classCollection[$bestClassKey],
            ] : null,
            'best_year' => $bestYearKey !== null ? [
                'label' => $bestYearKey === 'Unassigned' ? 'Unassigned' : 'Year ' . $bestYearKey,
                'year' => $bestYearKey,
                'amount' => $yearCollection[$bestYearKey],
            ] : null,
            'best_class_year' => null,
            'lowest_class' => $lowestClassKey !== null ? [
                'label' => $lowestClassKey,
                'amount' => $lowestClassCollection[$lowestClassKey],
            ] : null,
        ];

        if ($bestClassYearKey !== null) {
            [$classLabel, $yearLabel] = explode('|||', $bestClassYearKey);
            $leaders['best_class_year'] = [
                'class' => $classLabel,
                'year' => $yearLabel === 'Unassigned' ? null : $yearLabel,
                'label' => $yearLabel === 'Unassigned'
                    ? $classLabel
                    : ($classLabel . ' Â- Year ' . $yearLabel),
                'amount' => $classYearPairs[$bestClassYearKey],
            ];
        }

        $totalPaid = $aggregates['total_paid'];
        $classBreakdown = $classCollection->map(function ($amount, $class) use ($totalPaid) {
            $share = $totalPaid > 0 ? round(($amount / $totalPaid) * 100, 2) : 0;

            return [
                'label' => $class,
                'amount' => $amount,
                'share' => $share,
            ];
        })->values()->all();

        $yearBreakdown = $yearCollection->map(function ($amount, $year) use ($totalPaid) {
            $share = $totalPaid > 0 ? round(($amount / $totalPaid) * 100, 2) : 0;

            return [
                'label' => $year === 'Unassigned' ? 'Unassigned' : 'Year ' . $year,
                'year' => $year,
                'amount' => $amount,
                'share' => $share,
            ];
        })->values()->all();

        $collectionRate = $aggregates['count_total'] > 0
            ? round(($aggregates['count_paid'] / $aggregates['count_total']) * 100, 2)
            : 0.0;

        return [
            'totals' => [
                'paid_amount' => $aggregates['total_paid'],
                'outstanding_amount' => $aggregates['total_outstanding'],
                'paid_count' => $aggregates['count_paid'],
                'invoice_count' => $aggregates['count_total'],
                'collection_rate' => $collectionRate,
            ],
            'leaders' => $leaders,
            'breakdowns' => [
                'classes' => array_values($classBreakdown),
                'years' => array_values($yearBreakdown),
            ],
        ];
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        $query
            ->when($filters['academic_year'] ?? null, fn (Builder $builder, string $year) => $builder->where('academic_year', $year))
            ->when($filters['status'] ?? null, fn (Builder $builder, string $status) => $builder->where('payment_status', $status))
            ->when($filters['class'] ?? null, function (Builder $builder, string $class) {
                $builder->whereHas('student', fn (Builder $studentQuery) => $studentQuery->where('class', $class));
            })
            ->when($filters['year'] ?? null, function (Builder $builder, string $year) {
                $builder->whereHas('student', fn (Builder $studentQuery) => $studentQuery->where('year', $year));
            });

        if ($search !== '') {
            $query->whereHas('student', function (Builder $studentQuery) use ($search) {
                $like = "%{$search}%";
                $studentQuery->where('fullname', 'like', $like)
                    ->orWhere('username', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('index_number', 'like', $like);
            });
        }

        return $query;
    }

    protected function buildWorksheetXml(Builder $query): string
    {
        $columns = [
            'Due ID',
            'Student',
            'Email',
            'Class',
            'Year',
            'Description',
            'Academic Year',
            'Amount (GHS)',
            'Status',
            'Due date',
            'Payment date',
        ];

        $rowIndex = 1;
        $sheetData = '<sheetData>' . $this->buildRowXml($columns, $rowIndex++);

        $query->chunkById(500, function ($dues) use (&$sheetData, &$rowIndex) {
            foreach ($dues as $due) {
                $student = $due->student;
                $sheetData .= $this->buildRowXml([
                    $due->due_id,
                    $student?->fullname ?? $student?->username ?? ('Student #' . $due->student_id),
                    $student?->email,
                    $student?->class,
                    $student?->year,
                    $due->description,
                    $due->academic_year,
                    number_format((float) $due->amount, 2),
                    self::STATUS_OPTIONS[$due->payment_status] ?? ucwords(str_replace('_', ' ', (string) $due->payment_status)),
                    optional($due->due_date)->format('Y-m-d'),
                    optional($due->payment_date)->format('Y-m-d H:i'),
                ], $rowIndex++);
            }
        }, 'due_id');

        $sheetData .= '</sheetData>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . $sheetData
            . '</worksheet>';
    }

    protected function buildRowXml(array $values, int $rowIndex): string
    {
        $rowXml = '<row r="' . $rowIndex . '">';

        foreach ($values as $index => $value) {
            $cellReference = $this->columnLetter($index) . $rowIndex;
            $value = $value ?? '';

            if (is_numeric($value) && ! str_contains((string) $value, ',')) {
                $rowXml .= '<c r="' . $cellReference . '"><v>' . $value . '</v></c>';
            } else {
                $rowXml .= '<c r="' . $cellReference . '" t="inlineStr"><is><t>'
                    . $this->escapeXml((string) $value)
                    . '</t></is></c>';
            }
        }

        return $rowXml . '</row>';
    }

    protected function makeExcelFile(string $sheetXml): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'dues_xlsx_');
        $zip = new ZipArchive();

        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create Excel export.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();

        $binary = file_get_contents($tmp);
        unlink($tmp);

        return $binary !== false ? $binary : '';
    }

    protected function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    protected function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    protected function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Dues" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    protected function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    protected function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><name val="Segoe UI"/><family val="2"/><sz val="11"/></font></fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    protected function columnLetter(int $index): string
    {
        $index += 1; // convert to 1-based
        $letters = '';

        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = (int) (($index - $mod) / 26);
        }

        return $letters;
    }

    protected function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    public function filterOptions(): array
    {
        $studentFilters = $this->studentAccounts->filterOptions();
        $classes = $studentFilters['classes'] instanceof \Illuminate\Support\Collection
            ? $studentFilters['classes']->values()->all()
            : (array) $studentFilters['classes'];
        $years = $studentFilters['years'] instanceof \Illuminate\Support\Collection
            ? $studentFilters['years']->values()->all()
            : (array) $studentFilters['years'];

        return [
            'academic_years' => Due::query()->select('academic_year')->distinct()->orderByDesc('academic_year')->pluck('academic_year')->all(),
            'classes' => $classes,
            'years' => $years,
            'statuses' => self::STATUS_OPTIONS,
        ];
    }

    public function matrix(): array
    {
        $filters = $this->filterOptions();
        $classes = $filters['classes'];
        $years = $filters['years'];

        $config = DefaultDueConfig::query()
            ->where('is_active', true)
            ->get()
            ->keyBy(fn (DefaultDueConfig $entry) => $entry->class . '|' . $entry->year);

        $matrix = [];
        foreach ($classes as $class) {
            foreach ($years as $year) {
                $key = $class . '|' . $year;
                $matrix[$class][$year] = $config[$key]->amount ?? null;
            }
        }

        return [
            'classes' => $classes,
            'years' => $years,
            'values' => $matrix,
        ];
    }

    public function createDue(array $data, User $admin): int
    {
        $description = $data['description'];
        $academicYear = $data['academic_year'];
        $dueDate = Carbon::parse($data['due_date']);
        $baseAmount = (float) ($data['base_amount'] ?? 0);
        $matrixInput = $data['amounts'] ?? [];

        $filters = $this->filterOptions();
        $classValues = $filters['classes'];
        $yearValues = $filters['years'];

        $amountMatrix = [];
        foreach ($classValues as $class) {
            foreach ($yearValues as $year) {
                $raw = Arr::get($matrixInput, "$class.$year");
                $amountMatrix[$class][$year] = $raw !== null && $raw !== ''
                    ? (float) $raw
                    : $baseAmount;
            }
        }

        DB::transaction(function () use ($classValues, $yearValues, $amountMatrix, $description, $dueDate, $academicYear, $baseAmount, $admin) {
            foreach ($classValues as $class) {
                foreach ($yearValues as $year) {
                    DefaultDueConfig::query()->updateOrCreate(
                        ['class' => $class, 'year' => (string) $year],
                        [
                            'amount' => $amountMatrix[$class][$year],
                            'description' => $description,
                            'due_date_offset' => Carbon::now()->diffInDays($dueDate, false),
                            'target_group' => 'all',
                            'created_by' => $admin->user_id,
                            'is_active' => true,
                        ]
                    );
                }
            }

            User::query()
                ->where('role', 'student')
                ->whereNotNull('email_verified_at')
                ->orderBy('user_id')
                ->chunkById(500, function (Collection $students) use ($amountMatrix, $description, $dueDate, $academicYear, $baseAmount, $admin) {
                    $insert = [];
                    $studentIds = $students->pluck('user_id');

                    $existingStudents = Due::query()
                        ->where('academic_year', $academicYear)
                        ->where('description', $description)
                        ->whereIn('student_id', $studentIds)
                        ->pluck('student_id')
                        ->all();

                    foreach ($students as $student) {
                        if (in_array($student->user_id, $existingStudents, true)) {
                            continue;
                        }

                        $class = $student->class;
                        $year = $student->year;

                        $amount = $baseAmount;
                        if ($class && $year !== null && isset($amountMatrix[$class][$year])) {
                            $amount = $amountMatrix[$class][$year];
                        } elseif ($class && isset($amountMatrix[$class])) {
                            $amount = Arr::first($amountMatrix[$class], fn ($value) => $value !== null, $baseAmount);
                        }

                        $insert[] = [
                            'student_id' => $student->user_id,
                            'description' => $description,
                            'amount' => $amount,
                            'due_date' => $dueDate->toDateString(),
                            'academic_year' => $academicYear,
                            'payment_status' => 'owing',
                            'payment_method' => null,
                            'payment_reference' => null,
                            'payment_date' => null,
                            'verification_date' => null,
                            'verified_by' => null,
                            'verification_notes' => null,
                            'is_active' => true,
                            'network' => null,
                            'reference_number' => null,
                            'payment_notes' => null,
                            'recorded_by' => $admin->user_id,
                            'rejection_reason' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if (! empty($insert)) {
                        Due::query()->insert($insert);
                    }
                }, 'user_id');
        });

        return Due::query()->where('academic_year', $academicYear)->where('description', $description)->count();
    }

    public function updateDue(Due $due, array $data, User $admin): Due
    {
        // Hard-lock "Paid" dues against amount, description, and year edits
        if ($due->payment_status === 'paid') {
            unset($data['amount'], $data['description'], $data['academic_year']);
        }

        $assignable = [
            'description',
            'amount',
            'due_date',
            'academic_year',
            'payment_status',
            'payment_method',
            'payment_reference',
            'payment_date',
            'verification_date',
            'verification_notes',
            'payment_notes',
            'rejection_reason',
            'network',
            'reference_number',
        ];

        foreach ($assignable as $field) {
            if (array_key_exists($field, $data)) {
                $due->$field = $data[$field];
            }
        }

        if (array_key_exists('is_active', $data)) {
            $due->is_active = (bool) $data['is_active'];
        }

        if (! empty($data['verification_date'])) {
            $due->verified_by = $admin->user_id;
        }

        $due->recorded_by = (string) $admin->user_id;
        $due->save();

        return $due->refresh();
    }

    public function deleteDue(Due $due): void
    {
        if ($due->payment_status === 'paid') {
            throw new \Exception(__('Paid dues cannot be deleted.'));
        }

        $due->delete();
    }

    public function syncStudent(User $student): void
    {
        if ($student->role !== 'student') {
            return;
        }

        $class = $student->class;
        $year = $student->year;

        if (! $class || $year === null) {
            return;
        }

        $templates = Due::query()
            ->with('student:user_id,class,year')
            ->whereHas('student', function (Builder $builder) use ($class, $year, $student) {
                $builder->where('class', $class)
                    ->where('year', $year)
                    ->where('user_id', '!=', $student->user_id);
            })
            ->where('is_active', true)
            ->get()
            ->groupBy(fn (Due $due) => $due->academic_year . '|' . $due->description);

        if ($templates->isEmpty()) {
            $configs = DefaultDueConfig::query()
                ->where('class', $class)
                ->where('year', (string) $year)
                ->where('is_active', true)
                ->where('target_group', 'all')
                ->get();

            foreach ($configs as $config) {
                $referenceDue = Due::query()
                    ->where('description', $config->description)
                    ->orderByDesc('created_at')
                    ->first();

                if (! $referenceDue) {
                    continue;
                }

                $exists = Due::query()
                    ->where('student_id', $student->user_id)
                    ->where('academic_year', $referenceDue->academic_year)
                    ->where('description', $config->description)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $baseDate = $config->created_at ?: $referenceDue->due_date ?: now();
                $dueDate = $baseDate;

                if ($config->due_date_offset !== null) {
                    $dueDate = $baseDate->copy()->addDays((int) $config->due_date_offset);
                }

                Due::query()->create([
                    'student_id' => $student->user_id,
                    'description' => $config->description,
                    'amount' => $config->amount,
                    'due_date' => $dueDate->toDateString(),
                    'academic_year' => $referenceDue->academic_year,
                    'payment_status' => 'owing',
                    'payment_method' => null,
                    'payment_reference' => null,
                    'payment_date' => null,
                    'verification_date' => null,
                    'verified_by' => null,
                    'verification_notes' => null,
                    'is_active' => true,
                    'network' => null,
                    'reference_number' => null,
                    'payment_notes' => null,
                    'recorded_by' => $referenceDue->recorded_by,
                    'rejection_reason' => null,
                ]);
            }

            return;
        }

        foreach ($templates as $group) {
            if ($group->isEmpty()) {
                continue;
            }

            $template = $group->first();

            $exists = Due::query()
                ->where('student_id', $student->user_id)
                ->where('academic_year', $template->academic_year)
                ->where('description', $template->description)
                ->exists();

            if ($exists) {
                continue;
            }

            Due::query()->create([
                'student_id' => $student->user_id,
                'description' => $template->description,
                'amount' => $template->amount,
                'due_date' => $template->due_date,
                'academic_year' => $template->academic_year,
                'payment_status' => 'owing',
                'payment_method' => null,
                'payment_reference' => null,
                'payment_date' => null,
                'verification_date' => null,
                'verified_by' => null,
                'verification_notes' => null,
                'is_active' => true,
                'network' => null,
                'reference_number' => null,
                'payment_notes' => null,
                'recorded_by' => $template->recorded_by,
                'rejection_reason' => null,
            ]);
        }
    }
}

