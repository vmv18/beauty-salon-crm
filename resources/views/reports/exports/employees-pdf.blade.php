<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Звіт по майстрах</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #667eea; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f8f9fa; }
        .total { font-weight: bold; font-size: 14px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Звіт по майстрах (продуктивність)</h1>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Майстер</th>
                <th>Спеціалізація</th>
                <th>Рейтинг</th>
                <th>Кількість записів</th>
                <th>Доходи</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $index => $employee)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $employee->user->name }}</td>
                    <td>{{ $employee->specialization ?? '—' }}</td>
                    <td>{{ number_format($employee->rating, 1) }}</td>
                    <td>{{ $employee->appointments_count }}</td>
                    <td>{{ number_format($employee->revenue, 2) }} грн</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Звіт згенеровано: {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>

