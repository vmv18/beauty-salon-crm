@extends('layouts.app')

@section('title', 'Категорії послуг - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('admin.dashboard') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до панелі</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📁 Категорії послуг</h1>
            <a href="{{ route('service-categories.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">+ Додати категорію</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-300">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">ID</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Зображення</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Назва</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Опис</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Послуг</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Порядок</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 border-b border-gray-200">{{ $category->id }}</td>
                            <td class="p-4 border-b border-gray-200">
                                <img src="{{ \App\Helpers\ImageHelper::getCategoryImage($category) }}" alt="{{ $category->name }}" class="w-15 h-15 object-cover rounded-lg">
                            </td>
                            <td class="p-4 border-b border-gray-200">{{ $category->name }}</td>
                            <td class="p-4 border-b border-gray-200">{{ Str::limit($category->description, 50) }}</td>
                            <td class="p-4 border-b border-gray-200">{{ $category->services_count }}</td>
                            <td class="p-4 border-b border-gray-200">{{ $category->sort_order }}</td>
                            <td class="p-4 border-b border-gray-200">
                                <div class="flex gap-2">
                                    <a href="{{ route('service-categories.show', $category) }}" class="px-3 py-1 bg-green-600 text-white rounded text-xs font-medium hover:bg-green-700 transition no-underline">Переглянути</a>
                                    <a href="{{ route('service-categories.edit', $category) }}" class="px-3 py-1 bg-purple-600 text-white rounded text-xs font-medium hover:bg-purple-700 transition no-underline">Редагувати</a>
                                    <form method="POST" action="{{ route('service-categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Видалити категорію?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700 transition">Видалити</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-8 text-gray-600">Категорій не знайдено</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="has-text-centered" style="margin-top: 3rem;">{{ $categories->links('vendor.pagination.custom') }}</div>
        @endif
    </div>
@endsection

