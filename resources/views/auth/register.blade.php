@extends('layouts.public')

@section('title', 'Реєстрація - Beauty Salon CRM')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
<style>
</style>
@endpush

@section('content')
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-6-tablet is-5-desktop">
                    <div class="box">
                        <h1 class="title is-3 has-text-centered mb-5">Реєстрація</h1>
                        
                        @if ($errors->any())
                            <div class="notification is-danger is-light">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="notification is-success is-light">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="notification is-danger is-light">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register.post') }}">
                            @csrf

                            <div class="field">
                                <label class="label">Ім'я <span class="has-text-danger">*</span></label>
                                <div class="control">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        required 
                                        autofocus
                                        placeholder="Введіть ваше ім'я"
                                    >
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Email <span class="has-text-danger">*</span></label>
                                <div class="control">
                                    <input 
                                        class="input" 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required
                                        placeholder="your@email.com"
                                    >
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Пароль <span class="has-text-danger">*</span></label>
                                <div class="control has-icons-right" style="position: relative;">
                                    <input 
                                        class="input" 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        required
                                        placeholder="Введіть пароль"
                                        style="padding-right: 2.5rem;"
                                    >
                                    <span class="icon is-right" style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10;" onclick="togglePassword('password')">
                                        <svg id="password-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 1.25rem; height: 1.25rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <p class="help">Мінімум 8 символів</p>
                            </div>

                            <div class="field">
                                <label class="label">Підтвердження пароля <span class="has-text-danger">*</span></label>
                                <div class="control has-icons-right" style="position: relative;">
                                    <input 
                                        class="input" 
                                        type="password" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        required
                                        placeholder="Повторіть пароль"
                                        style="padding-right: 2.5rem;"
                                    >
                                    <span class="icon is-right" style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10;" onclick="togglePassword('password_confirmation')">
                                        <svg id="password_confirmation-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 1.25rem; height: 1.25rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Телефон</label>
                                <div class="control">
                                    <input 
                                        class="input" 
                                        type="text" 
                                        id="phone" 
                                        name="phone" 
                                        value="{{ old('phone') }}" 
                                        placeholder="+380XXXXXXXXX"
                                    >
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Дата народження</label>
                                <div class="control">
                                    <input 
                                        class="input" 
                                        type="date" 
                                        id="date_of_birth" 
                                        name="date_of_birth" 
                                        value="{{ old('date_of_birth') }}"
                                    >
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Стать</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="gender" name="gender">
                                            <option value="">Не вказано</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Чоловік</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Жінка</option>
                                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Інше</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth is-medium">
                                        Зареєструватися
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr class="my-5">

                        <p class="has-text-centered">
                            <span class="has-text-grey">Вже маєте обліковий запис?</span>
                            <a href="{{ route('login') }}" class="has-text-primary">Увійти</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');
            
            if (!input || !icon) return;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m13.42 13.42l-3.29-3.29M3 3l13.42 13.42"></path>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
@endsection
