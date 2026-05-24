@extends('layouts.app')

@section('title', 'Мої записи - Beauty Salon CRM')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-md mb-8">
            <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                <h1 class="text-3xl font-bold text-purple-600 m-0">📅 Мої записи</h1>
                <a href="{{ route('master.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm no-underline font-semibold">Назад до дашборду</a>
            </div>

            <!-- Фільтри -->
            <form method="GET" action="{{ route('master.appointments.index') }}" class="mb-6 flex gap-4 flex-wrap">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white">
                        <option value="">Всі статуси</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Заплановано</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Підтверджено</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Виконано</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Скасовано</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дата</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-semibold">Фільтрувати</button>
                    <a href="{{ route('master.appointments.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-semibold ml-2 no-underline">Скинути</a>
                </div>
            </form>

            @if($appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold text-gray-900">Клієнт</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold text-gray-900">Послуга</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold text-gray-900">Дата</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold text-gray-900">Час</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold text-gray-900">Статус</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold text-gray-900">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 border-b border-gray-200 text-gray-900">{{ $appointment->client->user->name }}</td>
                                    <td class="p-4 border-b border-gray-200 text-gray-900">{{ $appointment->service->name }}</td>
                                    <td class="p-4 border-b border-gray-200 text-gray-900">{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                                    <td class="p-4 border-b border-gray-200 text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                    <td class="p-4 border-b border-gray-200">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                                            @if($appointment->status === 'scheduled') bg-blue-100 text-blue-800
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
                                    </td>
                                    <td class="p-4 border-b border-gray-200">
                                        <a href="{{ route('master.appointments.show', $appointment) }}" class="px-3 py-1 bg-purple-600 text-white rounded text-xs font-medium hover:bg-purple-700 transition no-underline">Деталі</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            @else
                <p class="text-gray-600 text-center py-8">Записів не знайдено.</p>
            @endif
        </div>
    </div>
@endsection

