@component('mail::message')
# Підтвердження запису

Дякуємо за ваше бронювання! Ваш запис успішно створено.

## Деталі запису

**Послуга:** {{ $appointment->service->name }}  
**Майстер:** {{ $appointment->employee->user->name }}  
**Дата:** {{ $appointment->appointment_date->format('d.m.Y') }}  
**Час:** {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}  
**Тривалість:** {{ $appointment->duration }} хвилин  
**Ціна:** {{ number_format($appointment->price, 0) }} грн

@if($appointment->notes)
**Ваші побажання:**  
{{ $appointment->notes }}
@endif

**Статус:** {{ $appointment->status === 'scheduled' ? 'Заплановано' : ($appointment->status === 'confirmed' ? 'Підтверджено' : 'Очікує підтвердження') }}

@if($appointment->status === 'scheduled')
Ми зв'яжемося з вами для підтвердження запису.
@endif

Якщо у вас виникли питання або потрібно змінити час запису, будь ласка, зв'яжіться з нами.

Дякуємо, що обрали наш салон краси!

@component('mail::button', ['url' => route('public.booking.success', $appointment)])
Переглянути деталі запису
@endcomponent

З повагою,  
**Beauty Salon**
@endcomponent
