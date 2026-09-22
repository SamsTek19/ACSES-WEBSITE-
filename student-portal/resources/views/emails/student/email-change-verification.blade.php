@php($brandColor = '#0b3019')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm your new ACSES email address</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f6f8; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1f2a37;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f6f8; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 20px 45px rgba(11,48,25,0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg, {{ $brandColor }}, #145132); padding:32px 24px; text-align:center;">
                            <p style="margin:0; font-size:12px; letter-spacing:0.35em; color:#a0f5cb; text-transform:uppercase;">ACSES</p>
                            <h1 style="margin:12px 0 0; font-size:24px; font-weight:600; color:#ffffff;">Confirm your new email address</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 28px;">
                            <p style="margin:0 0 12px; font-size:16px; color:#1f2a37;">Hello {{ $student->fullname ?? $student->username ?? 'student' }},</p>
                            <p style="margin:0 0 24px; font-size:15px; line-height:1.7; color:#4b5563;">You recently asked us to update the email address on your ACSES profile. Please confirm the change by clicking the button below within the next {{ $expiresInMinutes }} minutes.</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $verificationUrl }}" style="display:inline-block; background-color:{{ $brandColor }}; color:#ffffff; text-decoration:none; padding:14px 26px; border-radius:9999px; font-size:15px; font-weight:600;">Verify new email</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#4b5563;">If the button above does not work, copy and paste this link into your browser:</p>
                            <p style="margin:0 0 20px; font-size:13px; line-height:1.6; color:#0f172a; word-break:break-all;">{{ $verificationUrl }}</p>

                            <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#4b5563;">If you did not request this change, please ignore this email or contact support immediately so we can secure your account.</p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:28px 0;" />

                            <p style="margin:0 0 6px; font-size:13px; font-weight:600; color:#1f2937; text-transform:uppercase; letter-spacing:0.15em;">Need help?</p>
                            <p style="margin:0; font-size:13px; color:#6b7280;">Reach the ACSES support team at <a href="mailto:acsesrepos@gmail.com" style="color:{{ $brandColor }}; text-decoration:none; font-weight:600;">acsesrepos@gmail.com</a> or call <span style="font-weight:600; color:#1f2937;">055 935 9824</span>.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f9fafb; padding:18px 32px; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#94a3b8;">© {{ now()->year }} {{ config('app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
