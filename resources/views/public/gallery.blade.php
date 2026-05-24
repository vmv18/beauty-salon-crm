@extends('layouts.public')

@section('title', 'Галерея робіт - Beauty Salon')

@section('meta')
    <meta name="description" content="Галерея наших робіт - результати професійних послуг салону краси">
    <meta name="keywords" content="галерея, салон краси, роботи, фото, результат">
    <meta property="og:title" content="Галерея робіт - Beauty Salon">
    <meta property="og:description" content="Галерея наших робіт - результати професійних послуг салону краси">
    <meta property="og:type" content="website">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@push('styles')
    <style>
        .gallery-item {
            cursor: pointer;
            transition: transform 0.3s;
        }
        .gallery-item:hover {
            transform: scale(1.05);
        }
        #lightbox {
            display: none;
        }
        #lightbox.is-active {
            display: flex;
        }
    </style>
@endpush

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-2">📸 Галерея наших робіт</h1>
                <p class="subtitle">Результати професійних послуг нашого салону краси</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
                <ul>
                    <li><a href="{{ route('landing') }}">Головна</a></li>
                    <li class="is-active"><a href="#" aria-current="page">Галерея</a></li>
                </ul>
            </nav>

            <div class="columns is-multiline is-mobile" id="gallery-grid">
                @foreach($galleryItems as $index => $item)
                    <div class="column is-3-desktop is-4-tablet is-6-mobile">
                        <div class="card gallery-item" data-index="{{ $index }}" data-id="{{ $item['id'] }}">
                            <div class="card-image">
                                <figure class="image is-square">
                                    @if(isset($item['image']))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item['image']) }}" alt="{{ $item['title'] }}">
                                    @else
                                        <div class="has-background-primary has-text-white" style="display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                                            ✨
                                        </div>
                                    @endif
                                </figure>
                            </div>
                            <div class="card-content">
                                <p class="title is-6">{{ $item['title'] }}</p>
                                <p class="is-size-7 has-text-grey">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="modal">
        <div class="modal-background" id="lightbox-close"></div>
        <div class="modal-content">
            <div class="box has-background-dark">
                <button class="delete is-large" id="lightbox-close-btn" style="position: absolute; top: 1rem; right: 1rem;"></button>
                <div class="has-text-centered">
                    <div id="lightbox-image" style="max-height: 70vh; overflow: hidden;">
                        <img id="lightbox-img" src="" alt="" class="is-hidden" style="max-width: 100%; max-height: 70vh; object-fit: contain;">
                        <div id="lightbox-placeholder" class="has-background-primary" style="min-height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white;">
                            <div id="lightbox-emoji" class="is-size-1">✨</div>
                            <div id="lightbox-title" class="title is-3 has-text-white mt-4"></div>
                            <div id="lightbox-description" class="subtitle is-5 has-text-white mt-2" style="opacity: 0.9;"></div>
                        </div>
                    </div>
                    <div id="lightbox-counter" class="has-text-white mt-4"></div>
                </div>
                <button class="button is-white" id="lightbox-prev" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);">←</button>
                <button class="button is-white" id="lightbox-next" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);">→</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Gallery Lightbox
        (function() {
            const galleryItems = document.querySelectorAll('.gallery-item');
            const lightbox = document.getElementById('lightbox');
            const lightboxClose = document.getElementById('lightbox-close');
            const lightboxCloseBtn = document.getElementById('lightbox-close-btn');
            const lightboxPrev = document.getElementById('lightbox-prev');
            const lightboxNext = document.getElementById('lightbox-next');
            const lightboxTitle = document.getElementById('lightbox-title');
            const lightboxDescription = document.getElementById('lightbox-description');
            const lightboxCounter = document.getElementById('lightbox-counter');
            let currentIndex = 0;

            const galleryData = @json($galleryItems);

            function openLightbox(index) {
                currentIndex = index;
                updateLightbox();
                lightbox.classList.add('is-active');
                document.body.classList.add('is-clipped');
            }

            function closeLightbox() {
                lightbox.classList.remove('is-active');
                document.body.classList.remove('is-clipped');
            }

            function updateLightbox() {
                const item = galleryData[currentIndex];
                lightboxTitle.textContent = item.title;
                lightboxDescription.textContent = item.description;
                lightboxCounter.textContent = `${currentIndex + 1} / ${galleryData.length}`;
                
                const lightboxImg = document.getElementById('lightbox-img');
                const lightboxPlaceholder = document.getElementById('lightbox-placeholder');
                
                if (item.image) {
                    lightboxImg.src = '/storage/' + item.image;
                    lightboxImg.alt = item.title;
                    lightboxImg.classList.remove('is-hidden');
                    lightboxPlaceholder.classList.add('is-hidden');
                } else {
                    lightboxImg.classList.add('is-hidden');
                    lightboxPlaceholder.classList.remove('is-hidden');
                }
            }

            function showPrev() {
                currentIndex = (currentIndex - 1 + galleryData.length) % galleryData.length;
                updateLightbox();
            }

            function showNext() {
                currentIndex = (currentIndex + 1) % galleryData.length;
                updateLightbox();
            }

            // Open lightbox on gallery item click
            galleryItems.forEach((item, index) => {
                item.addEventListener('click', () => openLightbox(index));
            });

            // Close lightbox
            if (lightboxClose) {
                lightboxClose.addEventListener('click', closeLightbox);
            }
            if (lightboxCloseBtn) {
                lightboxCloseBtn.addEventListener('click', closeLightbox);
            }
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox || e.target.classList.contains('modal-background')) {
                    closeLightbox();
                }
            });

            // Navigation
            if (lightboxPrev) {
                lightboxPrev.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showPrev();
                });
            }
            if (lightboxNext) {
                lightboxNext.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showNext();
                });
            }

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (lightbox.classList.contains('is-active')) {
                    if (e.key === 'Escape') {
                        closeLightbox();
                    } else if (e.key === 'ArrowLeft') {
                        showPrev();
                    } else if (e.key === 'ArrowRight') {
                        showNext();
                    }
                }
            });
        })();
    </script>
@endpush
