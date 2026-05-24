@extends('layouts.app')

@section('title', 'Редагувати відгук - Beauty Salon CRM')

@push('styles')
    <style>
        .rating-input input[type="radio"] { display: none; }
        .rating-input label { cursor: pointer; font-size: 2rem; color: #ddd; transition: color 0.2s; }
        .rating-input input[type="radio"]:checked ~ label,
        .rating-input label:hover,
        .rating-input label:hover ~ label { color: #ffc107; }
        .rating-input input[type="radio"]:checked ~ label { color: #ffc107; }
    </style>
@endpush

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-3xl font-bold text-purple-600 mb-8">Редагувати відгук #{{ $review->id }}</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border border-red-300">
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('reviews.update', $review) }}">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block mb-2 text-gray-700 font-semibold">Рейтинг *</label>
                <div class="rating-input flex gap-2 items-center">
                    <input type="radio" name="rating" value="5" id="rating5" {{ old('rating', $review->rating) == 5 ? 'checked' : '' }} required>
                    <label for="rating5">★</label>
                    <input type="radio" name="rating" value="4" id="rating4" {{ old('rating', $review->rating) == 4 ? 'checked' : '' }}>
                    <label for="rating4">★</label>
                    <input type="radio" name="rating" value="3" id="rating3" {{ old('rating', $review->rating) == 3 ? 'checked' : '' }}>
                    <label for="rating3">★</label>
                    <input type="radio" name="rating" value="2" id="rating2" {{ old('rating', $review->rating) == 2 ? 'checked' : '' }}>
                    <label for="rating2">★</label>
                    <input type="radio" name="rating" value="1" id="rating1" {{ old('rating', $review->rating) == 1 ? 'checked' : '' }}>
                    <label for="rating1">★</label>
                </div>
                @error('rating')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="comment" class="block mb-2 text-gray-700 font-semibold">Коментар</label>
                <textarea name="comment" id="comment" placeholder="Залиште свій відгук про послугу та майстра..." class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm resize-y min-h-[150px] focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200">{{ old('comment', $review->comment) }}</textarea>
                @error('comment')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            @if(auth()->user()->hasRole(['admin', 'manager']))
            <div class="mb-6">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_approved" id="is_approved" value="1" {{ old('is_approved', $review->is_approved) ? 'checked' : '' }} class="w-auto">
                    <label for="is_approved" class="text-gray-700 font-semibold m-0">Схвалено</label>
                </div>
            </div>
            @endif

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Зберегти зміни</button>
                <a href="{{ route('reviews.show', $review) }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition no-underline">Скасувати</a>
            </div>
        </form>
    </div>
@endsection

