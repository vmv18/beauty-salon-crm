@extends('layouts.public')

@section('title', 'Наші майстри - Beauty Salon')

@section('meta')
    <meta name="description" content="Професійні майстри салону краси з багаторічним досвідом. Ознайомтесь з нашими спеціалістами">
    <meta name="keywords" content="майстри салону краси, спеціалісти, профілі майстрів">
    <meta property="og:title" content="Наші майстри - Beauty Salon">
    <meta property="og:description" content="Професійні майстри салону краси">
    <meta property="og:type" content="website">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@push('styles')
@endpush

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-2">👨‍💼 Наші майстри</h1>
                <p class="subtitle">Професійна команда салону краси</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
                <ul>
                    <li><a href="{{ route('landing') }}">Головна</a></li>
                    <li class="is-active"><a href="#" aria-current="page">Наші майстри</a></li>
                </ul>
            </nav>

            <div class="box mb-5">
                <form method="GET" action="{{ route('public.employees') }}">
                    <div class="columns is-multiline">
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Пошук</label>
                                <div class="control">
                                    <input class="input" type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ім'я, спеціалізація...">
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Спеціалізація</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="specialization" name="specialization">
                                            <option value="">Всі</option>
                                            @foreach($specializations as $spec)
                                                <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Сортування</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="sort" name="sort">
                                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>За рейтингом</option>
                                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>За ім'ям</option>
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

            @if($employees->count() > 0)
                <div class="columns is-multiline">
                    @foreach($employees as $employee)
                        <div class="column is-one-third">
                            <a href="{{ route('public.employee-profile', $employee) }}" class="card has-text-dark">
                                <div class="card-image">
                                    <figure class="image is-4by3">
                                        <img src="{{ \App\Helpers\ImageHelper::getEmployeePhoto($employee) }}" alt="{{ $employee->user->name }}">
                                    </figure>
                                </div>
                                <div class="card-content">
                                    <p class="title is-5 mb-2">{{ $employee->user->name }}</p>
                                    @if($employee->specialization)
                                        <p class="tag is-primary is-light mb-2">{{ $employee->specialization }}</p>
                                    @endif
                                    <p class="has-text-warning has-text-weight-semibold mb-2">⭐ {{ number_format($employee->rating, 1) }}/5.0</p>
                                    <p class="is-size-7 has-text-grey">{{ $employee->services->count() }} послуг</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                @if($employees->hasPages())
                    <div class="has-text-centered" style="margin-top: 3rem;">
                        {{ $employees->links('vendor.pagination.custom') }}
                    </div>
                @endif
            @else
                <div class="has-text-centered py-6">
                    <p class="subtitle has-text-grey">Майстрів не знайдено</p>
                </div>
            @endif
        </div>
    </section>
@endsection
