@extends('layouts.app')

@section('title', 'Розклад майстра - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('employees.show', $employee) }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до майстра</a>
        
        <h1 class="text-3xl font-bold text-purple-600 mb-8">📅 Розклад майстра: {{ $employee->user->name }}</h1>

        <div class="mb-6">
            <a href="{{ route('schedules.create', ['employee_id' => $employee->id]) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">+ Додати день до розкладу</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">День тижня</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Час роботи</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Перерва</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Статус</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daysOfWeek as $dayNum => $dayName)
                        @php
                            $schedule = $schedulesByDay[$dayNum] ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 border-b border-gray-200 font-medium">{{ $dayName }}</td>
                            <td class="p-4 border-b border-gray-200">
                                @if($schedule && $schedule->start_time && $schedule->end_time)
                                    {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                @else
                                    <span class="text-gray-400">Не вказано</span>
                                @endif
                            </td>
                            <td class="p-4 border-b border-gray-200">
                                @if($schedule && $schedule->break_start && $schedule->break_end)
                                    {{ substr($schedule->break_start, 0, 5) }} - {{ substr($schedule->break_end, 0, 5) }}
                                @else
                                    <span class="text-gray-400">Немає</span>
                                @endif
                            </td>
                            <td class="p-4 border-b border-gray-200">
                                @if($schedule)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $schedule->is_working ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $schedule->is_working ? 'Робочий' : 'Вихідний' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Не налаштовано</span>
                                @endif
                            </td>
                            <td class="p-4 border-b border-gray-200">
                                @if($schedule)
                                    <div class="flex gap-2">
                                        <a href="{{ route('schedules.edit', $schedule) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700 transition no-underline">Редагувати</a>
                                        <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" class="inline" onsubmit="return confirm('Ви впевнені, що хочете видалити цей розклад?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700 transition">Видалити</button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('schedules.create', ['employee_id' => $employee->id, 'day_of_week' => $dayNum]) }}" class="px-3 py-1 bg-purple-600 text-white rounded text-xs font-medium hover:bg-purple-700 transition no-underline">Додати</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

