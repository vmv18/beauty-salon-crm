@extends('layouts.app')

@section('title', 'Деталі розкладу - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('schedules.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📅 Деталі розкладу #{{ $schedule->id }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('schedules.edit', $schedule) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Редагувати</a>
                <a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Основна інформація</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Майстер</div>
                    <div class="text-gray-900">{{ $schedule->employee->user->name ?? 'N/A' }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">День тижня</div>
                    <div class="text-gray-900">{{ $schedule->day_name }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Статус</div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $schedule->is_working ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $schedule->is_working ? 'Робочий день' : 'Вихідний день' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($schedule->is_working)
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Робочі години</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Час роботи</div>
                    <div class="text-gray-900">
                        @if($schedule->start_time && $schedule->end_time)
                            {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                        @else
                            <span class="text-gray-400">Не вказано</span>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Перерва</div>
                    <div class="text-gray-900">
                        @if($schedule->break_start && $schedule->break_end)
                            {{ substr($schedule->break_start, 0, 5) }} - {{ substr($schedule->break_end, 0, 5) }}
                        @else
                            <span class="text-gray-400">Немає перерви</span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

