@php($user = $user ?? null)
@php($context = $context ?? 'login')
@php($brandColor = '#0b3019')
@php($title = $context === 'verification' ? 'Confirm Your ACSES Email' : 'New Device Sign-In Verification')
@php($subtitle = $context === 'verification'
    ? 'Enter the one-time code below to verify your email address and activate your account.'
    : 'We detected a sign-in attempt from a new device. Enter this code to verify it\'s you and trust this device for future logins.')
@php($footerNote = $context === 'verification'
    ? 'If you didn’t request this verification, you can safely ignore this email or contact support.'
    : 'If you didn\'t try to sign in from a new device, please change your password immediately and contact the ACSES support team.')

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
                        <td style="background:linear-gradient(135deg, {{ $brandColor }}, #145132); padding:36px 24px; text-align:center;">
                            <img src="{{ asset('logo.png') }}" alt="ACSES Logo" width="70" height="70" style="width:70px; height:70px; max-width:70px; border-radius:16px; background-color:#ffffff; padding:6px; box-shadow:0 6px 16px rgba(0,0,0,0.15); display:inline-block; margin-bottom:16px;" />
                            <p style="margin:0; font-size:11px; letter-spacing:0.3em; color:#a0f5cb; text-transform:uppercase; font-weight:700;">ACSES Portal</p>
                            <h1 style="margin:8px 0 0; font-size:24px; font-weight:700; color:#ffffff;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 24px;">
                            <p style="margin:0 0 12px; font-size:16px; color:#1f2a37;">Hello {{ $user?->fullname ?? 'there' }},</p>
                            <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#4b5563;">{{ $subtitle }}</p>

                            <table width="100%" role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
                                <tr>
                                    <td style="background-color:#f4faf7; border:2px solid {{ $brandColor }}33; border-radius:16px; text-align:center; padding:20px 12px;">
                                        <p style="margin:0; font-size:32px; letter-spacing:0.5em; font-weight:700; color:{{ $brandColor }};">{{ $code }}</p>
                                        <p style="margin:12px 0 0; font-size:13px; color:#6b7280;">Expires in {{ $expiresInMinutes }} minutes</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#4b5563;">{{ $footerNote }}</p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:28px 0;" />

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 12px;">
                                <tr>
                                    <td style="padding:0;">
                                        <p style="margin:0 0 6px; font-size:13px; font-weight:600; color:#1f2937; text-transform:uppercase; letter-spacing:0.12em;">Security Tips</p>
                                        <ul style="margin:0; padding-left:18px; font-size:13px; line-height:1.6; color:#6b7280;">
                                            <li>Don’t share this code with anyone—ACSES will never ask for it.</li>
                                            <li>Make sure you’re browsing <strong>{{ config('app.url') }}</strong> before entering the code.</li>
                                            <li>Delete this email once you’re done to keep your inbox tidy.</li>
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
