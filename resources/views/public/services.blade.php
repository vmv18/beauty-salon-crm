@extends('layouts.public')

@section('title', 'Наші послуги - Beauty Salon')

@section('meta')
    <meta name="description" content="Широкий спектр послуг салону краси: стрижки, фарбування, манікюр, педикюр та інші послуги догляду">
    <meta name="keywords" content="послуги салону краси, стрижки, фарбування, манікюр, педикюр, догляд">
    <meta property="og:title" content="Наші послуги - Beauty Salon">
    <meta property="og:description" content="Широкий спектр послуг салону краси">
    <meta property="og:type" content="website">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@push('styles')
<style>
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
    .service-card .card-content .button {
        margin-top: auto;
    }
</style>
@endpush

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-2">💅 Наші послуги</h1>
                <p class="subtitle">Виберіть послугу, яка вам підходить</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
                <ul>
                    <li><a href="{{ route('landing') }}">Головна</a></li>
                    <li class="is-active"><a href="#" aria-current="page">Послуги</a></li>
                </ul>
            </nav>

            <div class="box mb-5">
                <div class="tags mb-4">
                    <a href="{{ route('public.services') }}" class="tag {{ !request('category') ? 'is-primary' : 'is-light' }} is-medium">
                        Всі послуги
                    </a>
                    @foreach($categories as $category)
                        @if($category->services_count > 0)
                            <a href="{{ route('public.services', ['category' => $category->id]) }}" class="tag {{ request('category') == $category->id ? 'is-primary' : 'is-light' }} is-medium">
                                {{ $category->name }} ({{ $category->services_count }})
                            </a>
                        @endif
                    @endforeach
                </div>

                <form method="GET" action="{{ route('public.services') }}">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="columns is-multiline">
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Пошук</label>
                                <div class="control">
                                    <input class="input" type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Назва послуги...">
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Сортування</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="sort" name="sort">
                                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>За назвою</option>
                                            <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>За ціною</option>
                                            <option value="duration" {{ request('sort') == 'duration' ? 'selected' : '' }}>За тривалістю</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Напрямок</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="dir" name="dir">
                                            <option value="asc" {{ request('dir') == 'asc' ? 'selected' : '' }}>За зростанням</option>
                                            <option value="desc" {{ request('dir') == 'desc' ? 'selected' : '' }}>За спаданням</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">&nbsp;</label>
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth">Фільтрувати</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if($services->count() > 0)
                <div class="columns is-multiline">
                    @foreach($services as $service)
                        <div class="column is-one-third">
                            <div class="card service-card">
                                <div class="card-image">
                                    <figure class="image is-4by3">
                                        <img src="{{ \App\Helpers\ImageHelper::getServiceImage($service) }}" alt="{{ $service->name }}">
                                    </figure>
                                </div>
                                <div class="card-content">
                                    <p class="tag is-primary is-light mb-2">{{ $service->category->name }}</p>
                                    <a href="{{ route('public.service-detail', $service) }}" class="has-text-dark">
                                        <p class="title is-5 mb-2">{{ $service->name }}</p>
                                    </a>
                                    <p class="content is-size-7 has-text-grey mb-4">{{ \Illuminate\Support\Str::limit($service->description, 100) }}</p>
                                    <div class="level is-mobile mb-4">
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
                                    <a href="{{ route('public.service-detail', $service) }}" class="button is-primary is-fullwidth mt-auto">
                                        Деталі та запис
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($services->hasPages())
                    <div class="has-text-centered" style="margin-top: 3rem;">
                        {{ $services->links('vendor.pagination.custom') }}
                    </div>
                @endif
            @else
                <div class="has-text-centered py-6">
                    <p class="subtitle has-text-grey">Послуг не знайдено</p>
                </div>
            @endif
        </div>
    </section>
@endsection
