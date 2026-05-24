@extends('layouts.app')

@section('title', 'Редагувати запис - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('appointments.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <h1 class="text-3xl font-bold text-purple-600 mb-8">✏️ Редагувати запис #{{ $appointment->id }}</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('appointments.update', $appointment) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="client_id" class="block mb-2 text-gray-700 font-medium">Клієнт *</label>
                    <select id="client_id" name="client_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                        <option value="">Оберіть клієнта</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $appointment->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->user->name }} ({{ $client->user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="employee_id" class="block mb-2 text-gray-700 font-medium">Майстер *</label>
                    <select id="employee_id" name="employee_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                        <option value="">Оберіть майстра</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $appointment->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="service_id" class="block mb-2 text-gray-700 font-medium">Послуга *</label>
                <select id="service_id" name="service_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="">Оберіть послугу</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} ({{ $service->category->name ?? '' }}) - {{ number_format($service->price, 0, ',', ' ') }} грн
                        </option>
                    @endforeach
                </select>
                @error('service_id')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="appointment_date" class="block mb-2 text-gray-700 font-medium">Дата запису *</label>
                    <input type="date" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    @error('appointment_date')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="appointment_time" class="block mb-2 text-gray-700 font-medium">Час запису *</label>
                    <input type="time" id="appointment_time" name="appointment_time" value="{{ old('appointment_time', $appointment->appointment_time ? substr($appointment->appointment_time, 0, 5) : '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    @error('appointment_time')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="status" class="block mb-2 text-gray-700 font-medium">Статус</label>
                <select id="status" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    <option value="scheduled" {{ old('status', $appointment->status) == 'scheduled' ? 'selected' : '' }}>Заплановано</option>
                    <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Підтверджено</option>
                    <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Завершено</option>
                    <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Скасовано</option>
                </select>
                @error('status')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block mb-2 text-gray-700 font-medium">Примітки</label>
                <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('notes', $appointment->notes) }}</textarea>
                @error('notes')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="photo_before" class="block mb-2 text-gray-700 font-medium">Фото до</label>
                    @if($appointment->photo_before)
                        <div class="mb-2">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($appointment->photo_before) }}" alt="Фото до" class="w-32 h-32 object-cover rounded-lg">
                        </div>
                        <div class="mb-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="remove_photo_before" value="1" class="mr-2">
                                <span class="text-sm text-gray-600">Видалити поточне фото</span>
                            </label>
                        </div>
                    @endif
                    <input type="file" id="photo_before" name="photo_before" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    @error('photo_before')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="photo_after" class="block mb-2 text-gray-700 font-medium">Фото після</label>
                    @if($appointment->photo_after)
                        <div class="mb-2">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($appointment->photo_after) }}" alt="Фото після" class="w-32 h-32 object-cover rounded-lg">
                        </div>
                        <div class="mb-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="remove_photo_after" value="1" class="mr-2">
                                <span class="text-sm text-gray-600">Видалити поточне фото</span>
                            </label>
                        </div>
                    @endif
                    <input type="file" id="photo_after" name="photo_after" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-white focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">
                    @error('photo_after')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Оновити запис</button>
                <a href="{{ route('appointments.show', $appointment) }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>
@endsection

