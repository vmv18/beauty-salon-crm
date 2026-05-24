@extends('layouts.public')

@section('title', 'Бронювання успішно - Beauty Salon')

@push('styles')
@endpush

@section('content')
    <section class="section">
        <div class="container">
            <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
                <ul>
                    <li><a href="{{ route('landing') }}">Головна</a></li>
                    <li><a href="{{ route('public.booking.create') }}">Онлайн бронювання</a></li>
                    <li class="is-active"><a href="#" aria-current="page">Успішно</a></li>
                </ul>
            </nav>

            <div class="columns is-centered">
                <div class="column is-8">
                    <div class="box has-text-centered">
                        <div class="is-size-1 mb-4 has-text-success">✅</div>
                        <h1 class="title is-3 has-text-primary mb-5">Бронювання успішно створено!</h1>
                        
                        @if(session('success'))
                            <div class="notification is-success is-light mb-5">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                <strong>Успіх!</strong>
                                <p>{{ session('success') }}</p>
                            </div>
                        @endif

                        <div class="box has-background-light mb-5">
                            <h3 class="title is-5 has-text-primary mb-5">Деталі запису</h3>
                            <div class="content">
                                <table class="table is-fullwidth">
                                    <tbody>
                                        <tr>
                                            <td class="has-text-weight-semibold">Клієнт:</td>
                                            <td>{{ $appointment->client->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="has-text-weight-semibold">Послуга:</td>
                                            <td>{{ $appointment->service->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="has-text-weight-semibold">Майстер:</td>
                                            <td>{{ $appointment->employee->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="has-text-weight-semibold">Дата:</td>
                                            <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="has-text-weight-semibold">Час:</td>
                                            <td>{{ $appointment->appointment_time ? substr($appointment->appointment_time, 0, 5) : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="has-text-weight-semibold">Тривалість:</td>
                                            <td>{{ $appointment->duration }} хвилин</td>
                                        </tr>
                                        <tr>
                                            <td class="has-text-weight-semibold">Ціна:</td>
                                            <td>{{ number_format($appointment->price, 0) }} грн</td>
                                        </tr>
                                        <tr>
                                            <td class="has-text-weight-semibold">Статус:</td>
                                            <td>
                                                <span class="tag 
                                                    @if($appointment->status === 'scheduled') is-info
                                                    @elseif($appointment->status === 'confirmed') is-success
                                                    @elseif($appointment->status === 'completed') is-dark
                                                    @else is-danger
                                                    @endif">
                                                    @if($appointment->status === 'scheduled')
                                                        Заплановано
                                                    @elseif($appointment->status === 'confirmed')
                                                        Підтверджено
                                                    @elseif($appointment->status === 'completed')
                                                        Виконано
                                                    @else
                                                        Скасовано
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                        @if($appointment->notes)
                                        <tr>
                                            <td class="has-text-weight-semibold">Примітки:</td>
                                            <td>{{ $appointment->notes }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="notification is-info is-light mb-5">
                            <strong>Що далі?</strong>
                            <ul class="mt-3">
                                <li>• На вашу email адресу (<strong>{{ $appointment->client->user->email }}</strong>) відправлено підтвердження запису</li>
                                <li>• Ми зв'яжемося з вами для підтвердження запису</li>
                                <li>• Ви можете переглянути деталі запису вище</li>
                            </ul>
                        </div>

                        <div class="buttons is-centered">
                            <a href="{{ route('public.booking.create') }}" class="button is-primary is-medium">
                                Забронювати ще один запис
                            </a>
                            <a href="{{ route('public.services') }}" class="button is-dark is-medium">
                                Переглянути послуги
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
