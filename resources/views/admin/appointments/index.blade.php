@extends('layouts.app')

@section('title', 'Записи - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li class="is-active"><a href="#" aria-current="page">Записи</a></li>
            </ul>
        </nav>

        <div class="box">
            <div class="level mb-5">
                <div class="level-left">
                    <h1 class="title is-3 has-text-primary m-0">📅 Управління записами</h1>
                </div>
                <div class="level-right">
                    <div class="buttons">
                        <a href="{{ route('appointments.create') }}" class="button is-primary">+ Додати запис</a>
                        <a href="{{ route('appointments.calendar') }}" class="button is-info">📅 Календар</a>
                    </div>
                </div>
            </div>

            <div class="box has-background-light mb-5">
                <form method="GET" action="{{ route('appointments.index') }}">
                    <div class="columns is-multiline">
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Пошук</label>
                                <div class="control">
                                    <input class="input" type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Клієнт, майстер, послуга...">
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Статус</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="status" name="status">
                                            <option value="">Всі</option>
                                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Заплановано</option>
                                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Підтверджено</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершено</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Скасовано</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Дата</label>
                                <div class="control">
                                    <input class="input" type="date" id="date" name="date" value="{{ request('date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Майстер</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="employee_id" name="employee_id">
                                            <option value="">Всі</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Клієнт</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="client_id" name="client_id">
                                            <option value="">Всі</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">&nbsp;</label>
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth">Фільтрувати</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Клієнт</th>
                            <th>Майстер</th>
                            <th>Послуга</th>
                            <th>Дата</th>
                            <th>Час</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('appointments.show', $appointment) }}'">
                                <td>{{ $appointment->id }}</td>
                                <td>{{ $appointment->client->user->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->employee->user->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->service->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                                <td>{{ $appointment->appointment_time ? substr($appointment->appointment_time, 0, 5) : '' }}</td>
                                <td>
                                    <span class="tag 
                                        @if($appointment->status === 'scheduled') is-info
                                        @elseif($appointment->status === 'confirmed') is-success
                                        @elseif($appointment->status === 'completed') is-success
                                        @else is-danger
                                        @endif">
                                        @if($appointment->status === 'scheduled') Заплановано
                                        @elseif($appointment->status === 'confirmed') Підтверджено
                                        @elseif($appointment->status === 'completed') Завершено
                                        @else Скасовано
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="buttons" onclick="event.stopPropagation()">
                                        <a href="{{ route('appointments.show', $appointment) }}" class="button is-small is-primary">Деталі</a>
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="button is-small is-info">Редагувати</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="has-text-centered py-5">Записи не знайдено</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $appointments->links() }}
            </div>
        </div>
    </div>
@endsection
