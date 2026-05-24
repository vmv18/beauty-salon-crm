@extends('layouts.app')

@section('title', 'Категорія: ' . $serviceCategory->name . ' - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('service-categories.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📁 {{ $serviceCategory->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('service-categories.edit', $serviceCategory) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Редагувати</a>
                <a href="{{ route('service-categories.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-300">{{ session('success') }}</div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Назва</div>
                <div class="text-gray-900">{{ $serviceCategory->name }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Опис</div>
                <div class="text-gray-900">{{ $serviceCategory->description ?? 'Немає опису' }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-2">Зображення</div>
                <img src="{{ \App\Helpers\ImageHelper::getCategoryImage($serviceCategory) }}" alt="{{ $serviceCategory->name }}" class="max-w-[300px] rounded-lg">
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Порядок сортування</div>
                <div class="text-gray-900">{{ $serviceCategory->sort_order }}</div>
            </div>
            <div class="mb-3">
                <div class="font-semibold text-gray-700 mb-1">Кількість послуг</div>
                <div class="text-gray-900">{{ $serviceCategory->services->count() }}</div>
            </div>
        </div>

        @if($serviceCategory->services->count() > 0)
            <h2 class="text-2xl font-semibold text-purple-600 mb-4">Послуги в цій категорії</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Назва</th>
                            <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Ціна</th>
                            <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Тривалість</th>
                            <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($serviceCategory->services as $service)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 border-b border-gray-200 text-gray-900">{{ $service->name }}</td>
                                <td class="p-4 border-b border-gray-200 text-gray-900">{{ number_format($service->price, 2) }} грн</td>
                                <td class="p-4 border-b border-gray-200 text-gray-900">{{ $service->duration }} хв</td>
                                <td class="p-4 border-b border-gray-200">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $service->is_active ? 'Активна' : 'Неактивна' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

