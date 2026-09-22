<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Due;
use App\Services\Payments\PaystackService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentPaystackPaymentController extends Controller
{
    public function __construct(private readonly PaystackService $paystack)
    {
    }

    public function initialize(Request $request, Due $due): RedirectResponse
    {
        $student = $request->user('student');

        if (! $student || $due->student_id !== $student->getAuthIdentifier()) {
            abort(403);
        }

        if ($due->payment_status === 'paid') {
            return redirect()
                ->route('student.dues.index')
                ->with('status', __('This due was already marked as paid.'));
        }

        $reference = 'DUE-' . $due->due_id . '-' . Str::upper(Str::random(10));

        $payload = [
            'reference' => $reference,
            'amount' => (int) round(((float) $due->amount) * 100),
            'email' => $student->email,
            'currency' => config('paystack.currency', 'GHS'),
            'callback_url' => route('student.payments.paystack.callback'),
            'metadata' => [
                'due_id' => $due->due_id,
                'student_id' => $student->getAuthIdentifier(),
                'student_email' => $student->email,
                'description' => $due->description,
                'academic_year' => $due->academic_year,
            ],
        ];

        $channels = config('paystack.channels');
        if ($channels) {
            $payload['channels'] = array_values(array_filter(array_map('trim', explode(',', $channels))));
        }

        try {
            $response = $this->paystack->initializeTransaction($payload);
        } catch (\Throwable $exception) {
            Log::error('Failed to initialize Paystack transaction', [
                'due_id' => $due->due_id,
                'student_id' => $student->getAuthIdentifier(),
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Unable to start Paystack payment. Please try again later.'));
        }

        if (! Arr::get($response, 'status')) {
            return redirect()
                ->route('student.dues.index')
                ->with('error', Arr::get($response, 'message', __('Unable to start Paystack payment.')));
        }

        $authorizationUrl = Arr::get($response, 'data.authorization_url');

        if (! $authorizationUrl) {
            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Paystack did not return an authorization link.'));
        }

        $due->payment_reference = $reference;
        $due->payment_status = 'pending_verification';
        $due->payment_method = 'paystack';
        $due->payment_notes = __('Awaiting Paystack confirmation.');
        $due->save();

        return redirect()->away($authorizationUrl);
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = (string) $request->query('reference', '');

        if ($reference === '') {
            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Payment reference was not provided by Paystack.'));
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);
        } catch (\Throwable $exception) {
            Log::error('Failed to verify Paystack transaction', [
                'reference' => $reference,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Unable to verify Paystack payment. Please contact support.'));
        }

        $data = Arr::get($response, 'data');

        if (! $data) {
            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Invalid response received from Paystack.'));
        }

        $due = Due::query()->where('payment_reference', $reference)->first();

        if (! $due) {
            return redirect()
                ->route('student.dues.index')
                ->with('error', __('We could not match this Paystack payment to any due.'));
        }

        $metadata = Arr::get($data, 'metadata', []);
        if (is_string($metadata)) {
            $metadata = json_decode($metadata, true) ?? [];
        }

        if ((int) Arr::get($metadata, 'due_id') !== (int) $due->due_id) {
            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Paystack metadata does not match the targeted due.'));
        }

        $status = Arr::get($data, 'status');

        if ($status === 'success') {
            $paidAt = Arr::get($data, 'paid_at');

            $due->payment_status = 'paid';
            $due->payment_method = 'paystack';
            $due->payment_date = $paidAt ? Carbon::parse($paidAt) : now();
            $due->verification_date = now();
            $due->verification_notes = __('Auto-verified via Paystack callback.');
            $due->payment_notes = Arr::get($data, 'gateway_response');
            $due->network = Arr::get($data, 'channel');
            $due->reference_number = Arr::get($data, 'reference');
            $due->save();

            return redirect()
                ->route('student.dues.index')
                ->with('status', __('Payment confirmed via Paystack.'));
        }

        $due->payment_status = 'owing';
        $due->payment_notes = sprintf(
            'Paystack status: %s (%s)',
            $status,
            Arr::get($data, 'gateway_response', 'No gateway response.')
        );
        $due->save();

        return redirect()
            ->route('student.dues.index')
            ->with('error', __('Paystack reported the payment as :status.', ['status' => ucfirst((string) $status)]));
    }

    public function receipt(Request $request, Due $due): Response|RedirectResponse
    {
        $student = $request->user('student');

        if (! $student || (int) $due->student_id !== (int) $student->getAuthIdentifier()) {
            abort(403);
        }

        if ($due->payment_status !== 'paid') {
            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Receipt is available once the payment is marked as paid.'));
        }

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultPaperOrientation', 'portrait');
        $options->set('defaultPaperSize', 'a4');

        $dompdf = new Dompdf($options);

        $institution = config('app.name', 'ACSES Portal');
        $generatedAt = now()->format('F j, Y \a\t g:i A');

        $logoPath = public_path('logo.png');
        $logoData = null;
        if (is_file($logoPath)) {
            $logoContents = @file_get_contents($logoPath);
            if ($logoContents !== false) {
                $logoData = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoContents);
            }
        }

        $html = view('dashboards.student.dues.receipt', [
            'due' => $due,
            'student' => $student,
            'institution' => $institution,
            'generatedAt' => $generatedAt,
            'logoData' => $logoData,
        ])->render();

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        $filename = sprintf(
            'ACSES-receipt-%s.pdf',
            Str::slug($due->payment_reference ?? $due->reference_number ?? ('due-' . $due->due_id))
        );

        $pdfOutput = $dompdf->output();

        return response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
