@extends('layouts.app')

@section('title', 'Особистий кабінет - Beauty Salon CRM')

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li class="is-active"><a href="#" aria-current="page">Особистий кабінет</a></li>
            </ul>
        </nav>

        @php
            $client = $user->client;
        @endphp

        @if($client)
            <div class="columns is-multiline mb-5">
                <!-- Інформація про акаунт -->
                <div class="column is-8">
                    <div class="box">
                        <h1 class="title is-3 has-text-primary mb-5">👤 Особистий кабінет</h1>
                        
                        <div class="media mb-5">
                            @if($client->photo)
                                <figure class="media-left">
                                    <p class="image is-128x128">
                                        <img class="is-rounded" src="{{ \Illuminate\Support\Facades\Storage::url($client->photo) }}" alt="{{ $user->name }}">
                                    </p>
                                </figure>
                            @else
                                <figure class="media-left">
                                    <div class="image is-128x128 has-background-primary has-text-white is-flex is-align-items-center is-justify-content-center is-rounded" style="font-size: 3rem;">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                </figure>
                            @endif
                            <div class="media-content">
                                <h2 class="title is-4 mb-2">{{ $user->name }}</h2>
                                <p class="subtitle is-6 has-text-grey">{{ $user->email }}</p>
                                @if($client->loyalty_level)
                                    <span class="tag is-large 
                                        @if($client->loyalty_level === 'gold') is-warning
                                        @elseif($client->loyalty_level === 'silver') is-light
                                        @else is-dark
                                        @endif">
                                        {{ $client->loyalty_level_name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="content">
                            <div class="columns is-multiline">
                                <div class="column is-half">
                                    <p><strong>📧 Email:</strong><br>{{ $user->email }}</p>
                                </div>
                                @if($client->phone)
                                    <div class="column is-half">
                                        <p><strong>📞 Телефон:</strong><br>{{ $client->phone }}</p>
                                    </div>
                                @endif
                                @if($client->date_of_birth)
                                    <div class="column is-half">
                                        <p><strong>🎂 Дата народження:</strong><br>{{ $client->date_of_birth->format('d.m.Y') }}</p>
                                    </div>
                                @endif
                                @if($client->gender)
                                    <div class="column is-half">
                                        <p><strong>👤 Стать:</strong><br>
                                            @if($client->gender === 'male') Чоловік
                                            @elseif($client->gender === 'female') Жінка
                                            @else Інше
                                            @endif
                                        </p>
                                    </div>
                                @endif
                                @if($client->address)
                                    <div class="column is-full">
                                        <p><strong>📍 Адреса:</strong><br>{{ $client->address }}</p>
                                    </div>
                                @endif
                                <div class="column is-full">
                                    <p><strong>Роль:</strong> 
                                        @foreach($user->roles as $role)
                                            <span class="tag is-primary mr-2">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Знижка та бонуси -->
                <div class="column is-4">
                    <div class="box has-background-primary-light">
                        <h2 class="title is-4 has-text-primary mb-4">🎁 Ваші переваги</h2>
                        
                        @if($client->loyalty_discount > 0)
                            <div class="notification is-primary is-light mb-4">
                                <p class="title is-3 has-text-primary">{{ number_format($client->loyalty_discount, 0) }}%</p>
                                <p class="subtitle is-6">Знижка на всі послуги</p>
                                <p class="is-size-7 has-text-grey">Рівень: {{ $client->loyalty_level_name }}</p>
                            </div>
                        @else
                            <div class="notification is-light mb-4">
                                <p class="title is-5">0%</p>
                                <p class="subtitle is-6">Знижка</p>
                                <p class="is-size-7 has-text-grey">Підвищте рівень лояльності для отримання знижок</p>
                            </div>
                        @endif

                        @if($client->loyalty_points !== null)
                            <div class="notification is-info is-light">
                                <p class="title is-4">{{ number_format($client->loyalty_points, 0) }}</p>
                                <p class="subtitle is-6">Балів лояльності</p>
                                @if($client->loyalty_points > 0)
                                    <p class="is-size-7 has-text-grey">1 бал = 0.1 грн знижки</p>
                                    <p class="is-size-7 has-text-grey mt-2">Ви можете використати {{ number_format($client->loyalty_points * 0.1, 2) }} грн знижки</p>
                                @endif
                            </div>
                        @endif

                        @if($client->total_loyalty_points_earned !== null && $client->total_loyalty_points_earned > 0)
                            <div class="notification is-success is-light mt-4">
                                <p class="is-size-7 has-text-grey">Всього нараховано балів:</p>
                                <p class="title is-5">{{ number_format($client->total_loyalty_points_earned, 0) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="box mb-5">
                <h1 class="title is-3 has-text-primary m-0">👤 Особистий кабінет</h1>
                <div class="content mt-4">
                    <p><strong>Користувач:</strong> {{ $user->name }} | <strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Роль:</strong> 
                        @foreach($user->roles as $role)
                            <span class="tag is-primary mr-2">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </p>
                    <div class="notification is-warning is-light mt-4">
                        <p>Профіль клієнта не знайдено. Будь ласка, зверніться до адміністратора.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="box mb-5">
            <div class="level mb-5">
                <div class="level-left">
                    <h2 class="title is-4 m-0">Мої записи</h2>
                </div>
                <div class="level-right">
                    <a href="{{ route('client.appointments.index') }}" class="button is-primary">Всі записи</a>
                </div>
            </div>
            @php
                $client = $user->client;
                $appointments = $client ? $client->appointments()
                    ->whereIn('status', ['scheduled', 'confirmed', 'completed'])
                    ->with(['service', 'employee.user'])
                    ->orderBy('appointment_date', 'desc')
                    ->orderBy('appointment_time', 'desc')
                    ->limit(10)
                    ->get() : collect();
            @endphp

            @if($appointments->count() > 0)
                <div class="table-container">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>Послуга</th>
                                <th>Майстер</th>
                                <th>Дата</th>
                                <th>Час</th>
                                <th>Статус</th>
                                <th>Ціна</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('client.appointments.show', $appointment) }}'">
                                    <td>{{ $appointment->service->name }}</td>
                                    <td>{{ $appointment->employee->user->name }}</td>
                                    <td>{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                    <td>
                                        <span class="tag 
                                            @if($appointment->status === 'scheduled') is-info
                                            @elseif($appointment->status === 'confirmed') is-success
                                            @elseif($appointment->status === 'completed') is-dark
                                            @elseif($appointment->status === 'cancelled') is-danger
                                            @endif">
                                            @if($appointment->status === 'scheduled') Заплановано
                                            @elseif($appointment->status === 'confirmed') Підтверджено
                                            @elseif($appointment->status === 'completed') Завершено
                                            @elseif($appointment->status === 'cancelled') Скасовано
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ number_format($appointment->price, 2) }} грн</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="has-text-grey">У вас поки немає записів.</p>
                <a href="{{ route('public.booking.create') }}" class="button is-primary mt-4">
                    Записатися на послугу
                </a>
            @endif
        </div>
    </div>
@endsection
