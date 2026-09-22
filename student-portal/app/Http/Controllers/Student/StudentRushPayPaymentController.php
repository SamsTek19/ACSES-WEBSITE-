<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Due;
use App\Services\Payments\RushPayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentRushPayPaymentController extends Controller
{
    public function __construct(private readonly RushPayService $rushPay)
    {
    }

    /**
     * Initialize a RushPay transaction.
     */
    public function initialize(Request $request, Due $due): RedirectResponse
    {
        $student = $request->user('student');

        if (!$student || $due->student_id !== $student->getAuthIdentifier()) {
            abort(403);
        }

        if ($due->payment_status === 'paid') {
            return redirect()
                ->route('student.dues.index')
                ->with('status', __('This due was already marked as paid.'));
        }

        // Generate unique reference
        $reference = 'RP-' . $due->due_id . '-' . Str::upper(Str::random(10));

        $payload = [
            'amount' => (float) $due->amount,
            'description' => $due->description,
            'payment_reference' => $reference,
            'metadata' => [
                'due_id' => $due->due_id,
                'student_id' => $student->getAuthIdentifier(),
                'academic_year' => $due->academic_year,
            ],
            'callback_url' => route('student.payments.rushpay.callback'),
        ];

        try {
            $response = $this->rushPay->initializeTransaction($payload);
            
            if (!Arr::get($response, 'success')) {
                throw new \RuntimeException(Arr::get($response, 'message', 'Initialization failed'));
            }

            // RushPay returns its own generated reference, overriding ours
            $apiReference = Arr::get($response, 'data.payment_reference', $reference);

            // Update due with reference
            $due->update([
                'payment_reference' => $apiReference,
                'payment_method' => 'rushpay',
                'payment_status' => 'pending_verification',
                'payment_notes' => __('Awaiting RushPay confirmation.'),
            ]);

            return redirect()->route('student.payments.rushpay.checkout', ['reference' => $apiReference]);

        } catch (\Exception $e) {
            Log::error('RushPay Initialization Error', [
                'due_id' => $due->due_id,
                'message' => $e->getMessage()
            ]);

            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Unable to start RushPay payment. Please try again later.'));
        }
    }

    /**
     * Show the checkout page with the RushPay widget.
     */
    public function checkout(Request $request, string $reference): View|RedirectResponse
    {
        $due = Due::where('payment_reference', $reference)->firstOrFail();
        
        try {
            $sessionResponse = $this->rushPay->generateWidgetSession($reference);
            
            if (!Arr::get($sessionResponse, 'success')) {
                throw new \RuntimeException('Failed to generate widget session');
            }

            return view('dashboards.student.dues.rushpay-checkout', [
                'token' => Arr::get($sessionResponse, 'data.token') ?? Arr::get($sessionResponse, 'data.widget_session_token'),
                'reference' => $reference,
                'due' => $due,
                'callbackUrl' => route('student.payments.rushpay.callback', ['reference' => $reference])
            ]);

        } catch (\Exception $e) {
            Log::error('RushPay Session Error', ['reference' => $reference, 'message' => $e->getMessage()]);
            
            return redirect()
                ->route('student.dues.index')
                ->with('error', __('Unable to load the payment widget.'));
        }
    }

    /**
     * Handle the callback from RushPay.
     */
    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('payment_reference') ?? $request->query('reference');

        if (!$reference) {
            return redirect()->route('student.dues.index')->with('error', __('Invalid payment reference.'));
        }

        try {
            $response = $this->rushPay->verifyTransaction($reference);
            $data = Arr::get($response, 'data');

            if (!Arr::get($response, 'success') || !$data) {
                throw new \RuntimeException('Verification failed');
            }

            $due = Due::where('payment_reference', $reference)->first();

            if (!$due) {
                return redirect()->route('student.dues.index')->with('error', __('Payment reference not found.'));
            }

            $status = strtolower(Arr::get($data, 'status', ''));

            if ($status === 'completed' || $status === 'success') {
                $due->update([
                    'payment_status' => 'paid',
                    'payment_date' => now(),
                    'verification_date' => now(),
                    'verification_notes' => __('Auto-verified via RushPay.'),
                    'payment_notes' => __('Payment successful.'),
                    'reference_number' => Arr::get($data, 'transaction_id', $reference),
                ]);

                return redirect()->route('student.dues.index')->with('status', __('Payment completed successfully!'));
            }

            return redirect()->route('student.dues.index')->with('error', __('Payment status: ') . ucfirst($status));

        } catch (\Exception $e) {
            Log::error('RushPay Callback Error', ['reference' => $reference, 'message' => $e->getMessage()]);

            $due = Due::where('payment_reference', $reference)->first();
            if ($due && str_contains($e->getMessage(), 'Payment not found')) {
                $due->update([
                    'payment_status' => 'owing',
                    'payment_reference' => null,
                    'payment_method' => null,
                    'payment_notes' => null,
                ]);
                return redirect()->route('student.dues.index')->with('error', __('Payment was not completed. Please try paying again.'));
            }

            return redirect()->route('student.dues.index')->with('error', __('Verification failed. Please contact support.'));
        }
    }
}
