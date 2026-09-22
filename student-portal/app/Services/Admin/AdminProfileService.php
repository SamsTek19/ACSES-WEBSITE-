<?php

namespace App\Services\Admin;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProfileService
{
    public function updateProfile(User $admin, array $data): array
    {
        $fields = ['fullname', 'username', 'email', 'phone_number'];
        $changes = collect($fields)
            ->filter(function (string $field) use ($data): bool {
                return array_key_exists($field, $data);
            })
            ->mapWithKeys(function (string $field) use ($data): array {
                return [$field => $data[$field]];
            })
            ->all();

        $profileUpdated = false;
        $passwordUpdated = false;

        if (! empty($changes)) {
            $admin->fill($changes);

            if ($admin->isDirty(array_keys($changes))) {
                $profileUpdated = true;
            }
        }

        if (! empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
            $passwordUpdated = true;
        }

        if ($profileUpdated || $passwordUpdated) {
            $admin->save();
        }

        return [
            'profile_updated' => $profileUpdated,
            'password_updated' => $passwordUpdated,
        ];
    }

    public function createAdmin(User $actor, array $data): User
    {
        return User::create([
            'fullname' => $data['fullname'] ?? $data['username'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function createSnapshot(User $actor, string $type, ?string $notes = null): array
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory('snapshots');

        $timestamp = now()->format('Ymd_His');
        $baseName = Str::slug($type . '_snapshot_' . $timestamp);
        $envelope = $this->buildSnapshotEnvelope($actor, $type, $notes);

        if ($type === 'database') {
            $sqlFilename = $baseName . '.sql';
            $sqlPath = 'snapshots/' . $sqlFilename;
            $metaPath = 'snapshots/' . $baseName . '.json';

            $disk->put($sqlPath, $this->generateDatabaseSqlDump());

            $metadata = array_merge($envelope, [
                'snapshot' => $this->databaseSnapshotSummary(),
                'dump_file' => $sqlPath,
            ]);

            $disk->put($metaPath, json_encode($metadata, JSON_PRETTY_PRINT));

            return [
                'path' => $sqlPath,
                'filename' => $sqlFilename,
                'size' => $disk->size($sqlPath),
                'meta' => [
                    'type' => $type,
                    'notes' => $notes,
                    'generated_at' => Carbon::parse($envelope['generated_at']),
                ],
            ];
        }

        $jsonFilename = $baseName . '.json';
        $jsonPath = 'snapshots/' . $jsonFilename;
        $payload = array_merge($envelope, [
            'snapshot' => $this->systemSnapshot(),
        ]);

        $disk->put($jsonPath, json_encode($payload, JSON_PRETTY_PRINT));

        return [
            'path' => $jsonPath,
            'filename' => $jsonFilename,
            'size' => $disk->size($jsonPath),
            'meta' => [
                'type' => $type,
                'notes' => $notes,
                'generated_at' => Carbon::parse($envelope['generated_at']),
            ],
        ];
    }

    public function otherAdmins(User $current): Collection
    {
        return User::query()
            ->select(['user_id', 'fullname', 'username', 'email', 'created_at'])
            ->where('role', 'admin')
            ->where('user_id', '<>', $current->getKey())
            ->orderBy('fullname')
            ->get();
    }

    public function recentSnapshots(int $limit = 5): array
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        if (! $disk->exists('snapshots')) {
            return [];
        }

        return collect($disk->files('snapshots'))
            ->filter(function (string $file): bool {
                return Str::endsWith($file, '.json');
            })
            ->map(function (string $file) use ($disk): array {
                $content = $this->decodeSnapshot($disk, $file);
                $downloadPath = $content['dump_file'] ?? $file;
                $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

                return [
                    'path' => $downloadPath,
                    'filename' => basename($downloadPath),
                    'size' => $disk->exists($downloadPath) ? $disk->size($downloadPath) : $disk->size($file),
                    'last_modified' => $lastModified,
                    'content' => $content,
                ];
            })
            ->sortByDesc(function (array $snapshot): Carbon {
                return $snapshot['last_modified'];
            })
            ->take($limit)
            ->values()
            ->all();
    }

    protected function systemSnapshot(): array
    {
        $adminCount = User::query()->where('role', 'admin')->count();
        $studentCount = User::query()->where('role', 'student')->count();

        $recentAdmins = User::query()
            ->where('role', 'admin')
            ->latest('created_at')
            ->limit(5)
            ->get(['fullname', 'username', 'email', 'created_at'])
            ->map(function (User $user): array {
                return [
                    'name' => $user->fullname ?? $user->username,
                    'email' => $user->email,
                    'created_at' => optional($user->created_at)->toIso8601String(),
                ];
            })
            ->all();

        return [
            'user_counts' => [
                'admins' => $adminCount,
                'students' => $studentCount,
                'total' => $adminCount + $studentCount,
            ],
            'recent_admins' => $recentAdmins,
        ];
    }

    protected function databaseSnapshotSummary(): array
    {
        $tables = collect(DB::select('SHOW TABLE STATUS'))
            ->map(function ($table): array {
                $name = $table->Name ?? $table->name ?? null;

                return [
                    'name' => $name,
                    'rows' => (int) ($table->Rows ?? 0),
                    'data_length' => (int) ($table->Data_length ?? 0),
                    'index_length' => (int) ($table->Index_length ?? 0),
                    'auto_increment' => $table->Auto_increment ?? null,
                ];
            })
            ->filter(function (array $table): bool {
                return ! empty($table['name']);
            })
            ->values()
            ->all();

        return [
            'tables' => $tables,
        ];
    }

    protected function buildSnapshotEnvelope(User $actor, string $type, ?string $notes): array
    {
        return [
            'type' => $type,
            'generated_at' => now()->toIso8601String(),
            'actor' => [
                'id' => $actor->getKey(),
                'name' => $actor->fullname ?? $actor->username ?? 'Administrator',
                'email' => $actor->email,
            ],
            'notes' => $notes,
            'system' => [
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'app_url' => config('app.url'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ],
        ];
    }

    protected function generateDatabaseSqlDump(): string
    {
        $connection = DB::connection();
        $database = $connection->getDatabaseName();
        $pdo = $connection->getPdo();

        $lines = [];
        $lines[] = '-- ACSES database snapshot';
        $lines[] = '-- Database: ' . $database;
        $lines[] = '-- Generated at: ' . now()->toIso8601String();
        $lines[] = 'SET NAMES utf8mb4;';
        $lines[] = 'SET FOREIGN_KEY_CHECKS=0;';

        $tables = collect(DB::select('SHOW FULL TABLES'))
            ->map(function ($table): ?string {
                $values = array_values((array) $table);

                return $values[0] ?? null;
            })
            ->filter()
            ->values();

        foreach ($tables as $table) {
            $lines[] = '';
            $lines[] = '-- --------------------------------------------------';
            $lines[] = '-- Table structure for `' . $table . '`';
            $lines[] = '-- --------------------------------------------------';
            $lines[] = 'DROP TABLE IF EXISTS `' . $table . '`;';

            $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = $createResult[0]->{'Create Table'} ?? null;

            if ($createSql) {
                $lines[] = $createSql . ';';
            }

            $columns = Schema::getColumnListing($table);

            if (empty($columns)) {
                continue;
            }

            $lines[] = '';
            $lines[] = '-- Data for table `' . $table . '`';

            $columnList = implode(', ', array_map(function (string $column): string {
                return '`' . $column . '`';
            }, $columns));

            $statement = $pdo->query('SELECT * FROM `' . $table . '`', \PDO::FETCH_ASSOC);

            if (! $statement) {
                $lines[] = '-- (unable to fetch rows)';
                continue;
            }

            $batch = [];
            $rowCounter = 0;

            while (($row = $statement->fetch(\PDO::FETCH_ASSOC)) !== false) {
                $rowValues = [];

                foreach ($columns as $column) {
                    $rowValues[] = $this->quoteValue($pdo, $row[$column] ?? null);
                }

                $batch[] = '(' . implode(', ', $rowValues) . ')';
                $rowCounter++;

                if ($rowCounter % 500 === 0) {
                    $lines[] = 'INSERT INTO `' . $table . '` (' . $columnList . ') VALUES ' . implode(', ', $batch) . ';';
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                $lines[] = 'INSERT INTO `' . $table . '` (' . $columnList . ') VALUES ' . implode(', ', $batch) . ';';
            }

            if ($rowCounter === 0) {
                $lines[] = '-- (no rows)';
            }

            $statement->closeCursor();
        }

        $lines[] = '';
        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected function quoteValue(\PDO $pdo, mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            return $pdo->quote($value);
        }

        if (is_resource($value)) {
            return $pdo->quote(stream_get_contents($value) ?: '');
        }

        return $pdo->quote((string) $value);
    }

    protected function decodeSnapshot(Filesystem|FilesystemAdapter $disk, string $file): array
    {
        try {
            $contents = $disk->get($file);
            $decoded = json_decode($contents, true);

            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
