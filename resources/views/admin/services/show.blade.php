@extends('layouts.app')

@section('title', 'Послуга: ' . $service->name . ' - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('services.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">💅 {{ $service->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('services.edit', $service) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Редагувати</a>
                <a href="{{ route('services.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-300">{{ session('success') }}</div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg">
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Назва</div>
                <div class="text-gray-900">{{ $service->name }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Категорія</div>
                <div class="text-gray-900">{{ $service->category->name }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Опис</div>
                <div class="text-gray-900">{{ $service->description ?? 'Немає опису' }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Ціна</div>
                <div class="text-gray-900">{{ number_format($service->price, 2) }} грн</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Тривалість</div>
                <div class="text-gray-900">{{ $service->duration }} хвилин</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Статус</div>
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $service->is_active ? 'Активна' : 'Неактивна' }}
                    </span>
                </div>
            </div>
            @if($service->image)
                <div class="mt-4">
                    <div class="font-semibold text-gray-700 mb-2">Зображення</div>
                    <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" class="max-w-[300px] rounded-lg mt-2">
                </div>
            @endif
        </div>
    </div>
@endsection

