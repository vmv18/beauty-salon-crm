@component('mail::message')
# Нагадування про запис

@if($hoursBefore == 24)
Через 24 години у вас запланований запис у салоні краси.
@else
Через 2 години у вас запланований запис у салоні краси.
@endif

## Деталі запису

**Послуга:** {{ $appointment->service->name }}  
**Майстер:** {{ $appointment->employee->user->name }}  
**Дата:** {{ $appointment->appointment_date->format('d.m.Y') }}  
**Час:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}  
**Тривалість:** {{ $appointment->duration }} хвилин  
**Ціна:** {{ number_format($appointment->price, 0) }} грн

@if($appointment->notes)
**Примітки:**  
{{ $appointment->notes }}
@endif

**Статус:** {{ $appointment->status === 'scheduled' ? 'Заплановано' : 'Підтверджено' }}

@if($hoursBefore == 24)
Будь ласка, підтвердіть свою присутність або зв'яжіться з нами, якщо потрібні зміни.
@else
Будь ласка, приходьте вчасно. Якщо у вас виникли обставини, які не дозволяють прийти, будь ласка, зв'яжіться з нами якнайшвидше.
@endif

@component('mail::button', ['url' => route('public.booking.success', $appointment)])
Переглянути деталі запису
@endcomponent

**Контакти:**  
📞 +380 (50) 123-45-67  
✉️ info@beautysalon.com

З повагою,  
**Beauty Salon**
@endcomponent

