@extends('layouts.app')

@section('title', 'Звіт по майстрах - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">👨‍💼 Звіт по майстрах (продуктивність)</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.employees', ['format' => 'pdf']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Експорт PDF</a>
                <a href="{{ route('admin.reports.employees', ['format' => 'csv']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Експорт CSV</a>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">#</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Майстер</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Спеціалізація</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Рейтинг</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Кількість записів</th>
                        <th class="p-4 text-left border-b border-gray-200 bg-gray-50 font-semibold text-gray-900">Доходи</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 border-b border-gray-200">{{ $index + 1 }}</td>
                            <td class="p-4 border-b border-gray-200">
                                <a href="{{ route('employees.show', $employee) }}" class="text-purple-600 no-underline hover:underline">
                                    {{ $employee->user->name }}
                                </a>
                            </td>
                            <td class="p-4 border-b border-gray-200">{{ $employee->specialization ?? '—' }}</td>
                            <td class="p-4 border-b border-gray-200">{{ number_format($employee->rating, 1) }} ⭐</td>
                            <td class="p-4 border-b border-gray-200 font-semibold text-purple-600">{{ $employee->appointments_count }}</td>
                            <td class="p-4 border-b border-gray-200 font-semibold text-green-600">{{ number_format($employee->revenue, 2) }} грн</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-8 text-gray-600">
                                Немає майстрів
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

