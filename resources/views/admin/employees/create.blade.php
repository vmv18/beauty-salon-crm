@extends('layouts.app')

@section('title', 'Додати майстра - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('employees.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад</a>
        <h1 class="text-3xl font-bold text-purple-600 mb-8">➕ Додати майстра</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <label for="user_id" class="block mb-2 text-gray-700 font-medium">Користувач *</label>
                <select id="user_id" name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="">Оберіть користувача</option>
                    @foreach($usersWithoutEmployee as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="specialization" class="block mb-2 text-gray-700 font-medium">Спеціалізація</label>
                <input type="text" id="specialization" name="specialization" value="{{ old('specialization') }}" placeholder="Наприклад: Перукар, Косметолог" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('specialization')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="bio" class="block mb-2 text-gray-700 font-medium">Біографія</label>
                <textarea id="bio" name="bio" placeholder="Опис майстра, досвід, навички..." class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white resize-y min-h-[100px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('bio') }}</textarea>
                @error('bio')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="photo" class="block mb-2 text-gray-700 font-medium">Фото</label>
                <input type="file" id="photo" name="photo" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('photo')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="rating" class="block mb-2 text-gray-700 font-medium">Рейтинг (0-5)</label>
                <input type="number" id="rating" name="rating" value="{{ old('rating', 0) }}" step="0.01" min="0" max="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('rating')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="hire_date" class="block mb-2 text-gray-700 font-medium">Дата найму</label>
                <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                @error('hire_date')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label for="status" class="block mb-2 text-gray-700 font-medium">Статус</label>
                <select id="status" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Активний</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Неактивний</option>
                    <option value="on_leave" {{ old('status') == 'on_leave' ? 'selected' : '' }}>У відпустці</option>
                </select>
                @error('status')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-6">
                <label class="block mb-2 text-gray-700 font-medium">Послуги</label>
                <div class="flex flex-wrap gap-4 mt-2">
                    @foreach($services as $service)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="service_{{ $service->id }}" name="services[]" value="{{ $service->id }}" {{ in_array($service->id, old('services', [])) ? 'checked' : '' }} class="rounded">
                            <label for="service_{{ $service->id }}" class="text-gray-700 text-sm">{{ $service->name }} ({{ $service->category->name }})</label>
                        </div>
                    @endforeach
                </div>
                @error('services')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Зберегти</button>
                <a href="{{ route('employees.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>
@endsection

