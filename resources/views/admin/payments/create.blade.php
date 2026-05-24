@extends('layouts.app')

@section('title', 'Додати платіж - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('payments.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <h1 class="text-3xl font-bold text-purple-600 mb-8">💰 Додати платіж</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($appointment)
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-purple-600 mb-3">Інформація про запис</h3>
                <p class="mb-2"><strong class="text-purple-600">Послуга:</strong> {{ $appointment->service->name }}</p>
                <p class="mb-2"><strong class="text-purple-600">Дата:</strong> {{ $appointment->appointment_date->format('d.m.Y') }}</p>
                <p class="mb-2"><strong class="text-purple-600">Час:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</p>
                <p class="mb-2"><strong class="text-purple-600">Ціна:</strong> {{ number_format($appointment->price, 2) }} грн</p>
                @php
                    $totalPaid = $appointment->payments()->where('status', 'completed')->sum('amount');
                    $remaining = max(0, $appointment->price - $totalPaid);
                @endphp
                <p class="mb-2"><strong class="text-purple-600">Вже оплачено:</strong> {{ number_format($totalPaid, 2) }} грн</p>
                <p class="mb-2"><strong class="text-purple-600">Залишок:</strong> <span class="font-semibold {{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($remaining, 2) }} грн</span></p>
            </div>
        @endif

        <form method="POST" action="{{ route('payments.store') }}">
            @csrf

            @if($appointment)
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
            @endif

            <div class="mb-6">
                <label for="client_id" class="block mb-2 text-gray-700 font-medium">Клієнт *</label>
                <select id="client_id" name="client_id" required onchange="loadAppointments()" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="">-- Оберіть клієнта --</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ old('client_id', $client?->id) == $c->id ? 'selected' : '' }}>
                            {{ $c->user->name }}
                        </option>
                    @endforeach
                </select>
                @error('client_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            @if($appointment)
                <div class="mb-6">
                    <label for="appointment_id_display" class="block mb-2 text-gray-700 font-medium">Запис</label>
                    <input type="text" id="appointment_id_display" value="Запис #{{ $appointment->id }} - {{ $appointment->service->name }}" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm bg-gray-100">
                </div>
            @else
                <div class="mb-6">
                    <label for="appointment_id" class="block mb-2 text-gray-700 font-medium">Запис (необов'язково)</label>
                    <select id="appointment_id" name="appointment_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                        <option value="">-- Без прив'язки до запису --</option>
                        @foreach($appointments as $apt)
                            <option value="{{ $apt->id }}" {{ old('appointment_id') == $apt->id ? 'selected' : '' }}>
                                #{{ $apt->id }} - {{ $apt->service->name }} ({{ $apt->appointment_date->format('d.m.Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    <div class="text-gray-600 text-xs mt-1">Оберіть клієнта, щоб побачити його записи</div>
                </div>
            @endif

            <div class="mb-6">
                <label for="amount" class="block mb-2 text-gray-700 font-medium">Сума *</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount', $appointment ? $appointment->remaining_amount : '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('amount')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                @if($appointment)
                    <div class="text-gray-600 text-xs mt-1">Максимальна сума: {{ number_format($appointment->remaining_amount, 2) }} грн</div>
                @endif
            </div>

            <div class="mb-6">
                <label for="payment_method" class="block mb-2 text-gray-700 font-medium">Спосіб оплати *</label>
                <select id="payment_method" name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="">-- Оберіть спосіб --</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Готівка</option>
                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Картка</option>
                    <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Онлайн</option>
                </select>
                @error('payment_method')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6">
                <label for="payment_date" class="block mb-2 text-gray-700 font-medium">Дата платежу *</label>
                <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('payment_date')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6">
                <label for="status" class="block mb-2 text-gray-700 font-medium">Статус *</label>
                <select id="status" name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="pending" {{ old('status', 'completed') == 'pending' ? 'selected' : '' }}>Очікує</option>
                    <option value="completed" {{ old('status', 'completed') == 'completed' ? 'selected' : '' }}>Завершено</option>
                    <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Не вдався</option>
                </select>
                @error('status')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block mb-2 text-gray-700 font-medium">Примітки</label>
                <textarea id="notes" name="notes" placeholder="Додаткові примітки..." class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm resize-y min-h-[100px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('notes') }}</textarea>
                @error('notes')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Зберегти</button>
                <a href="{{ route('payments.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>

    <script>
        function loadAppointments() {
            const clientId = document.getElementById('client_id').value;
            const appointmentSelect = document.getElementById('appointment_id');
            
            if (!appointmentSelect) return;
            
            if (!clientId) {
                appointmentSelect.innerHTML = '<option value="">-- Без прив'язки до запису --</option>';
                return;
            }
            
            // Завантажити записи клієнта через AJAX
            fetch(`/api/clients/${clientId}/appointments`)
                .then(response => response.json())
                .then(data => {
                    appointmentSelect.innerHTML = '<option value="">-- Без прив'язки до запису --</option>';
                    data.forEach(appointment => {
                        const option = document.createElement('option');
                        option.value = appointment.id;
                        option.textContent = `#${appointment.id} - ${appointment.service_name} (${appointment.date})`;
                        appointmentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading appointments:', error);
                });
        }
    </script>
@endsection

