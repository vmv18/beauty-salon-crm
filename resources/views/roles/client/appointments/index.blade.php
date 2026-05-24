@extends('layouts.app')

@section('title', 'Мої записи - Beauty Salon CRM')

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('client.dashboard') }}">Кабінет</a></li>
                <li class="is-active"><a href="#" aria-current="page">Мої записи</a></li>
            </ul>
        </nav>

        <div class="box">
            <h1 class="title is-3 has-text-primary mb-5">📅 Мої записи</h1>

            <!-- Фільтри -->
            <div class="box has-background-light mb-5">
                <form method="GET" action="{{ route('client.appointments.index') }}">
                    <div class="columns is-multiline">
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Статус</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="status">
                                            <option value="">Всі статуси</option>
                                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Заплановано</option>
                                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Підтверджено</option>
                                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Виконано</option>
                                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Скасовано</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Дата</label>
                                <div class="control">
                                    <input class="input" type="date" name="date" value="{{ request('date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="field is-grouped" style="align-items: flex-end;">
                                <div class="control">
                                    <button type="submit" class="button is-primary">Фільтрувати</button>
                                </div>
                                <div class="control">
                                    <a href="{{ route('client.appointments.index') }}" class="button is-light">Скинути</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if($appointments->count() > 0)
                <div class="table-container">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>Майстер</th>
                                <th>Послуга</th>
                                <th>Дата</th>
                                <th>Час</th>
                                <th>Статус</th>
                                <th>Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->employee->user->name }}</td>
                                    <td>{{ $appointment->service->name }}</td>
                                    <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                    <td>
                                        <span class="tag 
                                            @if($appointment->status === 'scheduled') is-info
                                            @elseif($appointment->status === 'confirmed') is-success
                                            @elseif($appointment->status === 'completed') is-success
                                            @else is-danger
                                            @endif">
                                            @if($appointment->status === 'scheduled') Заплановано
                                            @elseif($appointment->status === 'confirmed') Підтверджено
                                            @elseif($appointment->status === 'completed') Виконано
                                            @else Скасовано
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('client.appointments.show', $appointment) }}" class="button is-primary is-small">Деталі</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $appointments->links() }}
                </div>
            @else
                <div class="notification is-light has-text-centered">
                    <p>Записів не знайдено.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

