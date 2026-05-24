@extends('layouts.app')

@section('title', 'Редагувати платіж - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('payments.show', $payment) }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        
        <h1 class="text-3xl font-bold text-purple-600 mb-8">✏️ Редагувати платіж</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <p class="mb-2"><strong class="text-purple-600">Клієнт:</strong> {{ $payment->client->user->name }}</p>
            @if($payment->appointment)
                <p><strong class="text-purple-600">Запис:</strong> #{{ $payment->appointment->id }} - {{ $payment->appointment->service->name }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('payments.update', $payment) }}">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="amount" class="block mb-2 text-gray-700 font-medium">Сума *</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount', $payment->amount) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('amount')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6">
                <label for="payment_method" class="block mb-2 text-gray-700 font-medium">Спосіб оплати *</label>
                <select id="payment_method" name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Готівка</option>
                    <option value="card" {{ old('payment_method', $payment->payment_method) == 'card' ? 'selected' : '' }}>Картка</option>
                    <option value="online" {{ old('payment_method', $payment->payment_method) == 'online' ? 'selected' : '' }}>Онлайн</option>
                </select>
                @error('payment_method')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6">
                <label for="payment_date" class="block mb-2 text-gray-700 font-medium">Дата платежу *</label>
                <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('payment_date')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6">
                <label for="status" class="block mb-2 text-gray-700 font-medium">Статус *</label>
                <select id="status" name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Очікує</option>
                    <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>Завершено</option>
                    <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Не вдався</option>
                    <option value="refunded" {{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}>Повернено</option>
                </select>
                @error('status')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block mb-2 text-gray-700 font-medium">Примітки</label>
                <textarea id="notes" name="notes" placeholder="Додаткові примітки..." class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm resize-y min-h-[100px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('notes', $payment->notes) }}</textarea>
                @error('notes')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Оновити</button>
                <a href="{{ route('payments.show', $payment) }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
                <form method="POST" action="{{ route('payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Ви впевнені, що хочете видалити цей платіж?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">Видалити</button>
                </form>
            </div>
        </form>
    </div>
@endsection

