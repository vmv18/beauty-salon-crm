@extends('layouts.app')

@section('title', 'Залишити відгук - Beauty Salon CRM')

@push('styles')
    <style>
        .rating-input input[type="radio"] { display: none; }
        .rating-input label { cursor: pointer; font-size: 2rem; color: #ddd; transition: color 0.2s; }
        .rating-input input[type="radio"]:checked ~ label,
        .rating-input label:hover,
        .rating-input label:hover ~ label { color: #ffc107; }
        .rating-input input[type="radio"]:checked ~ label { color: #ffc107; }
    </style>
@endpush

@section('content')
    <div class="container">
        @php
            $user = auth()->user();
            $appointmentRoute = $appointment 
                ? ($user->hasRole('client') ? route('client.appointments.show', $appointment) : route('appointments.show', $appointment))
                : ($user->hasRole('client') ? route('client.appointments.index') : route('appointments.index'));
        @endphp
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                @if($user->hasRole('client'))
                    <li><a href="{{ route('client.dashboard') }}">Кабінет</a></li>
                    <li><a href="{{ route('client.appointments.index') }}">Мої записи</a></li>
                    @if($appointment)
                        <li><a href="{{ route('client.appointments.show', $appointment) }}">Запис #{{ $appointment->id }}</a></li>
                    @endif
                @else
                    <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                    <li><a href="{{ route('appointments.index') }}">Записи</a></li>
                    @if($appointment)
                        <li><a href="{{ route('appointments.show', $appointment) }}">Запис #{{ $appointment->id }}</a></li>
                    @endif
                @endif
                <li class="is-active"><a href="#" aria-current="page">Залишити відгук</a></li>
            </ul>
        </nav>

        <div class="box">
            <h1 class="title is-3 has-text-primary mb-5">⭐ Залишити відгук</h1>

        @if($appointment)
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-purple-600 mb-4">Інформація про запис</h3>
            <p class="mb-2"><strong class="text-purple-600">Послуга:</strong> {{ $appointment->service->name }}</p>
            <p class="mb-2"><strong class="text-purple-600">Майстер:</strong> {{ $appointment->employee->user->name }}</p>
            <p><strong class="text-purple-600">Дата:</strong> {{ $appointment->appointment_date->format('d.m.Y') }} о {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</p>
        </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('reviews.store') }}">
            @csrf
            
            @if($appointment)
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
            @else
                <div class="mb-6">
                    <label for="appointment_id" class="block mb-2 text-gray-700 font-semibold">Запис *</label>
                    <select name="appointment_id" id="appointment_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                        <option value="">Оберіть запис</option>
                        @foreach(auth()->user()->client->appointments()->where('status', 'completed')->get() as $apt)
                            <option value="{{ $apt->id }}">
                                {{ $apt->service->name }} - {{ $apt->appointment_date->format('d.m.Y') }} о {{ \Carbon\Carbon::parse($apt->appointment_time)->format('H:i') }}
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_id')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="mb-6">
                <label class="block mb-2 text-gray-700 font-semibold">Рейтинг *</label>
                <div class="rating-input flex gap-2 items-center">
                    <input type="radio" name="rating" value="5" id="rating5" required>
                    <label for="rating5">★</label>
                    <input type="radio" name="rating" value="4" id="rating4">
                    <label for="rating4">★</label>
                    <input type="radio" name="rating" value="3" id="rating3">
                    <label for="rating3">★</label>
                    <input type="radio" name="rating" value="2" id="rating2">
                    <label for="rating2">★</label>
                    <input type="radio" name="rating" value="1" id="rating1">
                    <label for="rating1">★</label>
                </div>
                @error('rating')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="comment" class="block mb-2 text-gray-700 font-semibold">Коментар</label>
                <textarea name="comment" id="comment" placeholder="Залиште свій відгук про послугу та майстра..." class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm resize-y min-h-[150px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('comment') }}</textarea>
                @error('comment')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary is-medium">Відправити відгук</button>
                </div>
                @php
                    $user = auth()->user();
                    $cancelRoute = $appointment 
                        ? ($user->hasRole('client') ? route('client.appointments.show', $appointment) : route('appointments.show', $appointment))
                        : ($user->hasRole('client') ? route('client.appointments.index') : route('appointments.index'));
                @endphp
                <div class="control">
                    <a href="{{ $cancelRoute }}" class="button is-light is-medium">Скасувати</a>
                </div>
            </div>
        </form>
    </div>
@endsection

