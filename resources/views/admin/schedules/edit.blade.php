@extends('layouts.app')

@section('title', 'Редагувати розклад - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('schedules.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <h1 class="text-3xl font-bold text-purple-600 mb-8">✏️ Редагувати розклад #{{ $schedule->id }}</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('schedules.update', $schedule) }}">
            @csrf
            @method('PUT')

            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <div class="mb-2">
                    <div class="font-semibold text-gray-700">Майстер</div>
                    <div class="text-gray-900">{{ $schedule->employee->user->name ?? 'N/A' }}</div>
                </div>
                <div class="mb-2">
                    <div class="font-semibold text-gray-700">День тижня</div>
                    <div class="text-gray-900">{{ $schedule->day_name }}</div>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" id="is_working" name="is_working" value="1" {{ old('is_working', $schedule->is_working) ? 'checked' : '' }} class="mr-2 rounded">
                    <span class="text-gray-700 font-medium">Робочий день</span>
                </label>
                @error('is_working')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div id="working-hours" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_time" class="block mb-2 text-gray-700 font-medium">Час початку роботи</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time ? substr($schedule->start_time, 0, 5) : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    @error('start_time')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block mb-2 text-gray-700 font-medium">Час закінчення роботи</label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time ? substr($schedule->end_time, 0, 5) : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    @error('end_time')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="break-hours" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="break_start" class="block mb-2 text-gray-700 font-medium">Час початку перерви</label>
                    <input type="time" id="break_start" name="break_start" value="{{ old('break_start', $schedule->break_start ? substr($schedule->break_start, 0, 5) : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    @error('break_start')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="break_end" class="block mb-2 text-gray-700 font-medium">Час закінчення перерви</label>
                    <input type="time" id="break_end" name="break_end" value="{{ old('break_end', $schedule->break_end ? substr($schedule->break_end, 0, 5) : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    @error('break_end')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Оновити розклад</button>
                <a href="{{ route('schedules.show', $schedule) }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('is_working').addEventListener('change', function() {
            const workingHours = document.getElementById('working-hours');
            const breakHours = document.getElementById('break-hours');
            if (this.checked) {
                workingHours.style.display = 'grid';
                breakHours.style.display = 'grid';
            } else {
                workingHours.style.display = 'none';
                breakHours.style.display = 'none';
            }
        });
        
        // Перевірити початковий стан
        if (!document.getElementById('is_working').checked) {
            document.getElementById('working-hours').style.display = 'none';
            document.getElementById('break-hours').style.display = 'none';
        }
    </script>
@endsection

