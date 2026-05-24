@extends('layouts.app')

@section('title', 'Редагувати послугу - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('services.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        <h1 class="text-3xl font-bold text-purple-600 mb-8">✏️ Редагувати послугу: {{ $service->name }}</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-6">
                <label for="category_id" class="block mb-2 text-gray-700 font-medium">Категорія *</label>
                <select id="category_id" name="category_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="">Оберіть категорію</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="name" class="block mb-2 text-gray-700 font-medium">Назва *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $service->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="description" class="block mb-2 text-gray-700 font-medium">Опис</label>
                <textarea id="description" name="description" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm resize-y min-h-[100px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('description', $service->description) }}</textarea>
                @error('description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="duration" class="block mb-2 text-gray-700 font-medium">Тривалість (хвилини) *</label>
                <input type="number" id="duration" name="duration" value="{{ old('duration', $service->duration) }}" min="1" max="480" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('duration')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="price" class="block mb-2 text-gray-700 font-medium">Ціна (грн) *</label>
                <input type="number" id="price" name="price" value="{{ old('price', $service->price) }}" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('price')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="image" class="block mb-2 text-gray-700 font-medium">Зображення</label>
                @if($service->image)
                    <div class="mb-2"><img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" class="max-w-[200px] mt-2 rounded-lg"></div>
                @endif
                <input type="file" id="image" name="image" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('image')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }} class="rounded">
                    <span class="text-gray-700 font-medium">Активна послуга</span>
                </label>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Оновити</button>
                <a href="{{ route('services.show', $service) }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>
@endsection

