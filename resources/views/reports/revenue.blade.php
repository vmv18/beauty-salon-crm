@extends('layouts.app')

@section('title', $title . ' - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">{{ $title }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.revenue', ['period' => $period, 'format' => 'pdf']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Експорт PDF</a>
                <a href="{{ route('admin.reports.revenue', ['period' => $period, 'format' => 'csv']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Експорт CSV</a>
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        <div class="mb-6 flex gap-2 flex-wrap">
            <a href="{{ route('admin.reports.revenue', ['period' => 'day']) }}" class="px-4 py-2 rounded-lg no-underline text-sm font-medium transition {{ $period === 'day' ? 'bg-purple-600 text-white hover:bg-purple-700' : 'bg-gray-600 text-white hover:bg-gray-700' }}">День</a>
            <a href="{{ route('admin.reports.revenue', ['period' => 'week']) }}" class="px-4 py-2 rounded-lg no-underline text-sm font-medium transition {{ $period === 'week' ? 'bg-purple-600 text-white hover:bg-purple-700' : 'bg-gray-600 text-white hover:bg-gray-700' }}">Тиждень</a>
            <a href="{{ route('admin.reports.revenue', ['period' => 'month']) }}" class="px-4 py-2 rounded-lg no-underline text-sm font-medium transition {{ $period === 'month' ? 'bg-purple-600 text-white hover:bg-purple-700' : 'bg-gray-600 text-white hover:bg-gray-700' }}">Місяць</a>
            <a href="{{ route('admin.reports.revenue', ['period' => 'year']) }}" class="px-4 py-2 rounded-lg no-underline text-sm font-medium transition {{ $period === 'year' ? 'bg-purple-600 text-white hover:bg-purple-700' : 'bg-gray-600 text-white hover:bg-gray-700' }}">Рік</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-sm font-semibold text-purple-600 mb-2">Загальна сума</h3>
                <div class="text-2xl font-semibold text-gray-900">{{ number_format($totalRevenue, 2) }} грн</div>
            </div>
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-sm font-semibold text-purple-600 mb-2">Кількість платежів</h3>
                <div class="text-2xl font-semibold text-gray-900">{{ $payments->count() }}</div>
            </div>
            @foreach($byPaymentMethod as $method => $data)
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-sm font-semibold text-purple-600 mb-2">{{ $method === 'cash' ? 'Готівка' : ($method === 'card' ? 'Картка' : 'Онлайн') }}</h3>
                    <div class="text-2xl font-semibold text-gray-900 mb-1">{{ number_format($data['total'], 2) }} грн</div>
                    <div class="text-sm text-gray-600">{{ $data['count'] }} платежів</div>
                </div>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Дата</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Клієнт</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Сума</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Спосіб оплати</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Запис</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 border-b border-gray-200">{{ $payment->payment_date->format('d.m.Y') }}</td>
                            <td class="p-4 border-b border-gray-200">{{ $payment->client->user->name }}</td>
                            <td class="p-4 border-b border-gray-200 font-semibold">{{ number_format($payment->amount, 2) }} грн</td>
                            <td class="p-4 border-b border-gray-200">{{ $payment->payment_method_name }}</td>
                            <td class="p-4 border-b border-gray-200">
                                @if($payment->appointment)
                                    <a href="{{ route('appointments.show', $payment->appointment) }}" class="text-purple-600 no-underline hover:underline">
                                        #{{ $payment->appointment->id }} - {{ $payment->appointment->service->name }}
                                    </a>
                                @else
                                    <span class="text-gray-500">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-8 text-gray-600">
                                Немає платежів за вибраний період
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

