@extends('layouts.app')

@section('title', 'Контактні повідомлення - Beauty Salon CRM')

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li class="is-active"><a href="#" aria-current="page">Контактні повідомлення</a></li>
            </ul>
        </nav>

        <div class="box">
            <div class="level mb-5">
                <div class="level-left">
                    <h1 class="title is-3 has-text-primary m-0">📧 Контактні повідомлення</h1>
                </div>
                <div class="level-right">
                    <div class="content has-text-right">
                        <p class="has-text-grey">
                            Всього: <strong>{{ $messages->total() }}</strong> |
                            Непрочитаних: <strong class="has-text-danger">{{ $messages->where('is_read', false)->count() }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <x-alerts.success :message="session('success')" />
            @endif

            @if($messages->isEmpty())
                <div class="has-text-centered py-6">
                    <div class="is-size-1 mb-4">📭</div>
                    <p class="has-text-grey is-size-5">Повідомлень поки немає</p>
                </div>
            @else
                <div class="table-container">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Ім'я</th>
                                <th>Email</th>
                                <th>Телефон</th>
                                <th>Повідомлення</th>
                                <th>Статус</th>
                                <th>Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr class="{{ !$message->is_read ? 'has-background-info-light' : '' }}">
                                    <td>
                                        <div>{{ $message->created_at->format('d.m.Y') }}</div>
                                        <div class="has-text-grey is-size-7">{{ $message->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="has-text-weight-medium">{{ $message->name }}</td>
                                    <td>
                                        <a href="mailto:{{ $message->email }}" class="has-text-primary">
                                            {{ $message->email }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($message->phone)
                                            <a href="tel:{{ $message->phone }}" class="has-text-grey-dark">
                                                {{ $message->phone }}
                                            </a>
                                        @else
                                            <span class="has-text-grey-light">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="has-text-grey-dark is-size-7" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $message->message }}">
                                            {{ Str::limit($message->message, 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($message->is_read)
                                            <span class="tag is-success is-light">Прочитано</span>
                                        @else
                                            <span class="tag is-danger is-light">Нове</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="buttons">
                                            <a href="{{ route('admin.contact-messages.show', $message) }}" 
                                               class="button is-small is-primary">
                                                Переглянути
                                            </a>
                                            @if(!$message->is_read)
                                                <form action="{{ route('admin.contact-messages.mark-as-read', $message) }}" method="POST" class="is-inline">
                                                    @csrf
                                                    <button type="submit" class="button is-small is-light">
                                                        Прочитано
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" class="is-inline" 
                                                  onsubmit="return confirm('Ви впевнені, що хочете видалити це повідомлення?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button is-small is-danger">
                                                    Видалити
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

