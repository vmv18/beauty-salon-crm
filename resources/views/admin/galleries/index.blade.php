@extends('layouts.app')

@section('title', 'Галерея - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('admin.dashboard') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до панелі</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📸 Галерея</h1>
            <a href="{{ route('galleries.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">+ Додати зображення</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-300">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">{{ session('error') }}</div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <form method="GET" action="{{ route('galleries.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="flex flex-col">
                    <label for="search" class="mb-2 text-gray-700 font-medium text-sm">Пошук</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Назва, опис..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div class="flex flex-col">
                    <label for="is_active" class="mb-2 text-gray-700 font-medium text-sm">Статус</label>
                    <select id="is_active" name="is_active" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Всі</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Активні</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Неактивні</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">Фільтрувати</button>
                </div>
            </form>
        </div>

        @if($galleries->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($galleries as $gallery)
                    <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow border border-gray-200">
                        <div class="relative aspect-square overflow-hidden bg-gray-100">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($gallery->image) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $gallery->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $gallery->is_active ? 'Активне' : 'Неактивне' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $gallery->title }}</h3>
                            @if($gallery->description)
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $gallery->description }}</p>
                            @endif
                            <div class="flex justify-between items-center text-xs text-gray-500 mb-4">
                                <span>Порядок: {{ $gallery->sort_order }}</span>
                                <span>{{ $gallery->created_at->format('d.m.Y') }}</span>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('galleries.show', $gallery) }}" class="flex-1 text-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium no-underline">Переглянути</a>
                                <a href="{{ route('galleries.edit', $gallery) }}" class="flex-1 text-center px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-medium no-underline">Редагувати</a>
                                <form action="{{ route('galleries.destroy', $gallery) }}" method="POST" class="inline" onsubmit="return confirm('Ви впевнені, що хочете видалити це зображення?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">Видалити</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $galleries->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-600 mb-4">Зображень поки немає</p>
                <a href="{{ route('galleries.create') }}" class="px-6 py-3 bg-purple-600 text-white rounded-lg no-underline font-medium hover:bg-purple-700 transition inline-block">Додати перше зображення</a>
            </div>
        @endif
    </div>
@endsection

