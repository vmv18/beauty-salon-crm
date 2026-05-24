@extends('layouts.app')

@section('title', 'Панель управління - Beauty Salon CRM')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li class="is-active"><a href="#" aria-current="page">Панель управління</a></li>
            </ul>
        </nav>

        <div class="box mb-5">
            <h1 class="title is-3 has-text-primary m-0">📊 Панель управління</h1>
            <div class="content mt-4">
                <p><strong>Користувач:</strong> {{ auth()->user()->name }} | <strong>Email:</strong> {{ auth()->user()->email }}</p>
                <p><strong>Роль:</strong> 
                    @foreach(auth()->user()->roles as $role)
                        <span class="tag is-primary mr-2">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </p>
            </div>
        </div>

        <!-- Статистичні картки -->
        <div class="columns is-multiline mb-5">
            <div class="column is-3-desktop is-6-tablet">
                <div class="box">
                    <p class="heading has-text-primary">Доходи за місяць</p>
                    <p class="title is-3">{{ number_format($currentMonthRevenue, 2) }} грн</p>
                    @if($lastMonthRevenue > 0)
                        @php
                            $change = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
                        @endphp
                        <p class="is-size-7 {{ $change >= 0 ? 'has-text-success' : 'has-text-danger' }}">
                            {{ $change >= 0 ? '↑' : '↓' }} {{ number_format(abs($change), 1) }}% від минулого місяця
                        </p>
                    @endif
                </div>
            </div>

            <div class="column is-3-desktop is-6-tablet">
                <div class="box">
                    <p class="heading has-text-primary">Нові клієнти (місяць)</p>
                    <p class="title is-3">{{ $newClientsThisMonth }}</p>
                    @if($newClientsLastMonth > 0)
                        @php
                            $change = (($newClientsThisMonth - $newClientsLastMonth) / $newClientsLastMonth) * 100;
                        @endphp
                        <p class="is-size-7 {{ $change >= 0 ? 'has-text-success' : 'has-text-danger' }}">
                            {{ $change >= 0 ? '↑' : '↓' }} {{ number_format(abs($change), 1) }}% від минулого місяця
                        </p>
                    @endif
                </div>
            </div>

            <div class="column is-3-desktop is-6-tablet">
                <div class="box">
                    <p class="heading has-text-primary">Записи сьогодні</p>
                    <p class="title is-3">{{ $todayAppointments->count() }}</p>
                    <p class="is-size-7 has-text-grey">
                        {{ $todayAppointments->where('status', 'confirmed')->count() }} підтверджено
                    </p>
                </div>
            </div>

            <div class="column is-3-desktop is-6-tablet">
                <div class="box">
                    <p class="heading has-text-primary">Записи завтра</p>
                    <p class="title is-3">{{ $tomorrowAppointments->count() }}</p>
                    <p class="is-size-7 has-text-grey">
                        {{ $tomorrowAppointments->where('status', 'confirmed')->count() }} підтверджено
                    </p>
                </div>
            </div>
        </div>

        <!-- Контент з графіками та таблицями -->
        <div class="columns is-multiline">
            <!-- Графік доходів за останні 7 днів -->
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-5 has-text-primary mb-5">Доходи за останні 7 днів</h2>
                    <div style="height: 300px;">
                        <canvas id="revenueChart7Days"></canvas>
                    </div>
                </div>
            </div>

            <!-- Графік доходів за останні 30 днів -->
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-5 has-text-primary mb-5">Доходи за останні 30 днів</h2>
                    <div style="height: 300px;">
                        <canvas id="revenueChart30Days"></canvas>
                    </div>
                </div>
            </div>

            <!-- Графік по способам оплати -->
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-5 has-text-primary mb-5">Доходи по способам оплати (місяць)</h2>
                    <div style="height: 300px;">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Графік по статусах записів -->
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-5 has-text-primary mb-5">Розподіл записів по статусах</h2>
                    <div style="height: 300px;">
                        <canvas id="appointmentsStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Популярні послуги -->
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-5 has-text-primary mb-5">Популярні послуги (ТОП 5)</h2>
                    @if($popularServices->count() > 0)
                        <div class="table-container">
                            <table class="table is-fullwidth is-striped">
                                <thead>
                                    <tr>
                                        <th>Послуга</th>
                                        <th>Категорія</th>
                                        <th>Кількість записів</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($popularServices as $service)
                                        <tr>
                                            <td>{{ $service->name }}</td>
                                            <td>{{ $service->category->name }}</td>
                                            <td><strong class="has-text-primary">{{ $service->appointments_count }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="has-text-grey has-text-centered py-5">Немає даних</p>
                    @endif
                </div>
            </div>

            <!-- Зайнятість майстрів -->
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-5 has-text-primary mb-5">Зайнятість майстрів (сьогодні)</h2>
                    @if($employeeWorkload->count() > 0)
                        <div class="table-container">
                            <table class="table is-fullwidth is-striped">
                                <thead>
                                    <tr>
                                        <th>Майстер</th>
                                        <th>Кількість записів</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeWorkload as $employee)
                                        <tr>
                                            <td>{{ $employee->user->name }}</td>
                                            <td><strong class="has-text-primary">{{ $employee->appointments_count }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="has-text-grey has-text-centered py-5">Немає активних майстрів</p>
                    @endif
                </div>
            </div>

            <!-- Записи на сьогодні -->
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-5 has-text-primary mb-5">Записи на сьогодні ({{ now()->format('d.m.Y') }})</h2>
                    @if($todayAppointments->count() > 0)
                        <div class="table-container">
                            <table class="table is-fullwidth is-striped">
                                <thead>
                                    <tr>
                                        <th>Час</th>
                                        <th>Клієнт</th>
                                        <th>Послуга</th>
                                        <th>Майстер</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAppointments as $appointment)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                            <td>{{ $appointment->client->user->name }}</td>
                                            <td>{{ $appointment->service->name }}</td>
                                            <td>{{ $appointment->employee->user->name }}</td>
                                            <td>
                                                <span class="tag {{ $appointment->status === 'scheduled' ? 'is-info' : 'is-success' }}">
                                                    {{ $appointment->status === 'scheduled' ? 'Заплановано' : 'Підтверджено' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="has-text-grey has-text-centered py-5">Немає записів на сьогодні</p>
                    @endif
                </div>
            </div>

            <!-- Записи на завтра -->
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-5 has-text-primary mb-5">Записи на завтра ({{ now()->addDay()->format('d.m.Y') }})</h2>
                    @if($tomorrowAppointments->count() > 0)
                        <div class="table-container">
                            <table class="table is-fullwidth is-striped">
                                <thead>
                                    <tr>
                                        <th>Час</th>
                                        <th>Клієнт</th>
                                        <th>Послуга</th>
                                        <th>Майстер</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tomorrowAppointments as $appointment)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                            <td>{{ $appointment->client->user->name }}</td>
                                            <td>{{ $appointment->service->name }}</td>
                                            <td>{{ $appointment->employee->user->name }}</td>
                                            <td>
                                                <span class="tag {{ $appointment->status === 'scheduled' ? 'is-info' : 'is-success' }}">
                                                    {{ $appointment->status === 'scheduled' ? 'Заплановано' : 'Підтверджено' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="has-text-grey has-text-centered py-5">Немає записів на завтра</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Графік доходів за 7 днів
        const revenue7DaysCtx = document.getElementById('revenueChart7Days').getContext('2d');
        new Chart(revenue7DaysCtx, {
            type: 'line',
            data: {
                labels: @json($revenueLast7DaysDates),
                datasets: [{
                    label: 'Доходи (грн)',
                    data: @json($revenueLast7DaysAmounts),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' грн';
                            }
                        }
                    }
                }
            }
        });

        // Графік доходів за 30 днів
        const revenue30DaysCtx = document.getElementById('revenueChart30Days').getContext('2d');
        new Chart(revenue30DaysCtx, {
            type: 'bar',
            data: {
                labels: @json($revenueLast30DaysDates),
                datasets: [{
                    label: 'Доходи (грн)',
                    data: @json($revenueLast30DaysAmounts),
                    backgroundColor: 'rgba(102, 126, 234, 0.6)',
                    borderColor: '#667eea',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' грн';
                            }
                        }
                    }
                }
            }
        });

        // Графік по способам оплати
        const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        new Chart(paymentMethodsCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($paymentMethodsStats->toArray())),
                datasets: [{
                    data: @json(array_values($paymentMethodsStats->toArray())),
                    backgroundColor: [
                        '#667eea',
                        '#28a745',
                        '#ffc107'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(2) + ' грн';
                            }
                        }
                    }
                }
            }
        });

        // Графік по статусах записів
        const appointmentsStatusCtx = document.getElementById('appointmentsStatusChart').getContext('2d');
        new Chart(appointmentsStatusCtx, {
            type: 'pie',
            data: {
                labels: @json(array_keys($appointmentsByStatus->toArray())),
                datasets: [{
                    data: @json(array_values($appointmentsByStatus->toArray())),
                    backgroundColor: [
                        '#667eea',
                        '#28a745',
                        '#17a2b8',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection
