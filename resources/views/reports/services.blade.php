@extends('layouts.app')

@section('title', 'Звіт по послугах - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">💅 Звіт по послугах (популярність)</h1>
            <div class="flex gap-2">
                <a href="{{ route('reports.services', ['format' => 'pdf']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Експорт PDF</a>
                <a href="{{ route('reports.services', ['format' => 'csv']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Експорт CSV</a>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">#</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Послуга</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Категорія</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Ціна</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Кількість записів</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Доходи</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $index => $service)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 border-b border-gray-200">{{ $index + 1 }}</td>
                            <td class="p-4 border-b border-gray-200">
                                <a href="{{ route('services.show', $service) }}" class="text-purple-600 no-underline hover:underline">
                                    {{ $service->name }}
                                </a>
                            </td>
                            <td class="p-4 border-b border-gray-200">{{ $service->category->name }}</td>
                            <td class="p-4 border-b border-gray-200">{{ number_format($service->price, 2) }} грн</td>
                            <td class="p-4 border-b border-gray-200 font-semibold text-purple-600">{{ $service->appointments_count }}</td>
                            <td class="p-4 border-b border-gray-200 font-semibold text-green-600">{{ number_format($service->revenue, 2) }} грн</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-8 text-gray-600">
                                Немає послуг
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

