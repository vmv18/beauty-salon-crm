@extends('layouts.app')

@section('title', 'Імпорт клієнтів - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-3xl font-bold text-purple-600 mb-6">📤 Імпорт клієнтів</h1>

        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition mb-4 inline-block">← Назад до списку</a>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                {{ session('error') }}
            </div>
        @endif

        @if(session('import_errors'))
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg mb-6 border border-yellow-300">
                <strong>Помилки при імпорті:</strong>
                <div class="bg-red-50 p-4 rounded-lg mt-2 max-h-[300px] overflow-y-auto">
                    <ul class="list-disc list-inside">
                        @foreach(session('import_errors') as $error)
                            <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-blue-50 p-6 rounded-lg mb-6 border-l-4 border-purple-600">
            <h3 class="text-xl font-semibold text-purple-600 mb-4">📋 Інструкція по імпорту</h3>
            <ul class="list-disc list-inside ml-4 text-gray-700 leading-relaxed">
                <li>Файл повинен бути у форматі <strong>CSV</strong> з роздільником <strong>крапка з комою (;)</strong></li>
                <li>Перший рядок повинен містити заголовки колонок</li>
                <li>Кодування файлу: <strong>UTF-8</strong></li>
                <li>Максимальний розмір файлу: <strong>10MB</strong></li>
                <li>Якщо користувач з таким email вже існує, він буде використаний</li>
                <li>Якщо у користувача вже є профіль клієнта, рядок буде пропущено</li>
            </ul>
        </div>

        <div class="bg-blue-50 p-6 rounded-lg mb-6 border-l-4 border-purple-600">
            <h3 class="text-xl font-semibold text-purple-600 mb-4">📊 Формат файлу</h3>
            <p class="mb-4 text-gray-700">Файл повинен містити наступні колонки (в такому порядку):</p>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse mt-4">
                    <thead>
                        <tr>
                            <th class="p-2 text-left border border-gray-300 bg-gray-50 font-semibold text-sm">Колонка</th>
                            <th class="p-2 text-left border border-gray-300 bg-gray-50 font-semibold text-sm">Опис</th>
                            <th class="p-2 text-left border border-gray-300 bg-gray-50 font-semibold text-sm">Обов'язкове</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">ID</td>
                            <td class="p-2 border border-gray-300 text-sm">Ідентифікатор (буде проігноровано при імпорті)</td>
                            <td class="p-2 border border-gray-300 text-sm">Ні</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Ім'я</td>
                            <td class="p-2 border border-gray-300 text-sm">Повне ім'я клієнта</td>
                            <td class="p-2 border border-gray-300 text-sm">Так</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Email</td>
                            <td class="p-2 border border-gray-300 text-sm">Email адреса (унікальна)</td>
                            <td class="p-2 border border-gray-300 text-sm">Так</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Телефон</td>
                            <td class="p-2 border border-gray-300 text-sm">Номер телефону</td>
                            <td class="p-2 border border-gray-300 text-sm">Ні</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Дата народження</td>
                            <td class="p-2 border border-gray-300 text-sm">Формат: дд.мм.рррр або рррр-мм-дд</td>
                            <td class="p-2 border border-gray-300 text-sm">Ні</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Стать</td>
                            <td class="p-2 border border-gray-300 text-sm">male, female або other</td>
                            <td class="p-2 border border-gray-300 text-sm">Ні</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Адреса</td>
                            <td class="p-2 border border-gray-300 text-sm">Повна адреса</td>
                            <td class="p-2 border border-gray-300 text-sm">Ні</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Статус</td>
                            <td class="p-2 border border-gray-300 text-sm">active або inactive (за замовчуванням: active)</td>
                            <td class="p-2 border border-gray-300 text-sm">Ні</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Примітки</td>
                            <td class="p-2 border border-gray-300 text-sm">Додаткова інформація</td>
                            <td class="p-2 border border-gray-300 text-sm">Ні</td>
                        </tr>
                        <tr>
                            <td class="p-2 border border-gray-300 text-sm">Дата створення</td>
                            <td class="p-2 border border-gray-300 text-sm">Буде проігноровано при імпорті</td>
                            <td class="p-2 border border-gray-300 text-sm">Ні</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <form method="POST" action="{{ route('clients.import') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label for="file" class="block font-semibold text-gray-700 mb-2">Виберіть CSV файл *</label>
                <input type="file" name="file" id="file" accept=".csv,.txt" required class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('file')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Завантажити та імпортувати</button>
                <a href="{{ route('clients.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>

        <div class="mt-8 pt-8 border-t-2 border-gray-200">
            <h3 class="text-xl font-semibold text-purple-600 mb-4">💡 Порада</h3>
            <p class="text-gray-700 leading-relaxed mb-4">
                Для створення файлу з правильним форматом, спочатку <strong>експортуйте</strong> поточних клієнтів, 
                щоб отримати приклад формату. Потім ви можете відредагувати файл та імпортувати його знову.
            </p>
            <a href="{{ route('clients.export') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition no-underline inline-block mt-4">
                📥 Завантажити приклад (експорт поточних клієнтів)
            </a>
        </div>
    </div>
@endsection

