<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #667eea; margin-bottom: 20px; }
        .summary { margin-bottom: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .summary-item { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f8f9fa; }
        .total { font-weight: bold; font-size: 14px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    
    <div class="summary">
        <div class="summary-item"><strong>Загальна сума:</strong> {{ number_format($totalRevenue, 2) }} грн</div>
        <div class="summary-item"><strong>Кількість платежів:</strong> {{ $payments->count() }}</div>
        @foreach($byPaymentMethod as $method => $data)
            <div class="summary-item">
                <strong>{{ $method === 'cash' ? 'Готівка' : ($method === 'card' ? 'Картка' : 'Онлайн') }}:</strong> 
                {{ number_format($data['total'], 2) }} грн ({{ $data['count'] }} платежів)
            </div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Клієнт</th>
                <th>Сума</th>
                <th>Спосіб оплати</th>
                <th>Запис</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('d.m.Y') }}</td>
                    <td>{{ $payment->client->user->name }}</td>
                    <td>{{ number_format($payment->amount, 2) }} грн</td>
                    <td>{{ $payment->payment_method_name }}</td>
                    <td>
                        @if($payment->appointment)
                            #{{ $payment->appointment->id }} - {{ $payment->appointment->service->name }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Звіт згенеровано: {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>

