@extends('layouts.app')

@section('title', 'Панель майстра - Beauty Salon CRM')

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li class="is-active"><a href="#" aria-current="page">Панель майстра</a></li>
            </ul>
        </nav>

        <div class="box mb-5">
            <h1 class="title is-3 has-text-primary m-0">✂️ Панель майстра</h1>
            <div class="content mt-4">
                <p><strong>Користувач:</strong> {{ $user->name }} | <strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Роль:</strong> 
                    @foreach($user->roles as $role)
                        <span class="tag is-primary mr-2">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </p>
            </div>
        </div>

        <div class="box mb-5">
            <div class="level mb-5">
                <div class="level-left">
                    <h2 class="title is-4 m-0">Мої записи</h2>
                </div>
                <div class="level-right">
                    <a href="{{ route('master.appointments.index') }}" class="button is-primary">Всі записи</a>
                </div>
            </div>
            @php
                $employee = $user->employee;
                $appointments = $employee ? $employee->appointments()
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->with(['service', 'client.user'])
                    ->where('appointment_date', '>=', now()->toDateString())
                    ->orderBy('appointment_date', 'asc')
                    ->orderBy('appointment_time', 'asc')
                    ->limit(10)
                    ->get() : collect();
            @endphp

            @if($appointments->count() > 0)
                <div class="table-container">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>Клієнт</th>
                                <th>Послуга</th>
                                <th>Дата</th>
                                <th>Час</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('master.appointments.show', $appointment) }}'">
                                    <td>{{ $appointment->client->user->name }}</td>
                                    <td>{{ $appointment->service->name }}</td>
                                    <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                    <td>
                                        <span class="tag 
                                            @if($appointment->status === 'scheduled') is-info
                                            @elseif($appointment->status === 'confirmed') is-success
                                            @endif">
                                            @if($appointment->status === 'scheduled') Заплановано
                                            @elseif($appointment->status === 'confirmed') Підтверджено
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="has-text-grey">У вас поки немає майбутніх записів.</p>
            @endif
        </div>
    </div>
@endsection
