@extends('layouts.app')

@section('title', 'Відгуки - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li class="is-active"><a href="#" aria-current="page">Відгуки</a></li>
            </ul>
        </nav>

        <div class="box">
            <h1 class="title is-3 has-text-primary mb-5">⭐ Відгуки</h1>

            <div class="box has-background-light mb-5">
                <form method="GET" action="{{ route('reviews.index') }}">
                    <div class="columns is-multiline">
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Пошук</label>
                                <div class="control">
                                    <input class="input" type="text" name="search" placeholder="Пошук..." value="{{ request('search') }}">
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Статус</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="is_approved">
                                            <option value="">Всі статуси</option>
                                            <option value="1" {{ request('is_approved') == '1' ? 'selected' : '' }}>Схвалені</option>
                                            <option value="0" {{ request('is_approved') == '0' ? 'selected' : '' }}>Очікують модерації</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Рейтинг</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="rating">
                                            <option value="">Всі рейтинги</option>
                                            @for($i = 5; $i >= 1; $i--)
                                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} зірок</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field is-grouped" style="align-items: flex-end;">
                                <div class="control">
                                    <button type="submit" class="button is-primary">Фільтрувати</button>
                                </div>
                                <div class="control">
                                    <a href="{{ route('reviews.index') }}" class="button is-light">Скинути</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if($reviews->count() > 0)
                <div class="table-container">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Клієнт</th>
                                <th>Майстер</th>
                                <th>Послуга</th>
                                <th>Рейтинг</th>
                                <th>Коментар</th>
                                <th>Статус</th>
                                <th>Дата</th>
                                <th>Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $review)
                                <tr>
                                    <td>{{ $review->id }}</td>
                                    <td>{{ $review->client->user->name }}</td>
                                    <td>{{ $review->employee->user->name }}</td>
                                    <td>{{ $review->service->name }}</td>
                                    <td>
                                        <span class="has-text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="icon is-small {{ $i <= $review->rating ? 'has-text-warning' : 'has-text-grey-light' }}">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                            @endfor
                                        </span>
                                        <span class="has-text-grey">({{ $review->rating }}/5)</span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($review->comment, 50) ?: '-' }}</td>
                                    <td>
                                        <span class="tag {{ $review->is_approved ? 'is-success' : 'is-warning' }} is-light">
                                            {{ $review->is_approved ? 'Схвалено' : 'Очікує модерації' }}
                                        </span>
                                    </td>
                                    <td>{{ $review->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <div class="buttons">
                                            <a href="{{ route('reviews.show', $review) }}" class="button is-small is-info">Переглянути</a>
                                            @if(!$review->is_approved)
                                                <form method="POST" action="{{ route('reviews.approve', $review) }}" class="is-inline">
                                                    @csrf
                                                    <button type="submit" class="button is-small is-success">Схвалити</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="is-inline" onsubmit="return confirm('Ви впевнені?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button is-small is-danger">Видалити</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="notification is-info is-light">
                    Відгуків не знайдено.
                </div>
            @endif
        </div>
    </div>
@endsection

