@extends('layouts.app')

@section('title', 'Зображення: ' . $gallery->title . ' - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('galleries.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📸 {{ $gallery->title }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('galleries.edit', $gallery) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Редагувати</a>
                <a href="{{ route('galleries.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-300">{{ session('success') }}</div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <div class="mb-4">
                <div class="font-semibold text-gray-700 mb-1">Назва</div>
                <div class="text-gray-900 text-lg">{{ $gallery->title }}</div>
            </div>
            @if($gallery->description)
                <div class="mb-4">
                    <div class="font-semibold text-gray-700 mb-1">Опис</div>
                    <div class="text-gray-900">{{ $gallery->description }}</div>
                </div>
            @endif
            <div class="mb-4">
                <div class="font-semibold text-gray-700 mb-1">Порядок сортування</div>
                <div class="text-gray-900">{{ $gallery->sort_order }}</div>
            </div>
            <div class="mb-4">
                <div class="font-semibold text-gray-700 mb-1">Статус</div>
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $gallery->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $gallery->is_active ? 'Активне' : 'Неактивне' }}
                    </span>
                </div>
            </div>
            <div class="mb-4">
                <div class="font-semibold text-gray-700 mb-1">Дата створення</div>
                <div class="text-gray-900">{{ $gallery->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>

        @if($gallery->image)
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="font-semibold text-gray-700 mb-4">Зображення</div>
                <img src="{{ \Illuminate\Support\Facades\Storage::url($gallery->image) }}" alt="{{ $gallery->title }}" class="max-w-full rounded-lg shadow-md">
            </div>
        @endif
    </div>
@endsection

