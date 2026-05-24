@extends('layouts.app')

@section('title', 'Редагувати клієнта - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('clients.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <h1 class="text-3xl font-bold text-purple-600 mb-8">✏️ Редагувати клієнта: {{ $client->user->name ?? 'N/A' }}</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('clients.update', $client) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="user_id" class="block mb-2 text-gray-700 font-medium">Користувач *</label>
                <select id="user_id" name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="">Оберіть користувача</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (old('user_id', $client->user_id) == $user->id) ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="phone" class="block mb-2 text-gray-700 font-medium">Телефон</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $client->phone) }}" placeholder="+380XXXXXXXXX" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('phone')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="email" class="block mb-2 text-gray-700 font-medium">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}" placeholder="email@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('email')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="date_of_birth" class="block mb-2 text-gray-700 font-medium">Дата народження</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $client->date_of_birth?->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('date_of_birth')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="gender" class="block mb-2 text-gray-700 font-medium">Стать</label>
                <select id="gender" name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="">Не вказано</option>
                    <option value="male" {{ old('gender', $client->gender) == 'male' ? 'selected' : '' }}>Чоловік</option>
                    <option value="female" {{ old('gender', $client->gender) == 'female' ? 'selected' : '' }}>Жінка</option>
                    <option value="other" {{ old('gender', $client->gender) == 'other' ? 'selected' : '' }}>Інше</option>
                </select>
                @error('gender')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="address" class="block mb-2 text-gray-700 font-medium">Адреса</label>
                <textarea id="address" name="address" placeholder="Повна адреса клієнта" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white resize-y min-h-[100px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('address', $client->address) }}</textarea>
                @error('address')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block mb-2 text-gray-700 font-medium">Примітки</label>
                <textarea id="notes" name="notes" placeholder="Додаткові примітки про клієнта" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white resize-y min-h-[100px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('notes', $client->notes) }}</textarea>
                @error('notes')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="status" class="block mb-2 text-gray-700 font-medium">Статус</label>
                <select id="status" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Активний</option>
                    <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Неактивний</option>
                </select>
                @error('status')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="photo" class="block mb-2 text-gray-700 font-medium">Фото клієнта</label>
                @if($client->photo)
                    <div class="mb-2">
                        <img src="{{ Storage::url($client->photo) }}" alt="Фото клієнта" class="max-w-[200px] max-h-[200px] rounded-lg border border-gray-300">
                    </div>
                    <label class="flex items-center gap-2 mb-2">
                        <input type="checkbox" name="remove_photo" value="1" class="rounded">
                        <span class="text-gray-700">Видалити поточне фото</span>
                    </label>
                @endif
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                <small class="text-gray-600 text-xs block mt-1">Формати: JPEG, PNG, JPG, GIF. Максимум 5MB</small>
                @error('photo')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Оновити</button>
                <a href="{{ route('clients.show', $client) }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>
@endsection

