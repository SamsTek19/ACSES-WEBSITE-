@php($studentName = $student->fullname ?? $student->username ?? 'Student')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('New update from ACSES') }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f5f7; font-family: 'Segoe UI', Tahoma, sans-serif; color: #1f2937; }
        .wrapper { max-width: 640px; margin: 0 auto; padding: 32px 16px; }
        .card { background: #ffffff; border-radius: 18px; padding: 32px; box-shadow: 0 20px 45px rgba(11, 48, 25, 0.08); }
        h1 { margin-top: 0; font-size: 24px; color: #0b3019; }
        p { font-size: 15px; line-height: 1.6; }
        .meta { display: inline-block; margin-top: 16px; padding: 6px 14px; border-radius: 9999px; background: rgba(11, 48, 25, 0.1); color: #0b3019; font-size: 12px; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; }
        .message { margin-top: 20px; white-space: pre-line; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #94a3b8; }
        a.btn { display: inline-block; margin-top: 24px; padding: 12px 24px; border-radius: 9999px; background: #0b3019; color: #ffffff; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <span class="meta">{{ __('ACSES announcement') }}</span>
            <h1>{{ __('Hello :name,', ['name' => $studentName]) }}</h1>
            <p>{{ __('We have a new update for you:') }}</p>
            <p style="font-weight: 600; font-size: 18px; color: #0b3019;">{{ $announcement->title }}</p>
            <div class="message">{!! nl2br(e($announcement->content ?? $announcement->excerpt ?? '')) !!}</div>
            <a class="btn" href="{{ config('app.url') }}/student/announcements">{{ __('View all announcements') }}</a>
            <p style="margin-top: 24px;">{{ __('If you have any questions, reach out to the ACSES support team.') }}</p>
            <p style="margin-top: 18px;">{{ __('Warm regards,') }}<br><strong>{{ __('ACSES Communications') }}</strong></p>
        </div>
        <p class="footer">© {{ date('Y') }} ACSES. {{ __('All rights reserved.') }}</p>
    </div>
</body>
</html>
