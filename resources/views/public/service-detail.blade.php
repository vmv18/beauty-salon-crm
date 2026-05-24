@extends('layouts.public')

@section('title', $service->name . ' - Beauty Salon')

@section('meta')
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($service->description, 160) }}">
    <meta name="keywords" content="{{ $service->name }}, {{ $service->category->name }}, салон краси, послуги">
    <meta property="og:title" content="{{ $service->name }} - Beauty Salon">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($service->description, 160) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ \App\Helpers\ImageHelper::getServiceImage($service) }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@push('styles')
@endpush

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-2">💅 {{ $service->name }}</h1>
                <p class="subtitle">{{ $service->category->name }}</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
                <ul>
                    <li><a href="{{ route('landing') }}">Головна</a></li>
                    <li><a href="{{ route('public.services') }}">Послуги</a></li>
                    <li class="is-active"><a href="#" aria-current="page">{{ $service->name }}</a></li>
                </ul>
            </nav>

            <div class="box">
                <div class="columns">
                    <div class="column is-half">
                        <figure class="image is-square">
                            <img src="{{ \App\Helpers\ImageHelper::getServiceImage($service) }}" alt="{{ $service->name }}">
                        </figure>
                    </div>
                    <div class="column is-half">
                        <p class="title is-2 has-text-primary mb-5">{{ number_format($service->price, 0) }} грн</p>
                        <div class="level mb-5">
                            <div class="level-item has-text-centered">
                                <div>
                                    <p class="heading">⏱️</p>
                                    <p class="title is-6">{{ $service->duration }} хвилин</p>
                                </div>
                            </div>
                            <div class="level-item has-text-centered">
                                <div>
                                    <p class="heading">⭐</p>
                                    <p class="title is-6">Популярна послуга</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{ route('public.booking.create', ['service_id' => $service->id]) }}" class="button is-primary is-large">
                                Записатися на послугу
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content mt-6">
                    <h2 class="title is-4 mb-4">Опис послуги</h2>
                    <p class="is-size-5">{{ $service->description }}</p>
                </div>

                @if($service->employees->count() > 0)
                <div class="mt-6">
                    <h2 class="title is-4 mb-5">Майстри, які надають цю послугу</h2>
                    <div class="columns is-multiline">
                        @foreach($service->employees as $employee)
                            <div class="column is-3">
                                <div class="box has-text-centered">
                                    <a href="{{ route('public.employee-profile', $employee) }}" class="has-text-dark">
                                        <figure class="image is-64x64 mx-auto mb-3">
                                            <img class="is-rounded" src="{{ \App\Helpers\ImageHelper::getEmployeePhoto($employee) }}" alt="{{ $employee->user->name }}">
                                        </figure>
                                        <p class="title is-6 mb-2">{{ $employee->user->name }}</p>
                                        <p class="has-text-warning has-text-weight-semibold mb-2">⭐ {{ number_format($employee->rating, 1) }}</p>
                                        @if($employee->specialization)
                                            <p class="is-size-7 has-text-grey">{{ $employee->specialization }}</p>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            @if(isset($reviews) && $reviews->count() > 0)
            <div class="box mt-5">
                <h2 class="title is-3 has-text-centered mb-5">⭐ Відгуки ({{ $reviewsStats['total'] }})</h2>
                
                @if(isset($reviewsStats['average']) && $reviewsStats['average'])
                    <div class="box has-background-light mb-5">
                        <div class="has-text-centered">
                            <p class="title is-3 has-text-primary mb-2">
                                Середній рейтинг: {{ number_format($reviewsStats['average'], 1) }}/5.0
                            </p>
                            <div class="is-size-3 mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= round($reviewsStats['average']) ? 'has-text-warning' : 'has-text-grey-light' }}">★</span>
                                @endfor
                            </div>
                            @if(isset($reviewsStats['by_rating']))
                                <div class="tags is-centered">
                                    @for($i = 5; $i >= 1; $i--)
                                        @php $count = $reviewsStats['by_rating'][$i] ?? 0; @endphp
                                        @if($count > 0)
                                            <span class="tag">{{ $i }} зірок: {{ $count }}</span>
                                        @endif
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="content">
                    @foreach($reviews as $review)
                        <div class="box mb-4">
                            <div class="level mb-3">
                                <div class="level-left">
                                    <div>
                                        <p class="title is-6 mb-1">{{ $review->client->user->name }}</p>
                                        <div class="is-size-5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $review->rating ? 'has-text-warning' : 'has-text-grey-light' }}">★</span>
                                            @endfor
                                            <span class="is-size-7 has-text-grey ml-2">({{ $review->rating }}/5)</span>
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
                            @if($review->employee)
                                <p class="is-size-7 has-text-primary mt-3">
                                    Майстер: <a href="{{ route('public.employee-profile', $review->employee) }}" class="has-text-primary">{{ $review->employee->user->name }}</a>
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
                <h2 class="title is-3 has-text-centered mb-5">⭐ Відгуки</h2>
                <p class="has-text-centered has-text-grey py-5">
                    Поки що немає відгуків про цю послугу.
                </p>
            </div>
            @endif

            @if($relatedServices->count() > 0)
            <div class="mt-5">
                <h2 class="title is-3 has-text-centered mb-5">Схожі послуги</h2>
                <div class="columns is-multiline">
                    @foreach($relatedServices as $related)
                        <div class="column is-3">
                            <div class="card">
                                <a href="{{ route('public.service-detail', $related) }}" class="has-text-dark">
                                    <div class="card-image">
                                        <figure class="image is-4by3">
                                            @if($related->image)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($related->image) }}" alt="{{ $related->name }}">
                                            @else
                                                <div class="has-background-primary has-text-white" style="display: flex; align-items: center; justify-content: center; font-size: 3rem;">💅</div>
                                            @endif
                                        </figure>
                                    </div>
                                    <div class="card-content">
                                        <p class="title is-6 mb-2">{{ $related->name }}</p>
                                        <p class="title is-5 has-text-primary m-0">{{ number_format($related->price, 0) }} грн</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>
@endsection
