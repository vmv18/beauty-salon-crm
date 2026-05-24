@extends('layouts.app')

@section('title', 'Деталі платежу - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('payments.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">💰 Деталі платежу #{{ $payment->id }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('payments.edit', $payment) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Редагувати</a>
                <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-300">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Основна інформація</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Сума</div>
                    <div class="text-2xl font-semibold text-green-600">{{ number_format($payment->amount, 2) }} грн</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Спосіб оплати</div>
                    <div class="text-gray-900">{{ $payment->payment_method_name }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Дата платежу</div>
                    <div class="text-gray-900">{{ $payment->payment_date->format('d.m.Y') }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Статус</div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status === 'completed') bg-green-100 text-green-800
                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                            @elseif($payment->status === 'refunded') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $payment->status_name }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Клієнт</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Ім'я</div>
                    <div class="text-gray-900">
                        <a href="{{ route('clients.show', $payment->client) }}" class="text-purple-600 no-underline hover:underline">
                            {{ $payment->client->user->name }}
                        </a>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Email</div>
                    <div class="text-gray-900">{{ $payment->client->user->email }}</div>
                </div>
                @if($payment->client->phone)
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Телефон</div>
                    <div class="text-gray-900">{{ $payment->client->phone }}</div>
                </div>
                @endif
            </div>

            @if($payment->appointment)
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Запис</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">ID запису</div>
                    <div class="text-gray-900">
                        <a href="{{ route('appointments.show', $payment->appointment) }}" class="text-purple-600 no-underline hover:underline">
                            #{{ $payment->appointment->id }}
                        </a>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Послуга</div>
                    <div class="text-gray-900">{{ $payment->appointment->service->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Дата</div>
                    <div class="text-gray-900">{{ $payment->appointment->appointment_date->format('d.m.Y') }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Час</div>
                    <div class="text-gray-900">{{ \Carbon\Carbon::parse($payment->appointment->appointment_time)->format('H:i') }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Ціна запису</div>
                    <div class="text-gray-900">{{ number_format($payment->appointment->price, 2) }} грн</div>
                </div>
            </div>
            @endif
        </div>

        @if($payment->notes)
        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-semibold text-purple-600 mb-4">Примітки</h3>
            <div class="text-gray-900 whitespace-pre-wrap">{{ $payment->notes }}</div>
        </div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-purple-600 mb-4">Системна інформація</h3>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">ID платежу</div>
                <div class="text-gray-900">#{{ $payment->id }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Дата створення</div>
                <div class="text-gray-900">{{ $payment->created_at->format('d.m.Y H:i') }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Останнє оновлення</div>
                <div class="text-gray-900">{{ $payment->updated_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>
    </div>
@endsection

