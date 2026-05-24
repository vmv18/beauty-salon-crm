<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Звіт по клієнтах</title>
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
    <h1>Звіт по клієнтах (найбільш активні)</h1>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Клієнт</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Кількість записів</th>
                <th>Сума оплат</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $index => $client)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $client->user->name }}</td>
                    <td>{{ $client->user->email }}</td>
                    <td>{{ $client->phone ?? '—' }}</td>
                    <td>{{ $client->appointments_count }}</td>
                    <td>{{ number_format($client->payments_sum_amount ?? 0, 2) }} грн</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Звіт згенеровано: {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>

