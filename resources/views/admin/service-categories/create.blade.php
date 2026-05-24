@extends('layouts.app')

@section('title', 'Додати категорію - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('service-categories.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        <h1 class="text-3xl font-bold text-purple-600 mb-8">➕ Додати категорію</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('service-categories.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <label for="name" class="block mb-2 text-gray-700 font-semibold">Назва *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="description" class="block mb-2 text-gray-700 font-semibold">Опис</label>
                <textarea id="description" name="description" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm resize-y min-h-[100px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('description') }}</textarea>
                @error('description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="image" class="block mb-2 text-gray-700 font-semibold">Зображення</label>
                <input type="file" id="image" name="image" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('image')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="sort_order" class="block mb-2 text-gray-700 font-semibold">Порядок сортування</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('sort_order')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Зберегти</button>
                <a href="{{ route('service-categories.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>
@endsection

