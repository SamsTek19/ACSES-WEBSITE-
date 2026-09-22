@php($brandColor = '#0b3019')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - ACSES Registration</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f6f8; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1f2a37;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f6f8; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 14px 40px rgba(11,48,25,0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg, {{ $brandColor }}, #145132); padding:36px 24px; text-align:center;">
                            <img src="{{ asset('logo.png') }}" alt="ACSES Logo" width="70" height="70" style="width:70px; height:70px; max-width:70px; border-radius:16px; background-color:#ffffff; padding:6px; box-shadow:0 6px 16px rgba(0,0,0,0.15); display:inline-block; margin-bottom:16px;" />
                            <p style="margin:0; font-size:11px; letter-spacing:0.3em; color:#a0f5cb; text-transform:uppercase; font-weight:700;">ACSES Registration</p>
                            <h1 style="margin:8px 0 0; font-size:24px; font-weight:700; color:#ffffff; tracking-tight">Verify Your Email</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 24px;">
                            <p style="margin:0 0 12px; font-size:16px; color:#1f2a37;">Hello {{ $registration->fullname ?? 'there' }},</p>
                            <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#4b5563;">
                                Thank you for registering with the ACSES Portal! Before we can submit your application for admin review, please verify your email address by entering the code below.
                            </p>

                            <table width="100%" role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
                                <tr>
                                    <td style="background-color:#f4faf7; border:2px solid {{ $brandColor }}33; border-radius:16px; text-align:center; padding:20px 12px;">
                                        <p style="margin:0; font-size:32px; letter-spacing:0.5em; font-weight:700; color:{{ $brandColor }};">{{ $code }}</p>
                                        <p style="margin:12px 0 0; font-size:13px; color:#6b7280;">Expires in {{ $expiresInMinutes }} minutes</p>
                                    </td>
                                </tr>
                            </table>

                            <div style="background:#fef3c7; border-left:4px solid #f59e0b; border-radius:8px; padding:16px; margin:0 0 24px;">
                                <p style="margin:0; font-size:14px; color:#92400e; font-weight:600;">What happens next?</p>
                                <p style="margin:8px 0 0; font-size:13px; color:#92400e; line-height:1.5;">
                                    Once you verify your email, your application will be submitted for administrator review. You'll receive another email when your account is approved.
                                </p>
                            </div>

                            <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#4b5563;">
                                If you didn't request this registration, you can safely ignore this email.
                            </p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:28px 0;" />

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 12px;">
                                <tr>
                                    <td style="padding:0;">
                                        <p style="margin:0 0 6px; font-size:13px; font-weight:600; color:#1f2937; text-transform:uppercase; letter-spacing:0.12em;">Your Registration Details</p>
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;">
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#6b7280; width:100px;">Name:</td>
                                                <td style="padding:6px 0; font-size:13px; color:#1f2937; font-weight:500;">{{ $registration->fullname }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#6b7280;">Program:</td>
                                                <td style="padding:6px 0; font-size:13px; color:#1f2937; font-weight:500;">{{ $registration->class }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#6b7280;">Level:</td>
                                                <td style="padding:6px 0; font-size:13px; color:#1f2937; font-weight:500;">Level {{ $registration->year }}00</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 6px; font-size:13px; color:#6b7280;">Need help? Reach our support team at <a href="mailto:{{ config('mail.from.address') }}" style="color:{{ $brandColor }}; text-decoration:none; font-weight:600;">{{ config('mail.from.address') }}</a>.</p>
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
