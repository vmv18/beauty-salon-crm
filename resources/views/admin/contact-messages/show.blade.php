@extends('layouts.app')

@section('title', 'Повідомлення - Beauty Salon CRM')

@section('content')
    <div class="bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('admin.contact-messages.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📧 Повідомлення від {{ $contactMessage->name }}</h1>
            <div class="flex gap-2">
                @if(!$contactMessage->is_read)
                    <form action="{{ route('admin.contact-messages.mark-as-read', $contactMessage) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline hover:bg-gray-700 transition">
                            Позначити як прочитане
                        </button>
                    </form>
                @endif
                <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST" class="inline"
                      onsubmit="return confirm('Ви впевнені, що хочете видалити це повідомлення?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg no-underline hover:bg-red-700 transition">
                        Видалити
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <x-alerts.success :message="session('success')" />
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="text-sm text-gray-600 mb-1">Ім'я</div>
                <div class="text-lg font-semibold text-gray-900">{{ $contactMessage->name }}</div>
            </div>
            
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="text-sm text-gray-600 mb-1">Email</div>
                <div class="text-lg">
                    <a href="mailto:{{ $contactMessage->email }}" class="text-purple-600 hover:underline no-underline font-semibold">
                        {{ $contactMessage->email }}
                    </a>
                </div>
            </div>
            
            @if($contactMessage->phone)
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="text-sm text-gray-600 mb-1">Телефон</div>
                <div class="text-lg">
                    <a href="tel:{{ $contactMessage->phone }}" class="text-gray-900 hover:text-purple-600 no-underline font-semibold">
                        {{ $contactMessage->phone }}
                    </a>
                </div>
            </div>
            @endif
            
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="text-sm text-gray-600 mb-1">Дата та час</div>
                <div class="text-lg font-semibold text-gray-900">
                    {{ $contactMessage->created_at->format('d.m.Y H:i') }}
                </div>
            </div>
            
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="text-sm text-gray-600 mb-1">Статус</div>
                <div class="text-lg">
                    @if($contactMessage->is_read)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Прочитано</span>
                        @if($contactMessage->read_at)
                            <div class="text-xs text-gray-500 mt-1">Прочитано: {{ $contactMessage->read_at->format('d.m.Y H:i') }}</div>
                        @endif
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">Нове</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-purple-50 p-6 rounded-lg border-l-4 border-purple-600">
            <div class="text-sm text-gray-600 mb-2 font-semibold">Повідомлення</div>
            <div class="text-gray-900 whitespace-pre-wrap">{{ $contactMessage->message }}</div>
        </div>

        <div class="mt-6 flex gap-4">
            <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ $contactMessage->name }}" 
               class="px-6 py-3 bg-purple-600 text-white rounded-lg no-underline hover:bg-purple-700 transition inline-flex items-center gap-2">
                <span>✉️</span>
                <span>Відповісти на email</span>
            </a>
            @if($contactMessage->phone)
                <a href="tel:{{ $contactMessage->phone }}" 
                   class="px-6 py-3 bg-green-600 text-white rounded-lg no-underline hover:bg-green-700 transition inline-flex items-center gap-2">
                    <span>📞</span>
                    <span>Зателефонувати</span>
                </a>
            @endif
        </div>
    </div>
@endsection

