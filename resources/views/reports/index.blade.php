@extends('layouts.app')

@section('title', 'Звіти - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-6xl mx-auto">
        <a href="{{ route('admin.dashboard') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до панелі</a>
        
        <div class="bg-white p-8 rounded-xl shadow-md mb-8">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📊 Звіти</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold text-purple-600 mb-4">💰 Звіт по доходах</h2>
                <p class="text-gray-600 mb-6">Детальний звіт по доходах за вибраний період (день, тиждень, місяць, рік)</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.reports.revenue', ['period' => 'day']) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">За день</a>
                    <a href="{{ route('admin.reports.revenue', ['period' => 'week']) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">За тиждень</a>
                    <a href="{{ route('admin.reports.revenue', ['period' => 'month']) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">За місяць</a>
                    <a href="{{ route('admin.reports.revenue', ['period' => 'year']) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">За рік</a>
                </div>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold text-purple-600 mb-4">👥 Звіт по клієнтах</h2>
                <p class="text-gray-600 mb-6">Найбільш активні клієнти з кількістю записів та сумою оплат</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.reports.clients') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Переглянути</a>
                    <a href="{{ route('admin.reports.clients', ['format' => 'pdf']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">PDF</a>
                    <a href="{{ route('admin.reports.clients', ['format' => 'csv']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">CSV</a>
                </div>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold text-purple-600 mb-4">👨‍💼 Звіт по майстрах</h2>
                <p class="text-gray-600 mb-6">Продуктивність майстрів: кількість записів та доходи</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.reports.employees') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Переглянути</a>
                    <a href="{{ route('admin.reports.employees', ['format' => 'pdf']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">PDF</a>
                    <a href="{{ route('admin.reports.employees', ['format' => 'csv']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">CSV</a>
                </div>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold text-purple-600 mb-4">💅 Звіт по послугах</h2>
                <p class="text-gray-600 mb-6">Популярність послуг та доходи від кожної послуги</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.reports.services') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Переглянути</a>
                    <a href="{{ route('admin.reports.services', ['format' => 'pdf']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">PDF</a>
                    <a href="{{ route('admin.reports.services', ['format' => 'csv']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">CSV</a>
                </div>
            </div>
        </div>
    </div>
@endsection

