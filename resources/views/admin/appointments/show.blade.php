@extends('layouts.app')

@section('title', 'Деталі запису - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li><a href="{{ route('appointments.index') }}">Записи</a></li>
                <li class="is-active"><a href="#" aria-current="page">Запис #{{ $appointment->id }}</a></li>
            </ul>
        </nav>

        <div class="box">
            <div class="level mb-0">
                <div class="level-left">
                    <h1 class="title is-3 has-text-primary m-0">📅 Деталі запису #{{ $appointment->id }}</h1>
                </div>
                <div class="level-right">
                    <a href="{{ route('appointments.edit', $appointment) }}" class="button is-primary">Редагувати</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Інформація про запис</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Дата</div>
                    <div class="text-gray-900">{{ $appointment->appointment_date->format('d.m.Y') }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Час</div>
                    <div class="text-gray-900">{{ $appointment->appointment_time ? substr($appointment->appointment_time, 0, 5) : '' }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Тривалість</div>
                    <div class="text-gray-900">{{ $appointment->duration }} хвилин</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Статус</div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($appointment->status === 'scheduled') bg-cyan-100 text-cyan-800
                            @elseif($appointment->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($appointment->status === 'completed') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if($appointment->status === 'scheduled') Заплановано
                            @elseif($appointment->status === 'confirmed') Підтверджено
                            @elseif($appointment->status === 'completed') Виконано
                            @else Скасовано
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Клієнт</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Ім'я</div>
                    <div class="text-gray-900">
                        <a href="{{ route('clients.show', $appointment->client) }}" class="text-purple-600 no-underline hover:underline">
                            {{ $appointment->client->user->name }}
                        </a>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Email</div>
                    <div class="text-gray-900">{{ $appointment->client->user->email }}</div>
                </div>
                @if($appointment->client->phone)
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Телефон</div>
                    <div class="text-gray-900">{{ $appointment->client->phone }}</div>
                </div>
                @endif
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Послуга та майстер</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Послуга</div>
                    <div class="text-gray-900">{{ $appointment->service->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Категорія</div>
                    <div class="text-gray-900">{{ $appointment->service->category->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Майстер</div>
                    <div class="text-gray-900">{{ $appointment->employee->user->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Ціна</div>
                    <div class="text-xl font-semibold text-purple-600">
                        {{ number_format($appointment->price, 2) }} грн
                    </div>
                </div>
            </div>
        </div>

        @if($appointment->notes)
        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-semibold text-purple-600 mb-4">Примітки</h3>
            <div class="text-gray-900 whitespace-pre-wrap">{{ $appointment->notes }}</div>
        </div>
        @endif

        @if($appointment->cancellation_reason)
        <div class="bg-yellow-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-semibold text-purple-600 mb-4">Причина скасування</h3>
            <div class="text-gray-900">{{ $appointment->cancellation_reason }}</div>
        </div>
        @endif

        <!-- Секція платежів -->
        <div class="mt-8">
            <div class="bg-blue-50 p-6 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">💰 Платежі</h3>
                @php
                    $totalPaid = $appointment->payments()->where('status', 'completed')->sum('amount');
                    $remaining = $appointment->remaining_amount;
                    $isPaid = $appointment->isPaid();
                @endphp
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Ціна запису</div>
                    <div class="text-xl font-semibold">{{ number_format($appointment->price, 2) }} грн</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Оплачено</div>
                    <div class="text-xl font-semibold text-green-600">{{ number_format($totalPaid, 2) }} грн</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Залишок</div>
                    <div class="text-xl font-semibold text-red-600">{{ number_format($remaining, 2) }} грн</div>
                </div>
                @if($isPaid)
                    <div class="mt-4 p-3 bg-green-100 rounded-lg text-green-800 font-semibold">
                        ✓ Запис оплачено повністю
                    </div>
                @else
                    <div class="mt-4">
                        <a href="{{ route('payments.create', ['appointment_id' => $appointment->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-green-700 transition">
                            + Додати платіж
                        </a>
                    </div>
                @endif
            </div>

            @if($appointment->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Дата</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Сума</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Спосіб оплати</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Статус</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointment->payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 border-b border-gray-200">{{ $payment->payment_date->format('d.m.Y') }}</td>
                                    <td class="p-4 border-b border-gray-200 font-semibold">{{ number_format($payment->amount, 2) }} грн</td>
                                    <td class="p-4 border-b border-gray-200">{{ $payment->payment_method_name }}</td>
                                    <td class="p-4 border-b border-gray-200">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($payment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                            @elseif($payment->status === 'refunded') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $payment->status_name }}
                                        </span>
                                    </td>
                                    <td class="p-4 border-b border-gray-200">
                                        <a href="{{ route('payments.show', $payment) }}" class="button is-primary is-small">Деталі</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-600">
                    Немає платежів для цього запису
                </div>
            @endif
        </div>

        <!-- Секція відгуків -->
        @if(isset($canReview) && $canReview)
        <div class="box mt-5">
            <h3 class="title is-5 has-text-primary mb-4">⭐ Залишити відгук</h3>
            <p class="mb-4">Ваш запис завершено. Будь ласка, залиште відгук про послугу та майстра.</p>
            <a href="{{ route('reviews.create', ['appointment_id' => $appointment->id]) }}" class="button is-primary">
                Залишити відгук
            </a>
        </div>
        @endif

        @if(isset($existingReview) && $existingReview)
        <div class="mt-8 pt-8 border-t-2 border-gray-200">
            <h3 class="text-xl font-semibold text-purple-600 mb-4">⭐ Ваш відгук</h3>
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Рейтинг</div>
                    <div class="text-gray-900">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="text-{{ $i <= $existingReview->rating ? 'yellow' : 'gray' }}-400">★</span>
                        @endfor
                        ({{ $existingReview->rating }}/5)
                    </div>
                </div>
                @if($existingReview->comment)
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Коментар</div>
                    <div class="text-gray-900 whitespace-pre-wrap">{{ $existingReview->comment }}</div>
                </div>
                @endif
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Статус</div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $existingReview->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $existingReview->is_approved ? 'Схвалено' : 'Очікує модерації' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Дії з записом -->
        @if($appointment->status === 'scheduled' || in_array($appointment->status, ['scheduled', 'confirmed']) || $appointment->status === 'confirmed')
        <div class="box mt-5">
            <h3 class="title is-5 has-text-primary mb-4">Дії</h3>
            <div class="field is-grouped">
                @if($appointment->status === 'scheduled')
                    <div class="control">
                        <form method="POST" action="{{ route('appointments.confirm', $appointment) }}" class="is-inline">
                            @csrf
                            <button type="submit" class="button is-success">Підтвердити запис</button>
                        </form>
                    </div>
                @endif

                @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                    <div class="control">
                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="is-inline" onsubmit="return confirm('Ви впевнені, що хочете скасувати запис?');">
                            @csrf
                            <div class="field has-addons">
                                <div class="control">
                                    <input class="input" type="text" name="cancellation_reason" placeholder="Причина скасування" required>
                                </div>
                                <div class="control">
                                    <button type="submit" class="button is-danger">Скасувати</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                @if($appointment->status === 'confirmed')
                    <div class="control">
                        <form method="POST" action="{{ route('appointments.complete', $appointment) }}" class="is-inline">
                            @csrf
                            <button type="submit" class="button is-success">Відмітити як виконаний</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
@endsection

