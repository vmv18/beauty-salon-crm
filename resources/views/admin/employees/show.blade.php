@extends('layouts.app')

@section('title', 'Майстер: ' . ($employee->user->name ?? 'N/A') . ' - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('employees.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">👨‍💼 {{ $employee->user->name ?? 'N/A' }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Редагувати</a>
                <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-300">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Основна інформація</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Ім'я</div>
                    <div class="text-gray-900">{{ $employee->user->name ?? 'N/A' }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Email</div>
                    <div class="text-gray-900">{{ $employee->user->email ?? 'N/A' }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Спеціалізація</div>
                    <div class="text-gray-900">{{ $employee->specialization ?? 'N/A' }}</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Рейтинг</div>
                    <div class="text-yellow-600 font-semibold text-xl">⭐ {{ number_format($employee->rating, 1) }}/5.0</div>
                </div>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Статус</div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($employee->status == 'active') bg-green-100 text-green-800
                            @elseif($employee->status == 'inactive') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            @if($employee->status == 'active') Активний
                            @elseif($employee->status == 'inactive') Неактивний
                            @else У відпустці
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Додаткова інформація</h3>
                <div class="mb-3">
                    <div class="font-semibold text-gray-700 mb-1">Дата найму</div>
                    <div class="text-gray-900">{{ $employee->hire_date ? $employee->hire_date->format('d.m.Y') : 'N/A' }}</div>
                </div>
                <div class="mt-4">
                    <div class="font-semibold text-gray-700 mb-2">Фото</div>
                    <img src="{{ \App\Helpers\ImageHelper::getEmployeePhoto($employee) }}" alt="{{ $employee->user->name }}" class="max-w-[300px] rounded-lg mt-2">
                </div>
            </div>
        </div>

        @if($employee->bio)
            <div class="bg-gray-50 p-6 rounded-lg mb-8">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Біографія</h3>
                <div class="text-gray-900 whitespace-pre-wrap">{{ $employee->bio }}</div>
            </div>
        @endif

        @if($employee->services->count() > 0)
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">Послуги ({{ $employee->services->count() }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    @foreach($employee->services as $service)
                        <div class="p-4 bg-white rounded-lg border border-gray-300">
                            <div class="font-semibold text-purple-600">{{ $service->name }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ $service->category->name }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ number_format($service->price, 0) }} грн / {{ $service->duration }} хв</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

