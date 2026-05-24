@extends('layouts.public')

@section('title', 'Відновлення пароля - Beauty Salon CRM')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-5-tablet is-4-desktop">
                    <div class="box">
                        <h1 class="title is-3 has-text-centered mb-5">Відновлення пароля</h1>
                        
                        @if (session('status'))
                            <div class="notification is-success is-light">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="notification is-danger is-light">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <p class="has-text-grey mb-5">
                            Введіть ваш email адрес, і ми надішлемо вам посилання для відновлення пароля.
                        </p>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="field">
                                <label class="label">Email</label>
                                <div class="control">
                                    <input 
                                        class="input" 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        autofocus
                                        placeholder="your@email.com"
                                    >
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth is-medium">
                                        Надіслати посилання для відновлення
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr class="my-5">

                        <p class="has-text-centered">
                            <a href="{{ route('login') }}" class="has-text-primary">
                                ← Повернутися до входу
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
