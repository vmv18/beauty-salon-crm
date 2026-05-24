@extends('layouts.public')

@section('title', 'Beauty Salon - Салон краси | Професійні послуги догляду')

@section('meta')
    <meta name="description" content="Beauty Salon - сучасний салон краси з професійними майстрами. Широкий спектр послуг: стрижки, фарбування, манікюр, педикюр. Запис онлайн.">
    <meta name="keywords" content="салон краси, послуги салону, стрижки, фарбування, манікюр, педикюр, запис онлайн">
    <meta property="og:title" content="Beauty Salon - Салон краси">
    <meta property="og:description" content="Професійні послуги салону краси з індивідуальним підходом">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@push('styles')
    <style>
        .hero-section {
            position: relative;
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .hero-background {
            position: absolute;
            inset: 0;
            z-index: 0;
            opacity: 0.3;
        }
        .hero-background img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            background: radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3), transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(255, 119, 198, 0.3), transparent 50%),
                        radial-gradient(circle at 40% 20%, rgba(120, 219, 255, 0.2), transparent 50%);
            animation: overlayMove 20s ease-in-out infinite;
        }
        @keyframes overlayMove {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, -20px) scale(1.1); }
        }
        .hero-decorative {
            position: absolute;
            z-index: 2;
            opacity: 0.1;
        }
        .hero-decorative-circle {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: white;
            position: absolute;
            animation: float 6s ease-in-out infinite;
        }
        .hero-decorative-circle:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        .hero-decorative-circle:nth-child(2) {
            bottom: 10%;
            right: 10%;
            width: 200px;
            height: 200px;
            animation-delay: 2s;
        }
        .hero-decorative-circle:nth-child(3) {
            top: 50%;
            right: 20%;
            width: 150px;
            height: 150px;
            animation-delay: 4s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        .hero-content {
            position: relative;
            z-index: 10;
            color: white;
            padding: 2rem 0;
        }
        .hero-content-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 3rem 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 0 4px 20px rgba(255, 255, 255, 0.3);
        }
        .hero-subtitle {
            font-size: 1.5rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            font-weight: 300;
            letter-spacing: 0.5px;
        }
        .hero-button {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            background: white;
            color: #667eea;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        .hero-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(102, 126, 234, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .hero-button:hover::before {
            width: 300px;
            height: 300px;
        }
        .hero-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        @media (max-width: 768px) {
            .hero-section {
                min-height: 70vh;
            }
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.2rem;
            }
            .hero-content-box {
                padding: 2rem 1.5rem;
            }
        }
        .about-section {
            padding: 5rem 0;
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }
        .about-content {
            padding-right: 3rem;
        }
        .about-title {
            font-size: 3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .about-subtitle {
            font-size: 1.25rem;
            color: #667eea;
            font-weight: 500;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #4a5568;
        }
        .about-text p {
            margin-bottom: 1.5rem;
        }
        .about-features {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        .about-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .about-feature-item:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
            border-left-color: #667eea;
        }
        .feature-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #667eea;
            opacity: 0.2;
            line-height: 1;
            min-width: 60px;
        }
        .feature-content {
            flex: 1;
        }
        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.75rem;
        }
        .feature-description {
            font-size: 1rem;
            line-height: 1.6;
            color: #718096;
            margin: 0;
        }
        @media (max-width: 768px) {
            .about-section {
                padding: 3rem 0;
            }
            .about-content {
                padding-right: 0;
                margin-bottom: 3rem;
            }
            .about-title {
                font-size: 2rem;
            }
            .about-subtitle {
                font-size: 1.1rem;
            }
            .about-text {
                font-size: 1rem;
            }
            .about-feature-item {
                padding: 1.5rem;
            }
            .feature-number {
                font-size: 2rem;
                min-width: 50px;
            }
            .feature-title {
                font-size: 1.25rem;
            }
        }
        .testimonials-section {
            padding: 5rem 0;
            background: #f8f9fa;
        }
        .testimonials-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .testimonials-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .testimonials-subtitle {
            font-size: 1.25rem;
            color: #718096;
            max-width: 42rem;
            margin: 0 auto;
        }
        .testimonial-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        .testimonial-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }
        .testimonial-rating {
            margin-bottom: 1.5rem;
        }
        .testimonial-rating .star {
            color: #d1d5db;
            font-size: 1.25rem;
            margin-right: 0.25rem;
            transition: color 0.2s ease;
        }
        .testimonial-rating .star.is-active {
            color: #fbbf24;
        }
        .testimonial-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 2rem;
            flex-grow: 1;
            font-style: italic;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        .author-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .author-info {
            flex: 1;
        }
        .author-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }
        .author-role {
            font-size: 0.9rem;
            color: #718096;
            margin: 0;
        }
        @media (max-width: 768px) {
            .testimonials-section {
                padding: 3rem 0;
            }
            .testimonials-title {
                font-size: 2rem;
            }
            .testimonials-subtitle {
                font-size: 1.1rem;
            }
            .testimonial-card {
                padding: 2rem;
            }
            .testimonial-text {
                font-size: 1rem;
            }
        }
        .gallery-section {
            padding: 5rem 0;
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
        }
        .gallery-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .gallery-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .gallery-subtitle {
            font-size: 1.25rem;
            color: #718096;
            max-width: 42rem;
            margin: 0 auto;
        }
        .gallery-slider-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .gallery-slider {
            position: relative;
            width: 100%;
            height: 600px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
        }
        .gallery-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .gallery-slide.is-active {
            opacity: 1;
            z-index: 1;
        }
        .gallery-slide-image {
            width: 100%;
            height: 100%;
            position: relative;
        }
        .gallery-slide-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .gallery-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .placeholder-icon {
            font-size: 5rem;
            opacity: 0.5;
        }
        @media (max-width: 768px) {
            .gallery-section {
                padding: 3rem 0;
            }
            .gallery-title {
                font-size: 2rem;
            }
            .gallery-subtitle {
                font-size: 1.1rem;
            }
            .gallery-slider {
                height: 400px;
            }
        }
        .service-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .service-card .card-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .service-card .card-content > .content {
            flex-grow: 1;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background">
            <img src="{{ \Illuminate\Support\Facades\Storage::url('landing/hero.png') }}" alt="Beauty Salon">
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-decorative">
            <div class="hero-decorative-circle"></div>
            <div class="hero-decorative-circle"></div>
            <div class="hero-decorative-circle"></div>
        </div>
        <div class="container hero-content">
            <div class="hero-content-box">
                <h1 class="hero-title">Ваша краса - наш пріоритет</h1>
                <p class="hero-subtitle">Професійні послуги салону краси з індивідуальним підходом до кожного клієнта</p>
                <a href="{{ route('public.booking.create') }}" class="hero-button">
                    ✨ Записатися онлайн
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section about-section">
        <div class="container">
            <div class="columns is-vcentered">
                <div class="column is-5">
                    <div class="about-content">
                        <h2 class="about-title">Про нас</h2>
                        <p class="about-subtitle">Ми створюємо красу та впевненість у собі для наших клієнтів</p>
                        <div class="about-text">
                            <p>Beauty Salon - це сучасний салон краси, де працюють досвідчені майстри з багаторічним досвідом. Ми пропонуємо широкий спектр послуг для догляду за вашою красою та здоров'ям.</p>
                            <p>Наша місія - допомогти вам відчути себе особливими та впевненими у собі. Ми використовуємо тільки якісні матеріали та сучасне обладнання.</p>
                        </div>
                    </div>
                </div>
                <div class="column is-7">
                    <div class="about-features">
                        <div class="about-feature-item">
                            <div class="feature-number">01</div>
                            <div class="feature-content">
                                <h3 class="feature-title">Досвідчені майстри</h3>
                                <p class="feature-description">Професійна команда з багаторічним досвідом роботи в індустрії краси</p>
                            </div>
                        </div>
                        <div class="about-feature-item">
                            <div class="feature-number">02</div>
                            <div class="feature-content">
                                <h3 class="feature-title">Якісні матеріали</h3>
                                <p class="feature-description">Використовуємо тільки перевірені бренди та сертифіковану продукцію</p>
                            </div>
                        </div>
                        <div class="about-feature-item">
                            <div class="feature-number">03</div>
                            <div class="feature-content">
                                <h3 class="feature-title">Індивідуальний підхід</h3>
                                <p class="feature-description">Кожен клієнт отримує персональну увагу та індивідуальну програму догляду</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section">
        <div class="container">
            <h2 class="title is-2 has-text-centered mb-5">Наші послуги</h2>
            <p class="subtitle is-5 has-text-centered has-text-grey mb-6" style="max-width: 42rem; margin-left: auto; margin-right: auto;">
                Широкий спектр послуг для догляду за вашою красою
            </p>
            <div class="columns is-multiline mb-6">
                @forelse($featuredServices as $service)
                    <div class="column is-one-third">
                        <div class="card service-card">
                            <div class="card-image">
                                <figure class="image is-4by3">
                                    <img src="{{ \App\Helpers\ImageHelper::getServiceImage($service) }}" alt="{{ $service->name }}" style="background: linear-gradient(to right, #9333ea, #7c3aed);">
                                </figure>
                            </div>
                            <div class="card-content">
                                <p class="tag is-primary is-light mb-2">{{ $service->category->name }}</p>
                                <p class="title is-5 mb-3">{{ $service->name }}</p>
                                <p class="content has-text-grey mb-4">{{ \Illuminate\Support\Str::limit($service->description, 100) }}</p>
                                <div class="level is-mobile mt-auto">
                                    <div class="level-left">
                                        <div class="level-item">
                                            <p class="title is-4 has-text-primary m-0">{{ number_format($service->price, 0) }} грн</p>
                                        </div>
                                    </div>
                                    <div class="level-right">
                                        <div class="level-item">
                                            <span class="has-text-grey">{{ $service->duration }} хв</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="column">
                        <p class="has-text-centered has-text-grey">Послуги будуть доступні найближчим часом</p>
                    </div>
                @endforelse
            </div>
            <div class="has-text-centered">
                <a href="{{ route('public.services') }}" class="button is-primary is-large">
                    Переглянути всі послуги
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="section gallery-section">
        <div class="container">
            <div class="gallery-header">
                <h2 class="gallery-title">Галерея наших робіт</h2>
                <p class="gallery-subtitle">
                    Результати нашої роботи говорять самі за себе
                </p>
            </div>
            <div class="gallery-slider-container">
                <div class="gallery-slider" id="gallery-slider">
                    @foreach($galleryImages as $index => $item)
                        <div class="gallery-slide {{ $index === 0 ? 'is-active' : '' }}" data-index="{{ $index }}">
                            <div class="gallery-slide-image">
                                @if(isset($item['image']))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item['image']) }}" alt="{{ $item['title'] }}">
                                @else
                                    <div class="gallery-placeholder">
                                        <span class="placeholder-icon">✨</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="has-text-centered">
                    <a href="{{ route('public.gallery') }}" class="button is-primary is-large">
                        Переглянути всю галерею
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="section testimonials-section">
        <div class="container">
            <div class="testimonials-header">
                <h2 class="testimonials-title">Відгуки наших клієнтів</h2>
                <p class="testimonials-subtitle">
                    Що кажуть про нас наші клієнти
                </p>
            </div>
            @if(isset($testimonials) && $testimonials->count() > 0)
                <div class="columns is-multiline">
                    @foreach($testimonials as $testimonial)
                        <div class="column is-one-third">
                            <div class="testimonial-card">
                                <div class="testimonial-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $testimonial->rating ? 'is-active' : '' }}">★</span>
                                    @endfor
                                </div>
                                <p class="testimonial-text">
                                    {{ $testimonial->comment ?: 'Відмінний сервіс!' }}
                                </p>
                                <div class="testimonial-author">
                                    @php
                                        $clientName = $testimonial->client->user->name ?? 'Клієнт';
                                        $nameParts = explode(' ', $clientName);
                                        $initials = '';
                                        if (count($nameParts) >= 2) {
                                            $initials = mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1);
                                        } else {
                                            $initials = mb_substr($clientName, 0, 2);
                                        }
                                        $initials = mb_strtoupper($initials);
                                    @endphp
                                    <div class="author-avatar">{{ $initials }}</div>
                                    <div class="author-info">
                                        <p class="author-name">{{ $clientName }}</p>
                                        <p class="author-role">{{ $testimonial->service->name ?? 'Послуга' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="has-text-centered py-6">
                    <p class="subtitle has-text-grey">Поки що немає відгуків</p>
                </div>
            @endif
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Gallery Slider - автоматичне перегортання
        (function() {
            const slider = document.getElementById('gallery-slider');
            if (!slider) return;
            
            const slides = slider.querySelectorAll('.gallery-slide');
            if (slides.length === 0) return;
            
            let currentIndex = 0;
            const slideInterval = 5000; // 5 секунд
            
            function showSlide(index) {
                // Видаляємо активний клас з усіх слайдів
                slides.forEach(slide => slide.classList.remove('is-active'));
                
                // Додаємо активний клас до поточного слайду
                slides[index].classList.add('is-active');
            }
            
            function nextSlide() {
                currentIndex = (currentIndex + 1) % slides.length;
                showSlide(currentIndex);
            }
            
            // Автоматичне перегортання
            setInterval(nextSlide, slideInterval);
            
            // Показуємо перший слайд при завантаженні
            showSlide(0);
        })();
    </script>
@endpush
