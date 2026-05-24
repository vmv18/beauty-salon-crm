@extends('layouts.app')

@section('title', 'Платежі - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li class="is-active"><a href="#" aria-current="page">Платежі</a></li>
            </ul>
        </nav>

        <div class="box">
            <div class="level mb-5">
                <div class="level-left">
                    <h1 class="title is-3 has-text-primary m-0">💰 Платежі</h1>
                </div>
                <div class="level-right">
                    <a href="{{ route('payments.create') }}" class="button is-primary">+ Додати платіж</a>
                </div>
            </div>

            @if(session('success'))
                <div class="notification is-success is-light mb-5">
                    <button class="delete" onclick="this.parentElement.remove()"></button>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="notification is-danger is-light mb-5">
                    <button class="delete" onclick="this.parentElement.remove()"></button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="box has-background-light mb-5">
                <form method="GET">
                    <div class="columns is-multiline">
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Пошук</label>
                                <div class="control">
                                    <input class="input" type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Клієнт, сума...">
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Статус</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="status" name="status">
                                            <option value="">Всі статуси</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Очікує</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершено</option>
                                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Не вдався</option>
                                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Повернено</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Спосіб оплати</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="payment_method" name="payment_method">
                                            <option value="">Всі способи</option>
                                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Готівка</option>
                                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Картка</option>
                                            <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>Онлайн</option>
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
                                            <option value="">Всі клієнти</option>
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
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">&nbsp;</label>
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth">Застосувати</button>
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
                            <th>Дата</th>
                            <th>Клієнт</th>
                            <th>Сума</th>
                            <th>Спосіб оплати</th>
                            <th>Статус</th>
                            <th>Запис</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>#{{ $payment->id }}</td>
                                <td>{{ $payment->payment_date->format('d.m.Y') }}</td>
                                <td>{{ $payment->client->user->name }}</td>
                                <td class="has-text-weight-semibold has-text-success">{{ number_format($payment->amount, 2) }} грн</td>
                                <td>{{ $payment->payment_method_name }}</td>
                                <td>
                                    <span class="tag 
                                        @if($payment->status === 'pending') is-warning
                                        @elseif($payment->status === 'completed') is-success
                                        @elseif($payment->status === 'failed') is-danger
                                        @elseif($payment->status === 'refunded') is-info
                                        @else is-dark
                                        @endif">
                                        {{ $payment->status_name }}
                                    </span>
                                </td>
                                <td>
                                    @if($payment->appointment)
                                        <a href="{{ route('appointments.show', $payment->appointment) }}" class="has-text-primary">
                                            #{{ $payment->appointment->id }}
                                        </a>
                                    @else
                                        <span class="has-text-grey">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('payments.show', $payment) }}" class="button is-small is-primary">Деталі</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="has-text-centered py-5 has-text-grey">
                                    Немає платежів
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="has-text-centered" style="margin-top: 3rem;">
                {{ $payments->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
@endsection
