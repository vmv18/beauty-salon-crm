<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Нове повідомлення з контактної форми</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(to right, #9333ea, #7c3aed);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .field-value {
            color: #111827;
            font-size: 16px;
            padding: 10px;
            background: white;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        .message-box {
            background: white;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #9333ea;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💅 Beauty Salon</h1>
        <p style="margin: 0;">Нове повідомлення з контактної форми</p>
    </div>
    
    <div class="content">
        <div class="field">
            <div class="field-label">Ім'я</div>
            <div class="field-value">{{ $contactMessage->name }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">Email</div>
            <div class="field-value">{{ $contactMessage->email }}</div>
        </div>
        
        @if($contactMessage->phone)
        <div class="field">
            <div class="field-label">Телефон</div>
            <div class="field-value">{{ $contactMessage->phone }}</div>
        </div>
        @endif
        
        <div class="field">
            <div class="field-label">Повідомлення</div>
            <div class="message-box">
                {{ $contactMessage->message }}
            </div>
        </div>
        
        <div class="field">
            <div class="field-label">Дата та час</div>
            <div class="field-value">{{ $contactMessage->created_at->format('d.m.Y H:i') }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p>Це автоматичне повідомлення з сайту Beauty Salon CRM</p>
        <p>Дата: {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>

