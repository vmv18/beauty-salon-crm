<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Звіт по послугах</title>
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
    <h1>Звіт по послугах (популярність)</h1>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Послуга</th>
                <th>Категорія</th>
                <th>Ціна</th>
                <th>Кількість записів</th>
                <th>Доходи</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $index => $service)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->category->name }}</td>
                    <td>{{ number_format($service->price, 2) }} грн</td>
                    <td>{{ $service->appointments_count }}</td>
                    <td>{{ number_format($service->revenue, 2) }} грн</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Звіт згенеровано: {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>

