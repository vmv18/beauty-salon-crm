@extends('layouts.app')

@section('title', 'Клієнти - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li class="is-active"><a href="#" aria-current="page">Клієнти</a></li>
            </ul>
        </nav>

        <div class="box">
            <div class="level mb-5">
                <div class="level-left">
                    <h1 class="title is-3 has-text-primary m-0">👥 Управління клієнтами</h1>
                </div>
                <div class="level-right">
                    <div class="buttons">
                        <a href="{{ route('clients.create') }}" class="button is-primary">+ Додати клієнта</a>
                        <a href="{{ route('clients.export', request()->query()) }}" class="button is-success">📥 Експорт CSV</a>
                        <a href="{{ route('clients.import.show') }}" class="button is-primary">📤 Імпорт CSV</a>
                    </div>
                </div>
            </div>

            <div class="box has-background-light mb-5">
                <form method="GET" action="{{ route('clients.index') }}">
                    <div class="columns is-multiline">
                        <div class="column is-3">
                            <div class="field">
                                <label class="label">Пошук</label>
                                <div class="control">
                                    <input class="input" type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ім'я, email, телефон...">
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Статус</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="status" name="status">
                                            <option value="">Всі</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активні</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Неактивні</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Стать</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="gender" name="gender">
                                            <option value="">Всі</option>
                                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Чоловік</option>
                                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Жінка</option>
                                            <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Інше</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column is-2">
                            <div class="field">
                                <label class="label">Сортування</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="sort_by" name="sort_by">
                                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Дата створення</option>
                                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Ім'я</option>
                                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Статус</option>
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
                            <th>Ім'я</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Стать</th>
                            <th>Дата народження</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->user->name ?? 'N/A' }}</td>
                                <td>{{ $client->email ?? $client->user->email ?? 'N/A' }}</td>
                                <td>{{ $client->phone ?? 'N/A' }}</td>
                                <td>
                                    @if($client->gender)
                                        {{ $client->gender == 'male' ? 'Чоловік' : ($client->gender == 'female' ? 'Жінка' : 'Інше') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $client->date_of_birth ? $client->date_of_birth->format('d.m.Y') : 'N/A' }}</td>
                                <td>
                                    <span class="tag {{ $client->status == 'active' ? 'is-success' : 'is-danger' }}">
                                        {{ $client->status == 'active' ? 'Активний' : 'Неактивний' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="buttons">
                                        <a href="{{ route('clients.show', $client) }}" class="button is-small is-success">Переглянути</a>
                                        <a href="{{ route('clients.edit', $client) }}" class="button is-small is-primary">Редагувати</a>
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}" class="is-inline" onsubmit="return confirm('Ви впевнені, що хочете видалити цього клієнта?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button is-small is-danger">Видалити</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="has-text-centered py-5">
                                    Клієнтів не знайдено
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($clients->hasPages())
                <div class="has-text-centered" style="margin-top: 3rem;">
                    {{ $clients->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
