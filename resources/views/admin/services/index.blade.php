@extends('layouts.app')

@section('title', 'Послуги - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li class="is-active"><a href="#" aria-current="page">Послуги</a></li>
            </ul>
        </nav>

        <div class="box">
            <div class="level mb-5">
                <div class="level-left">
                    <h1 class="title is-3 has-text-primary m-0">💅 Послуги</h1>
                </div>
                <div class="level-right">
                    <a href="{{ route('services.create') }}" class="button is-primary">+ Додати послугу</a>
                </div>
            </div>

            @if(session('success'))
                <div class="notification is-success is-light mb-5">
                    <button class="delete" onclick="this.parentElement.remove()"></button>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="notification is-danger is-light mb-5">
                    <button class="delete" onclick="this.parentElement.remove()"></button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="box has-background-light mb-5">
                <form method="GET" action="{{ route('services.index') }}">
                    <div class="columns is-multiline">
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Пошук</label>
                                <div class="control">
                                    <input class="input" type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Назва, опис...">
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Категорія</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="category_id" name="category_id">
                                            <option value="">Всі</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Статус</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="is_active" name="is_active">
                                            <option value="">Всі</option>
                                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Активні</option>
                                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Неактивні</option>
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

            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Назва</th>
                            <th>Категорія</th>
                            <th>Ціна</th>
                            <th>Тривалість</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>{{ $service->id }}</td>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->category->name }}</td>
                                <td>{{ number_format($service->price, 2) }} грн</td>
                                <td>{{ $service->duration }} хв</td>
                                <td>
                                    <span class="tag {{ $service->is_active ? 'is-success' : 'is-danger' }}">
                                        {{ $service->is_active ? 'Активна' : 'Неактивна' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="buttons">
                                        <a href="{{ route('services.show', $service) }}" class="button is-small is-success">Переглянути</a>
                                        <a href="{{ route('services.edit', $service) }}" class="button is-small is-primary">Редагувати</a>
                                        <form method="POST" action="{{ route('services.destroy', $service) }}" class="is-inline" onsubmit="return confirm('Видалити послугу?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="button is-small is-danger">Видалити</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="has-text-centered py-5">Послуг не знайдено</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($services->hasPages())
                <div class="has-text-centered" style="margin-top: 3rem;">
                    {{ $services->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
