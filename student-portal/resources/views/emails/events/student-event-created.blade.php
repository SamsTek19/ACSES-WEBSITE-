<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New ACSES Event</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:'Segoe UI',Tahoma,Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background-color:#0b3019;padding:32px 24px;text-align:center;color:#f8fafc;">
                            <h1 style="margin:0;font-size:24px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">ACSES Portal</h1>
                            <p style="margin:12px 0 0;font-size:16px;opacity:0.85;">New campus event announcement</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;">
                            <p style="margin:0 0 16px;font-size:15px;">Hello {{ $student->fullname ?? $student->username ?? 'Student' }},</p>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;">A new event has just been published on the ACSES portal. We would love for you to be part of it.</p>

                            <div style="margin-bottom:24px;padding:20px;border:1px solid #e2e8f0;border-radius:12px;background-color:#f1f5f9;">
                                <h2 style="margin:0 0 8px;font-size:20px;color:#0b3019;">{{ $event['title'] }}</h2>
                                @if(!empty($start))
                                    <p style="margin:0 0 6px;font-size:14px;color:#0b3019;"><strong>Date:</strong> {{ $start->format('l, F j, Y') }}</p>
                                    <p style="margin:0 0 6px;font-size:14px;color:#0b3019;"><strong>Time:</strong> {{ $start->format('g:i A') }}@if(!empty($end)) &ndash; {{ $end->format('g:i A') }}@endif</p>
                                @endif
                                @if(!empty($event['location']))
                                    <p style="margin:0 0 6px;font-size:14px;color:#0b3019;"><strong>Location:</strong> {{ $event['location'] }}</p>
                                @endif
                                @if(!empty($event['description']))
                                    <p style="margin:12px 0 0;font-size:14px;line-height:1.6;color:#334155;">{!! nl2br(e($event['description'])) !!}</p>
                                @endif
                            </div>

                            @if(!empty($event['cta_url']))
                                <p style="text-align:center;margin:0 0 24px;">
                                    <a href="{{ $event['cta_url'] }}" style="display:inline-block;padding:12px 28px;background-color:#0b3019;color:#ffffff;text-decoration:none;font-weight:600;border-radius:999px;">View full event details</a>
                                </p>
                            @endif

                            <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#475569;">Log into the ACSES portal to RSVP or explore more happenings around campus. We look forward to seeing you there!</p>
                            <p style="margin:0;font-size:14px;color:#475569;">Warm regards,<br><strong>ACSES Admin Team</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#0b3019;padding:16px 24px;text-align:center;color:#f8fafc;font-size:12px;">
                            <p style="margin:0;">You are receiving this email because you are enrolled as a student on the ACSES portal.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
