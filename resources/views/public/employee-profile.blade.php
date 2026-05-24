@extends('layouts.public')

@section('title', $employee->user->name . ' - Профіль майстра | Beauty Salon')

@section('meta')
    <meta name="description" content="Профіль майстра {{ $employee->user->name }}. {{ $employee->specialization ?? 'Професійний майстер салону краси' }}. Рейтинг: {{ number_format($employee->rating, 1) }}">
    <meta name="keywords" content="{{ $employee->user->name }}, майстер салону краси, {{ $employee->specialization ?? '' }}">
    <meta property="og:title" content="{{ $employee->user->name }} - Профіль майстра">
    <meta property="og:description" content="{{ $employee->specialization ?? 'Професійний майстер салону краси' }}">
    <meta property="og:type" content="profile">
    <meta property="og:image" content="{{ \App\Helpers\ImageHelper::getEmployeePhoto($employee) }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@push('styles')
@endpush

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-2">Профіль майстра</h1>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
                <ul>
                    <li><a href="{{ route('landing') }}">Головна</a></li>
                    <li><a href="{{ route('public.employees') }}">Наші майстри</a></li>
                    <li class="is-active"><a href="#" aria-current="page">{{ $employee->user->name }}</a></li>
                </ul>
            </nav>

            <div class="box mb-5">
                <div class="media">
                    <div class="media-left">
                        <figure class="image is-128x128">
                            <img class="is-rounded" src="{{ \App\Helpers\ImageHelper::getEmployeePhoto($employee) }}" alt="{{ $employee->user->name }}">
                        </figure>
                    </div>
                    <div class="media-content">
                        <h1 class="title is-3 has-text-primary mb-2">{{ $employee->user->name }}</h1>
                        @if($employee->specialization)
                            <p class="tag is-primary is-medium mb-2">{{ $employee->specialization }}</p>
                        @endif
                        <p class="title is-4 has-text-warning mb-4">⭐ {{ number_format($employee->rating, 1) }}/5.0</p>
                        @if($employee->bio)
                            <p class="content">{{ $employee->bio }}</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($employee->services->count() > 0)
                <div class="box">
                    <h2 class="title is-4 has-text-primary mb-5">Послуги ({{ $employee->services->count() }})</h2>
                    <div class="columns is-multiline">
                        @foreach($employee->services as $service)
                            <div class="column is-one-third">
                                <div class="box">
                                    <p class="title is-6 has-text-primary mb-1">{{ $service->name }}</p>
                                    <p class="tag is-light mb-1">{{ $service->category->name }}</p>
                                    <p class="has-text-grey-dark">{{ number_format($service->price, 0) }} грн / {{ $service->duration }} хв</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(isset($reviews) && $reviews->count() > 0)
                <div class="box mt-5">
                    <h2 class="title is-4 has-text-primary mb-5">⭐ Відгуки ({{ $reviewsStats['total'] }})</h2>
                    
                    @if(isset($reviewsStats['average']) && $reviewsStats['average'])
                        <div class="box has-background-light mb-5">
                            <p class="title is-5 has-text-primary mb-2">
                                Середній рейтинг: {{ number_format($reviewsStats['average'], 1) }}/5.0
                            </p>
                            @if(isset($reviewsStats['by_rating']))
                                <div class="tags">
                                    @for($i = 5; $i >= 1; $i--)
                                        @php $count = $reviewsStats['by_rating'][$i] ?? 0; @endphp
                                        @if($count > 0)
                                            <span class="tag">{{ $i }} зірок: {{ $count }}</span>
                                        @endif
                                    @endfor
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="content">
                        @foreach($reviews as $review)
                            <div class="box mb-4">
                                <div class="level mb-2">
                                    <div class="level-left">
                                        <div>
                                            <p class="title is-6 mb-1">{{ $review->client->user->name }}</p>
                                            <div class="is-size-5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="{{ $i <= $review->rating ? 'has-text-warning' : 'has-text-grey-light' }}">★</span>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="level-right">
                                        <span class="has-text-grey is-size-7">{{ $review->created_at->format('d.m.Y') }}</span>
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="content">{{ $review->comment }}</p>
                                @endif
                                @if($review->service)
                                    <p class="is-size-7 has-text-primary mt-3">
                                        Послуга: {{ $review->service->name }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="has-text-centered" style="margin-top: 3rem;">
                        {{ $reviews->links('vendor.pagination.custom') }}
                    </div>
                </div>
            @elseif(isset($reviews))
                <div class="box mt-5">
                    <h2 class="title is-4 has-text-primary mb-5">⭐ Відгуки</h2>
                    <p class="has-text-centered has-text-grey py-5">
                        Поки що немає відгуків про цього майстра.
                    </p>
                </div>
            @endif
        </div>
    </section>
@endsection
