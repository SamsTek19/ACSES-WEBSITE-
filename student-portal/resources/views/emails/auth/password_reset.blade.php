@php($brandColor = '#0b3019')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset your ACSES Portal password') }}</title>
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
                            <h1 style="margin:8px 0 0; font-size:24px; font-weight:700; color:#ffffff;">{{ __('Reset your password') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 32px 24px;">
                            <p style="margin:0 0 12px; font-size:16px; color:#1f2a37;">{{ __('Hello :name,', ['name' => $user?->fullname ?? __('there')]) }}</p>
                            <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#4b5563;">
                                {{ __('We received a request to reset the password for your ACSES Portal account. Use the secure button below to choose a new password.') }}
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" style="display:inline-block; padding:14px 28px; background-color:{{ $brandColor }}; color:#ffffff; text-decoration:none; font-size:15px; font-weight:600; border-radius:9999px; box-shadow:0 12px 24px rgba(11,48,25,0.22);">
                                            {{ __('Reset password') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px; font-size:14px; color:#6b7280;">
                                {{ __('This link is valid for 60 minutes. For security, it can only be used once.') }}
                            </p>
                            <p style="margin:0 0 16px; font-size:13px; color:#6b7280;">
                                {{ __('If the button above does not work, copy and paste this URL into your browser:') }}
                            </p>
                            <p style="margin:0 0 20px; font-size:12px; word-break:break-all; color:#6366f1;">
                                <a href="{{ $resetUrl }}" style="color:#2563eb; text-decoration:none;">{{ $resetUrl }}</a>
                            </p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:28px 0;" />

                            <p style="margin:0 0 12px; font-size:13px; font-weight:600; color:#1f2937; text-transform:uppercase; letter-spacing:0.12em;">{{ __('Security tips') }}</p>
                            <ul style="margin:0; padding-left:18px; font-size:13px; line-height:1.6; color:#6b7280;">
                                <li>{{ __('Reset your password only if you requested it.') }}</li>
                                <li>{{ __('Use a unique password and don’t reuse credentials from other sites.') }}</li>
                                <li>{{ __('Contact support immediately if you did not request this change.') }}</li>
                            </ul>

                            <p style="margin:24px 0 6px; font-size:13px; color:#6b7280;">
                                {{ __('Need help? Email us at :email', ['email' => config('mail.from.address')]) }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f9fafb; padding:18px 32px; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#94a3b8;">© {{ now()->year }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
