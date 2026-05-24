@extends('layouts.app')

@section('title', 'Деталі запису - Beauty Salon CRM')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('master.appointments.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📅 Деталі запису #{{ $appointment->id }}</h1>
            <a href="{{ route('master.appointments.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
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
                    <div class="text-gray-900">{{ $appointment->client->user->name }}</div>
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
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Послуга</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Послуга</div>
                    <div class="text-gray-900">{{ $appointment->service->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Категорія</div>
                    <div class="text-gray-900">{{ $appointment->service->category->name }}</div>
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

        <!-- Дії з записом -->
        <div class="mt-8 pt-8 border-t-2 border-gray-200">
            <h3 class="text-xl font-semibold text-purple-600 mb-4">Дії</h3>
            <div class="flex flex-col gap-4">
                @if($appointment->status === 'scheduled')
                    <form method="POST" action="{{ route('master.appointments.confirm', $appointment) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">✅ Підтвердити запис</button>
                    </form>
                @endif

                @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-red-800 mb-2">Скасувати запис</h4>
                        <form method="POST" action="{{ route('master.appointments.cancel', $appointment) }}" class="flex gap-2 items-end" onsubmit="return confirm('Ви впевнені, що хочете скасувати запис?');">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Причина скасування *</label>
                                <input type="text" name="cancellation_reason" placeholder="Вкажіть причину скасування" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white" value="{{ old('cancellation_reason') }}">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">Скасувати</button>
                        </form>
                    </div>
                @endif

                @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-800 mb-2">Перенести запис</h4>
                        <form method="POST" action="{{ route('master.appointments.reschedule', $appointment) }}" class="flex gap-2 items-end flex-wrap">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Нова дата *</label>
                                <input type="date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Новий час *</label>
                                <input type="time" name="appointment_time" value="{{ old('appointment_time', substr($appointment->appointment_time, 0, 5)) }}" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Причина перенесення</label>
                                <input type="text" name="reschedule_reason" placeholder="Необов'язково" value="{{ old('reschedule_reason') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">Перенести</button>
                        </form>
                    </div>
                @endif

                @if($appointment->status === 'confirmed')
                    <form method="POST" action="{{ route('master.appointments.complete', $appointment) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">✅ Відмітити як виконаний</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

