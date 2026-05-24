@extends('layouts.public')

@section('title', 'Контакти - Beauty Salon')

@section('meta')
    <meta name="description" content="Зв'яжіться з нами - Beauty Salon. Адреса, телефон, email та форма зворотного зв'язку">
    <meta name="keywords" content="контакти, салон краси, адреса, телефон, зв'язок">
    <meta property="og:title" content="Контакти - Beauty Salon">
    <meta property="og:description" content="Зв'яжіться з нами - Beauty Salon. Адреса, телефон, email та форма зворотного зв'язку">
    <meta property="og:type" content="website">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 0.5rem;
        }
    </style>
@endpush

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-2">📞 Зв'яжіться з нами</h1>
                <p class="subtitle">Ми завжди раді відповісти на ваші питання та допомогти вам</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
                <ul>
                    <li><a href="{{ route('landing') }}">Головна</a></li>
                    <li class="is-active"><a href="#" aria-current="page">Контакти</a></li>
                </ul>
            </nav>

            <div class="columns">
                <div class="column is-half">
                    <div class="box">
                        <h2 class="title is-4 mb-5">Надішліть нам повідомлення</h2>
                        
                        <form method="POST" action="{{ route('public.contact.store') }}">
                            @csrf
                            <div class="field">
                                <label class="label">Ваше ім'я <span class="has-text-danger">*</span></label>
                                <div class="control">
                                    <input class="input" type="text" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                                @error('name')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="field">
                                <label class="label">Email <span class="has-text-danger">*</span></label>
                                <div class="control">
                                    <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="field">
                                <label class="label">Телефон</label>
                                <div class="control">
                                    <input class="input" type="tel" id="phone" name="phone" value="{{ old('phone') }}">
                                </div>
                                @error('phone')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="field">
                                <label class="label">Повідомлення <span class="has-text-danger">*</span></label>
                                <div class="control">
                                    <textarea class="textarea" id="message" name="message" required style="min-height: 120px;">{{ old('message') }}</textarea>
                                </div>
                                @error('message')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="field">
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth is-large">
                                        Відправити повідомлення
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="column is-half">
                    <div class="box mb-5">
                        <h3 class="title is-5 has-text-primary mb-5">Контактна інформація</h3>
                        <div class="media mb-5">
                            <div class="media-left">
                                <span class="icon is-large">📍</span>
                            </div>
                            <div class="media-content">
                                <h4 class="title is-6 mb-1">Адреса</h4>
                                <p class="has-text-grey">м. Київ, вул. Хрещатик, 1<br>БЦ "Центральний", 3 поверх</p>
                            </div>
                        </div>
                        <div class="media mb-5">
                            <div class="media-left">
                                <span class="icon is-large">📞</span>
                            </div>
                            <div class="media-content">
                                <h4 class="title is-6 mb-1">Телефон</h4>
                                <p class="has-text-grey">+380 (50) 123-45-67<br>+380 (67) 123-45-68</p>
                            </div>
                        </div>
                        <div class="media mb-5">
                            <div class="media-left">
                                <span class="icon is-large">✉️</span>
                            </div>
                            <div class="media-content">
                                <h4 class="title is-6 mb-1">Email</h4>
                                <p class="has-text-grey">info@beautysalon.com<br>booking@beautysalon.com</p>
                            </div>
                        </div>
                        <div class="media">
                            <div class="media-left">
                                <span class="icon is-large">🕒</span>
                            </div>
                            <div class="media-content">
                                <h4 class="title is-6 mb-1">Години роботи</h4>
                                <p class="has-text-grey">Понеділок - П'ятниця: 9:00 - 20:00<br>Субота - Неділя: 10:00 - 18:00</p>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <h3 class="title is-5 has-text-primary mb-5">Як нас знайти</h3>
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Координати салону краси (Київ, Хрещатик, 1)
            const salonLat = 50.4501;
            const salonLng = 30.5234;
            
            // Ініціалізація карти
            const map = L.map('map').setView([salonLat, salonLng], 15);
            
            // Додавання тайлів OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Додавання маркера
            const marker = L.marker([salonLat, salonLng]).addTo(map);
            
            // Додавання popup з інформацією
            marker.bindPopup(`
                <div style="text-align: center;">
                    <strong style="color: #9333ea; font-size: 16px;">💅 Beauty Salon</strong><br>
                    <p style="margin: 8px 0;">м. Київ, вул. Хрещатик, 1<br>БЦ "Центральний", 3 поверх</p>
                    <p style="margin: 4px 0; color: #666;">📞 +380 (50) 123-45-67</p>
                </div>
            `).openPopup();
            
            // Додавання кола для візуалізації області
            L.circle([salonLat, salonLng], {
                color: '#9333ea',
                fillColor: '#9333ea',
                fillOpacity: 0.1,
                radius: 200
            }).addTo(map);
        });
    </script>
@endpush
