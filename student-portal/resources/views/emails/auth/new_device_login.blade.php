@php($brandColor = '#0b3019')
@php($title = 'New Device Sign-In Detected')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f6f8; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1f2a37;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f6f8; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 14px 40px rgba(11,48,25,0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg, {{ $brandColor }}, #145132); padding:32px 24px; text-align:center;">
                            <p style="margin:0; font-size:12px; letter-spacing:0.35em; color:#a0f5cb; text-transform:uppercase;">ACSES Security</p>
                            <h1 style="margin:12px 0 0; font-size:24px; font-weight:600; color:#ffffff;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 24px;">
                            <p style="margin:0 0 12px; font-size:16px; color:#1f2a37;">Hello {{ $user?->fullname ?? 'there' }},</p>
                            <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#4b5563;">
                                We noticed a new sign-in to your ACSES account from a device we don't recognize. If this was you, no action is needed.
                            </p>

                            <table width="100%" role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px; background:#f9fafb; border-radius:12px; padding:20px;">
                                <tr>
                                    <td style="padding:16px;">
                                        <p style="margin:0 0 12px; font-size:13px; font-weight:600; color:#1f2937; text-transform:uppercase; letter-spacing:0.12em;">Sign-In Details</p>
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;">
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#6b7280; width:100px;">Device:</td>
                                                <td style="padding:6px 0; font-size:14px; color:#1f2937; font-weight:500;">{{ $deviceName }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#6b7280;">IP Address:</td>
                                                <td style="padding:6px 0; font-size:14px; color:#1f2937; font-weight:500;">{{ $ipAddress }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:14px; color:#6b7280;">Time:</td>
                                                <td style="padding:6px 0; font-size:14px; color:#1f2937; font-weight:500;">{{ $loginTime }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <div style="background:#fef3c7; border-left:4px solid #f59e0b; border-radius:8px; padding:16px; margin:0 0 24px;">
                                <p style="margin:0; font-size:14px; color:#92400e; font-weight:600;">Wasn't you?</p>
                                <p style="margin:8px 0 0; font-size:13px; color:#92400e; line-height:1.5;">
                                    If you didn't sign in from this device, please change your password immediately and contact our support team.
                                </p>
                            </div>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:28px 0;" />

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 12px;">
                                <tr>
                                    <td style="padding:0;">
                                        <p style="margin:0 0 6px; font-size:13px; font-weight:600; color:#1f2937; text-transform:uppercase; letter-spacing:0.12em;">Security Tips</p>
                                        <ul style="margin:0; padding-left:18px; font-size:13px; line-height:1.6; color:#6b7280;">
                                            <li>Use a strong, unique password for your account.</li>
                                            <li>Never share your verification codes with anyone.</li>
                                            <li>Sign out from devices you no longer use.</li>
                                        </ul>
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
