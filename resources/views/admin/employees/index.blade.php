@extends('layouts.app')

@section('title', 'Майстри - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li class="is-active"><a href="#" aria-current="page">Майстри</a></li>
            </ul>
        </nav>

        <div class="box">
            <div class="level mb-5">
                <div class="level-left">
                    <h1 class="title is-3 has-text-primary m-0">👨‍💼 Майстри</h1>
                </div>
                <div class="level-right">
                    <a href="{{ route('employees.create') }}" class="button is-primary">+ Додати майстра</a>
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

            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Фото</th>
                            <th>Ім'я</th>
                            <th>Спеціалізація</th>
                            <th>Рейтинг</th>
                            <th>Послуг</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->id }}</td>
                                <td>
                                    <figure class="image is-48x48">
                                        <img class="is-rounded" src="{{ \App\Helpers\ImageHelper::getEmployeePhoto($employee) }}" alt="{{ $employee->user->name }}">
                                    </figure>
                                </td>
                                <td>{{ $employee->user->name ?? 'N/A' }}</td>
                                <td>{{ $employee->specialization ?? 'N/A' }}</td>
                                <td><span class="has-text-warning has-text-weight-semibold">⭐ {{ number_format($employee->rating, 1) }}</span></td>
                                <td>{{ $employee->services->count() }}</td>
                                <td>
                                    <span class="tag 
                                        @if($employee->status == 'active') is-success
                                        @elseif($employee->status == 'inactive') is-danger
                                        @else is-warning
                                        @endif">
                                        @if($employee->status == 'active') Активний
                                        @elseif($employee->status == 'inactive') Неактивний
                                        @else У відпустці
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="buttons">
                                        <a href="{{ route('employees.show', $employee) }}" class="button is-small is-success">Переглянути</a>
                                        <a href="{{ route('employees.edit', $employee) }}" class="button is-small is-primary">Редагувати</a>
                                        <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="is-inline" onsubmit="return confirm('Видалити майстра?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="button is-small is-danger">Видалити</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="has-text-centered py-5">Майстрів не знайдено</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
                <div class="has-text-centered" style="margin-top: 3rem;">
                    {{ $employees->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
