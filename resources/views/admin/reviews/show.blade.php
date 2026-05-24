@extends('layouts.app')

@section('title', 'Відгук #' . $review->id . ' - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600">⭐ Відгук #{{ $review->id }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('reviews.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
                @if(!$review->is_approved)
                    <form method="POST" action="{{ route('reviews.approve', $review) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">Схвалити</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-purple-600 mb-4">Рейтинг</h3>
            <div class="text-2xl mb-4">
                @for($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                @endfor
                <span class="text-gray-900 text-base ml-2">{{ $review->rating }}/5</span>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Статус</div>
                <div>
                    <span class="px-4 py-2 rounded-full text-sm font-medium {{ $review->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $review->is_approved ? 'Схвалено' : 'Очікує модерації' }}
                    </span>
                </div>
            </div>
        </div>

        @if($review->comment)
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-purple-600 mb-4">Коментар</h3>
            <div class="bg-white p-6 rounded-lg border-l-4 border-purple-600 whitespace-pre-wrap leading-relaxed">{{ $review->comment }}</div>
        </div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-purple-600 mb-4">Інформація про запис</h3>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Клієнт</div>
                <div class="text-gray-900">
                    <a href="{{ route('clients.show', $review->client) }}" class="text-purple-600 no-underline hover:underline">
                        {{ $review->client->user->name }}
                    </a>
                </div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Майстер</div>
                <div class="text-gray-900">{{ $review->employee->user->name }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Послуга</div>
                <div class="text-gray-900">{{ $review->service->name }}</div>
            </div>
            @if($review->appointment)
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Запис</div>
                <div class="text-gray-900">
                    <a href="{{ route('appointments.show', $review->appointment) }}" class="text-purple-600 no-underline hover:underline">
                        #{{ $review->appointment->id }} - {{ $review->appointment->appointment_date->format('d.m.Y') }} о {{ \Carbon\Carbon::parse($review->appointment->appointment_time)->format('H:i') }}
                    </a>
                </div>
            </div>
            @endif
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Дата створення</div>
                <div class="text-gray-900">{{ $review->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>

        <div class="flex gap-4 mt-8">
            <a href="{{ route('reviews.edit', $review) }}" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition no-underline">Редагувати</a>
            <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="inline" onsubmit="return confirm('Ви впевнені, що хочете видалити цей відгук?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">Видалити</button>
            </form>
        </div>
    </div>
@endsection

