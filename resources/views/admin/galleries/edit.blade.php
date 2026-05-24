@extends('layouts.app')

@section('title', 'Редагувати зображення - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('galleries.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        <h1 class="text-3xl font-bold text-purple-600 mb-8">✏️ Редагувати зображення: {{ $gallery->title }}</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('galleries.update', $gallery) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-6">
                <label for="title" class="block mb-2 text-gray-700 font-medium">Назва *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $gallery->title) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('title')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="description" class="block mb-2 text-gray-700 font-medium">Опис</label>
                <textarea id="description" name="description" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm resize-y min-h-[100px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('description', $gallery->description) }}</textarea>
                @error('description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="image" class="block mb-2 text-gray-700 font-medium">Зображення</label>
                @if($gallery->image)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Поточне зображення:</p>
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($gallery->image) }}" alt="{{ $gallery->title }}" class="max-w-[300px] rounded-lg border border-gray-300">
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                <p class="text-sm text-gray-500 mt-2">Залиште порожнім, щоб залишити поточне зображення. Максимальний розмір: 5MB</p>
                @error('image')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="sort_order" class="block mb-2 text-gray-700 font-medium">Порядок сортування</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $gallery->sort_order) }}" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                <p class="text-sm text-gray-500 mt-2">Менше значення = вище в списку</p>
                @error('sort_order')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $gallery->is_active) ? 'checked' : '' }} class="rounded">
                    <span class="text-gray-700 font-medium">Активне зображення</span>
                </label>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Оновити</button>
                <a href="{{ route('galleries.show', $gallery) }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>
@endsection

