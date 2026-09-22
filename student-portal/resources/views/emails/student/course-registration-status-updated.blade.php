<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Course registration status update</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Arial, sans-serif; color: #1f2937; }
        .wrapper { width: 100%; padding: 24px 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; }
        .header { background-color: #0b3019; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; color: #ffffff; }
        .content { padding: 32px 28px; line-height: 1.6; }
        .pill { display: inline-block; padding: 6px 14px; border-radius: 999px; background-color: #e5f2eb; color: #0b3019; font-weight: 600; font-size: 13px; }
        .button { display: inline-block; margin-top: 18px; padding: 12px 20px; border-radius: 999px; background-color: #0b3019; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 14px; }
        .footer { padding: 24px 28px 32px; font-size: 12px; color: #6b7280; text-align: center; }
        .divider { border: 0; border-top: 1px solid #e5e7eb; margin: 28px 0; }
        .comment-box { border-left: 4px solid #0b3019; padding: 12px 16px; background-color: #f9fafb; border-radius: 8px; font-size: 14px; margin-top: 16px; }
        @media (max-width: 600px) {
            .container { border-radius: 0; }
            .content { padding: 24px 20px; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">
        <div class="header">
            <h1>Course Registration Update</h1>
        </div>
        <div class="content">
            <p style="margin-top: 0;">Hi {{ $student?->fullname ?? $student?->username ?? 'there' }},</p>
            <p>Your course registration status has been updated.</p>

            <p style="margin-bottom: 12px;">Current status:</p>
            <span class="pill">{{ $statusLabel }}</span>

            @if ($previousStatusLabel)
                <p style="margin-top: 20px; font-size: 14px; color: #4b5563;">Previous status: <strong>{{ $previousStatusLabel }}</strong></p>
            @endif

            @if ($registration->admin_comment)
                <div class="comment-box">
                    <strong>Admin note:</strong>
                    <p style="margin: 8px 0 0 0;">{{ $registration->admin_comment }}</p>
                </div>
            @endif

            <p style="margin-top: 20px;">You can review your submission and upload any required documents from the student portal.</p>

            <a href="{{ route('student.course-registration.show') }}" class="button">View registration</a>

            <hr class="divider">
            <p style="margin-top: 0;">If you have questions, reply to this email or contact the admin office.</p>
        </div>
        <div class="footer">
            &copy; {{ now()->year }} ACSES. All rights reserved.
        </div>
    </div>
</div>
</body>
</html>
