<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quizly Reports</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; line-height: 1.5; padding: 40px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .header h1 { font-size: 28px; color: #4f46e5; margin-bottom: 5px; }
        .header p { color: #64748b; font-size: 14px; }
        .period { background: #f1f5f9; padding: 15px 20px; border-radius: 8px; margin-bottom: 30px; text-align: center; }
        .period strong { color: #4f46e5; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; text-align: center; }
        .stat-value { font-size: 32px; font-weight: bold; color: #0f172a; }
        .stat-value.revenue { color: #10b981; }
        .stat-value.users { color: #3b82f6; }
        .stat-value.assessments { color: #a855f7; }
        .stat-value.tests { color: #f59e0b; }
        .stat-label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 5px; }
        .section-title { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f1f5f9; text-align: left; padding: 12px; font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        .status-success { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 99px; font-size: 12px; font-weight: 500; }
        .status-pending { background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 99px; font-size: 12px; font-weight: 500; }
        .status-failed { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 99px; font-size: 12px; font-weight: 500; }
        .amount { font-weight: 600; color: #10b981; }
        .footer { margin-top: 40px; text-align: center; color: #94a3b8; font-size: 12px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .no-data { text-align: center; padding: 40px; color: #94a3b8; }
        @media print {
            body { padding: 20px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Quizly.</h1>
        <p>Platform Reports & Analytics</p>
    </div>

    <div class="period">
        Report Period: <strong>{{ $fromDate }}</strong> to <strong>{{ $toDate }}</strong>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value revenue">₦{{ number_format($totalRevenue) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-value users">{{ number_format($newUsers) }}</div>
            <div class="stat-label">New Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-value assessments">{{ number_format($assessments) }}</div>
            <div class="stat-label">Assessments Created</div>
        </div>
        <div class="stat-card">
            <div class="stat-value tests">{{ number_format($testsCompleted) }}</div>
            <div class="stat-label">Tests Completed</div>
        </div>
    </div>

    <h3 class="section-title">Transaction History</h3>
    @if($transactions->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>User</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $txn)
            <tr>
                <td>#{{ $txn->reference ?? 'TXN-' . str_pad($txn->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $txn->user?->first_name }} {{ $txn->user?->last_name }}</td>
                <td>{{ ucfirst($txn->billing_cycle ?? 'Monthly') }}</td>
                <td class="amount">₦{{ number_format($txn->amount) }}</td>
                <td>
                    <span class="status-{{ $txn->status }}">{{ ucfirst($txn->status) }}</span>
                </td>
                <td>{{ $txn->paid_at?->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">No transactions found for this period.</div>
    @endif

    <div class="footer">
        <p>Generated on {{ $generatedAt }} | Quizly Assessment Platform</p>
    </div>
</body>
</html>
