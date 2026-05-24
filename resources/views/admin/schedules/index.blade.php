@extends('layouts.app')

@section('title', 'Розклади майстрів - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="bg-white p-8 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📅 Управління розкладами майстрів</h1>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('schedules.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">+ Додати розклад</a>
            </div>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <form method="GET" action="{{ route('schedules.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="flex flex-col">
                    <label for="employee_id" class="mb-2 text-gray-700 font-medium text-sm">Майстер</label>
                    <select id="employee_id" name="employee_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white">
                        <option value="">Всі</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="day_of_week" class="mb-2 text-gray-700 font-medium text-sm">День тижня</label>
                    <select id="day_of_week" name="day_of_week" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white">
                        <option value="">Всі</option>
                        <option value="1" {{ request('day_of_week') == '1' ? 'selected' : '' }}>Понеділок</option>
                        <option value="2" {{ request('day_of_week') == '2' ? 'selected' : '' }}>Вівторок</option>
                        <option value="3" {{ request('day_of_week') == '3' ? 'selected' : '' }}>Середа</option>
                        <option value="4" {{ request('day_of_week') == '4' ? 'selected' : '' }}>Четвер</option>
                        <option value="5" {{ request('day_of_week') == '5' ? 'selected' : '' }}>П'ятниця</option>
                        <option value="6" {{ request('day_of_week') == '6' ? 'selected' : '' }}>Субота</option>
                        <option value="7" {{ request('day_of_week') == '7' ? 'selected' : '' }}>Неділя</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="is_working" class="mb-2 text-gray-700 font-medium text-sm">Тип дня</label>
                    <select id="is_working" name="is_working" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white">
                        <option value="">Всі</option>
                        <option value="1" {{ request('is_working') == '1' ? 'selected' : '' }}>Робочі</option>
                        <option value="0" {{ request('is_working') == '0' ? 'selected' : '' }}>Вихідні</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">Фільтрувати</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">ID</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Майстер</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">День тижня</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Час роботи</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Перерва</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Статус</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 border-b border-gray-200">{{ $schedule->id }}</td>
                            <td class="p-4 border-b border-gray-200">{{ $schedule->employee->user->name ?? 'N/A' }}</td>
                            <td class="p-4 border-b border-gray-200">{{ $schedule->day_name }}</td>
                            <td class="p-4 border-b border-gray-200">
                                @if($schedule->start_time && $schedule->end_time)
                                    {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                @else
                                    <span class="text-gray-400">Не вказано</span>
                                @endif
                            </td>
                            <td class="p-4 border-b border-gray-200">
                                @if($schedule->break_start && $schedule->break_end)
                                    {{ substr($schedule->break_start, 0, 5) }} - {{ substr($schedule->break_end, 0, 5) }}
                                @else
                                    <span class="text-gray-400">Немає</span>
                                @endif
                            </td>
                            <td class="p-4 border-b border-gray-200">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $schedule->is_working ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $schedule->is_working ? 'Робочий' : 'Вихідний' }}
                                </span>
                            </td>
                            <td class="p-4 border-b border-gray-200">
                                <div class="flex gap-2">
                                    <a href="{{ route('schedules.show', $schedule) }}" class="px-3 py-1 bg-purple-600 text-white rounded text-xs font-medium hover:bg-purple-700 transition no-underline">Деталі</a>
                                    <a href="{{ route('schedules.edit', $schedule) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700 transition no-underline">Редагувати</a>
                                    <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" class="inline" onsubmit="return confirm('Ви впевнені, що хочете видалити цей розклад?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700 transition">Видалити</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">Розклади не знайдено</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $schedules->links() }}
        </div>
    </div>
@endsection

