@component('mail::message')
# Новий запис

У вас новий запис від клієнта.

## Деталі запису

**Клієнт:** {{ $appointment->client->user->name }}  
**Email:** {{ $appointment->client->user->email }}  
@if($appointment->client->phone)
**Телефон:** {{ $appointment->client->phone }}  
@endif

**Послуга:** {{ $appointment->service->name }}  
**Дата:** {{ $appointment->appointment_date->format('d.m.Y') }}  
**Час:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}  
**Тривалість:** {{ $appointment->duration }} хвилин  
**Ціна:** {{ number_format($appointment->price, 0) }} грн

@if($appointment->notes)
**Примітки від клієнта:**  
{{ $appointment->notes }}
@endif

**Статус:** {{ $appointment->status === 'scheduled' ? 'Заплановано' : ($appointment->status === 'confirmed' ? 'Підтверджено' : 'Очікує підтвердження') }}

Будь ласка, підтвердіть запис або зв'яжіться з клієнтом, якщо потрібні зміни.

@component('mail::button', ['url' => url('/appointments/' . $appointment->id)])
Переглянути запис
@endcomponent

З повагою,  
**Beauty Salon CRM**
@endcomponent
