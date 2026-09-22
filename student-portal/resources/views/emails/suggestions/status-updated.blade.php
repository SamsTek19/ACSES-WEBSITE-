@php($studentName = $student->fullname ?? $student->username ?? 'Dear student')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Suggestion update from ACSES') }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f5f7; margin: 0; padding: 0; }
        .wrapper { max-width: 640px; margin: 0 auto; padding: 32px 16px; }
        .card { background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 18px 45px rgba(11, 48, 25, 0.08); }
        h1 { color: #0b3019; font-size: 24px; margin-bottom: 16px; }
        p { color: #475569; line-height: 1.6; font-size: 15px; }
        .badge { display: inline-flex; align-items: center; gap: 8px; border-radius: 9999px; padding: 6px 16px; font-size: 13px; font-weight: 600; text-transform: uppercase; background: rgba(11, 48, 25, 0.12); color: #0b3019; letter-spacing: 0.12em; }
        .footer { margin-top: 32px; text-align: center; font-size: 13px; color: #94a3b8; }
        .panel { background: #f8fafc; border-radius: 12px; padding: 16px 18px; margin-top: 18px; font-size: 14px; color: #475569; }
        a.btn { display: inline-block; padding: 12px 22px; background: #0b3019; color: #ffffff; text-decoration: none; border-radius: 9999px; font-weight: 600; margin-top: 22px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <h1>{{ __('Hello :name,', ['name' => $studentName]) }}</h1>
            <p>{{ __('We wanted to let you know that the status of your suggestion below has been updated.') }}</p>

            <div class="panel">
                <p style="margin: 0 0 12px; font-weight: 600; color: #0f172a;">{{ $suggestion->subject }}</p>
                <p style="margin: 0 0 8px;">{{ __('Previous status: :status', ['status' => $previousLabel]) }}</p>
                <p style="margin: 0;">{{ __('Current status: :status', ['status' => $statusLabel]) }}</p>
            </div>

            <p>{{ __('Thank you for sharing your thoughts with the ACSES team. We appreciate your input and will keep you updated as things progress.') }}</p>

            <a class="btn" href="{{ config('app.url') }}/student/suggestions">{{ __('View your suggestion history') }}</a>

            <p style="margin-top: 24px;">{{ __('If you have any further details or follow-up questions, feel free to reply to this email or reach out through the portal.') }}</p>

            <p style="margin-top: 24px;">{{ __('Warm regards,') }}<br><strong>{{ __('ACSES Support Team') }}</strong></p>
        </div>
        <p class="footer">© {{ date('Y') }} ACSES. {{ __('All rights reserved.') }}</p>
    </div>
</body>
</html>
