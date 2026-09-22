<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $institution }} · Official Payment Receipt</title>
    <style>
        * {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 28px;
            background: #ffffff;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.55;
        }
        .wrapper {
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 20px;
            padding: 26px 28px 30px;
        }
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 18px;
            border-bottom: 2px solid rgba(11, 48, 25, 0.15);
        }
        .header-left {
            flex: 1;
            text-align: center;
        }
        .header-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
        }
        .logo img {
            width: 64px;
            height: 64px;
            margin: 0 auto;
            border-radius: 18px;
            object-fit: contain;
            border: 1px solid rgba(11, 48, 25, 0.25);
            padding: 4px;
        }
        .meta {
            margin-top: 8px;
            font-size: 11px;
            color: rgba(15, 23, 42, 0.6);
        }
        .badge-right {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(11, 48, 25, 0.08);
            color: #0b3019;
            font-size: 10px;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            font-weight: 600;
        }
        .amount-box {
            text-align: right;
        }
        .amount-box span {
            display: block;
        }
        .amount-label {
            font-size: 10px;
            letter-spacing: 0.26em;
            text-transform: uppercase;
            color: rgba(15, 23, 42, 0.5);
            font-weight: 600;
        }
        .amount-value {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 700;
            color: #0b3019;
        }
        .section-columns {
            margin-top: 24px;
            display: flex;
            gap: 24px;
        }
        .column {
            flex: 1;
        }
        .grid {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px 18px;
        }
        .grid .item {
            flex: 1 1 48%;
            min-width: 160px;
        }
        .label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(15, 23, 42, 0.55);
        }
        .value {
            margin-top: 4px;
            font-size: 12px;
            color: #0f172a;
        }
        .section-title {
            font-size: 11px;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: rgba(15, 23, 42, 0.55);
            font-weight: 700;
        }
        .divider {
            margin: 10px 0 16px;
            border-top: 1px solid rgba(15, 23, 42, 0.12);
        }
        .notes {
            margin-top: 16px;
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px dashed rgba(11, 48, 25, 0.3);
            background: rgba(11, 48, 25, 0.05);
            font-size: 11px;
            color: #0b3019;
        }
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 28px;
        }
        .signatures .line {
            flex: 1;
            border-top: 1px solid rgba(15, 23, 42, 0.2);
            padding-top: 6px;
            text-align: center;
            font-size: 10px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(15, 23, 42, 0.6);
        }
        .footer {
            margin-top: 22px;
            text-align: center;
            font-size: 10px;
            color: rgba(15, 23, 42, 0.55);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="header-left">
                @if ($logoData)
                    <div class="logo">
                        <img src="{{ $logoData }}" alt="{{ $institution }} logo">
                    </div>
                @endif
                <div class="meta">Generated {{ $generatedAt }}</div>
            </div>
            <div class="header-right">
                <span class="badge-right">Official receipt</span>
                <div class="amount-box">
                    <span class="amount-label">Total paid</span>
                    <span class="amount-value">GHS {{ number_format((float) $due->amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="section-columns">
            <div class="column">
                <div class="section-title">Student information</div>
                <div class="divider"></div>
                <div class="grid">
                    <div class="item">
                        <div class="label">Name</div>
                        <div class="value">{{ $student->fullname ?? $student->username }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Student ID</div>
                        <div class="value">{{ $student->username ?? '—' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Email</div>
                        <div class="value">{{ $student->email ?? '—' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Class</div>
                        <div class="value">{{ $student->class ?? '—' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Academic year</div>
                        <div class="value">{{ $due->academic_year }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Year group</div>
                        <div class="value">{{ $student->year !== null ? 'Year ' . $student->year : '—' }}</div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="section-title">Transaction details</div>
                <div class="divider"></div>
                <div class="grid">
                    <div class="item">
                        <div class="label">Due description</div>
                        <div class="value">{{ $due->description }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Payment method</div>
                        <div class="value">{{ $due->payment_method ? ucfirst($due->payment_method) : 'Paystack' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Transaction Reference</div>
                        <div class="value">{{ $due->payment_reference ?? $due->reference_number ?? '—' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Network / Bank</div>
                        <div class="value">{{ $due->network ? ucfirst(str_replace('_', ' ', $due->network)) : ($due->payment_method === 'manual' ? 'Manual Transfer' : 'Paystack') }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Status</div>
                        <div class="value">{{ ucfirst($due->payment_status) }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Amount</div>
                        <div class="value">GHS {{ number_format((float) $due->amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-title">Timeline</div>
        <div class="divider"></div>
        <div class="grid">
            <div class="item">
                <div class="label">Due date</div>
                <div class="value">{{ optional($due->due_date)->format('F j, Y') ?? '—' }}</div>
            </div>
            <div class="item">
                <div class="label">Paid on</div>
                <div class="value">{{ optional($due->payment_date)->format('F j, Y g:i A') ?? '—' }}</div>
            </div>
            <div class="item">
                <div class="label">Verified on</div>
                <div class="value">{{ optional($due->verification_date)->format('F j, Y g:i A') ?? '—' }}</div>
            </div>
        </div>

        @if ($due->payment_notes)
            <div class="notes">
                <strong>Transaction Notes:</strong>
                <div>{{ $due->payment_notes }}</div>
            </div>
        @endif

        <div class="signatures">
            <div class="line">Finance officer</div>
            <div class="line">Student signature</div>
        </div>

        <div class="footer">Receipt generated by the ACSES Student Portal · Please keep this document for future reference.</div>
    </div>
</body>
</html>
