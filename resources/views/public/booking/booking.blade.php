@extends('layouts.public')

@section('title', 'Онлайн бронювання - Beauty Salon')

@section('meta')
    <meta name="description" content="Запишіться онлайн на послуги салону краси. Зручний вибір послуги, майстра та часу">
    <meta name="keywords" content="онлайн запис, бронювання, салон краси, запис на послуги">
    <meta property="og:title" content="Онлайн бронювання - Beauty Salon">
    <meta property="og:description" content="Запишіться онлайн на послуги салону краси">
    <meta property="og:type" content="website">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    svg {
        display: inline-block !important;
        vertical-align: middle !important;
        flex-shrink: 0 !important;
        max-width: 100% !important;
        height: auto !important;
    }
    
    #time-slots-container svg {
        max-width: 16px !important;
        max-height: 16px !important;
    }
    
    .flatpickr-calendar {
        font-family: inherit !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        border: 1px solid #e5e7eb !important;
        padding: 1rem !important;
        background: white !important;
    }
    
    .flatpickr-calendar.inline {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        display: block !important;
    }
    
    #appointment_date_input.flatpickr-input {
        display: none !important;
    }
    
    .flatpickr-months {
        margin-bottom: 1rem !important;
    }
    
    .flatpickr-month {
        color: #9333ea !important;
    }
    
    .flatpickr-current-month {
        font-size: 1.125rem !important;
        font-weight: 600 !important;
        color: #9333ea !important;
    }
    
    .flatpickr-weekdays {
        background: #f9fafb !important;
        padding: 0.5rem 0 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .flatpickr-weekday {
        color: #6b7280 !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
    }
    
    .flatpickr-day {
        border-radius: 0.5rem !important;
        margin: 0.125rem !important;
        height: 2.5rem !important;
        line-height: 2.5rem !important;
        font-size: 0.875rem !important;
        transition: all 0.2s !important;
    }
    
    .flatpickr-day:hover {
        background: #f3e8ff !important;
        border-color: #9333ea !important;
    }
    
    .flatpickr-day.selected {
        background: #9333ea !important;
        border-color: #9333ea !important;
        color: white !important;
        font-weight: 600 !important;
    }
    
    .flatpickr-day.selected:hover {
        background: #7e22ce !important;
        border-color: #7e22ce !important;
    }
    
    .flatpickr-day.today {
        border-color: #9333ea !important;
        font-weight: 600 !important;
    }
    
    .flatpickr-day.today:hover {
        background: #f3e8ff !important;
    }
    
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: #d1d5db !important;
        cursor: not-allowed !important;
    }
    
    .flatpickr-prev-month,
    .flatpickr-next-month {
        color: #9333ea !important;
        fill: #9333ea !important;
    }
    
    .flatpickr-prev-month:hover,
    .flatpickr-next-month:hover {
        color: #7e22ce !important;
        fill: #7e22ce !important;
    }
    
    .time-slot {
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .time-slot:hover {
        border-color: #9333ea !important;
        background-color: #f3e8ff !important;
    }
    
    .time-slot.selected {
        background-color: #9333ea !important;
        color: white !important;
        border-color: #9333ea !important;
    }
</style>
@endpush

@section('content')
    <section class="section">
        <div class="container">
            <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
                <ul>
                    <li><a href="{{ route('landing') }}">Головна</a></li>
                    <li class="is-active"><a href="#" aria-current="page">Онлайн бронювання</a></li>
                </ul>
            </nav>

            <div class="columns is-centered">
                <div class="column is-8">
                    <div class="box">
                        <h1 class="title is-3 has-text-primary has-text-centered mb-5">📅 Онлайн бронювання</h1>

                        @if(session('success'))
                            <div class="notification is-success is-light mb-5">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                <strong>Успіх!</strong>
                                <p>{{ session('success') }}</p>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="notification is-danger is-light mb-5">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                <strong>Помилка валідації:</strong>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="notification is-danger is-light mb-5">
                                <button class="delete" onclick="this.parentElement.remove()"></button>
                                <strong>Помилка!</strong>
                                <p>{{ session('error') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.booking.store') }}" id="booking-form">
                            @csrf

                            <!-- Вибір послуги -->
                            <div class="box has-background-light mb-5">
                                <h3 class="title is-5 has-text-primary mb-4">1. Оберіть послугу</h3>
                                <div class="field">
                                    <label class="label">Послуга <span class="has-text-danger">*</span></label>
                                    <div class="control">
                                        <div class="select is-fullwidth">
                                            <select id="service_id" name="service_id" required>
                                                <option value="">-- Оберіть послугу --</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" {{ old('service_id', $serviceId) == $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }} ({{ $service->category->name }}) - {{ number_format($service->price, 0) }} грн
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @error('service_id')
                                        <p class="help is-danger">{{ $message }}</p>
                                    @enderror
                                    <div id="service-info" class="box is-hidden mt-3">
                                        <strong class="has-text-primary">Тривалість:</strong> <span id="service-duration"></span> хвилин<br>
                                        <strong class="has-text-primary">Ціна:</strong> <span id="service-price"></span> грн
                                    </div>
                                </div>
                            </div>

                            <!-- Вибір майстра -->
                            <div class="box has-background-light mb-5">
                                <h3 class="title is-5 has-text-primary mb-4">2. Оберіть майстра</h3>
                                <div class="field">
                                    <label class="label">Майстер <span class="has-text-danger">*</span></label>
                                    <div class="control">
                                        <div class="select is-fullwidth">
                                            <select id="employee_id" name="employee_id" required>
                                                <option value="">-- Спочатку оберіть послугу --</option>
                                                @if($employees)
                                                    @foreach($employees as $employee)
                                                        <option value="{{ $employee->id }}" {{ old('employee_id', $employeeId) == $employee->id ? 'selected' : '' }}>
                                                            {{ $employee->user->name }}@if($employee->specialization) - {{ $employee->specialization }}@endif (⭐ {{ number_format($employee->rating, 1) }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    @error('employee_id')
                                        <p class="help is-danger">{{ $message }}</p>
                                    @enderror
                                    <div id="employee-info" class="box is-hidden mt-3">
                                        <strong class="has-text-primary">Рейтинг:</strong> <span id="employee-rating"></span><br>
                                        <strong class="has-text-primary">Спеціалізація:</strong> <span id="employee-specialization"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Вибір дати та часу -->
                            <div class="box has-background-light mb-5">
                                <h3 class="title is-5 has-text-primary mb-5">3. Оберіть дату та час</h3>
                                
                                <div class="field mb-5">
                                    <label class="label">Дата запису <span class="has-text-danger">*</span></label>
                                    <div class="columns">
                                        <div class="column is-two-thirds">
                                            <input type="text" id="appointment_date_input" style="display: none;">
                                            <input type="hidden" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required>
                                        </div>
                                        
                                        <div class="column is-one-third">
                                            <div id="selected-date-info" class="box is-hidden">
                                                <div class="level mb-3">
                                                    <div class="level-left">
                                                        <span class="icon has-text-success">✓</span>
                                                        <span class="has-text-weight-semibold">Обрана дата</span>
                                                    </div>
                                                </div>
                                                <p class="title is-4 has-text-primary mb-1" id="selected-day"></p>
                                                <p class="is-size-7 has-text-grey mb-3" id="selected-date-full"></p>
                                                <div class="is-size-7 has-text-grey" style="border-top: 1px solid #dbdbdb; padding-top: 0.75rem;">
                                                    <p>Мінімальна дата: сьогодні</p>
                                                    <p>Максимальна дата: {{ date('d.m.Y', strtotime('+3 months')) }}</p>
                                                </div>
                                            </div>
                                            <div id="no-date-selected" class="box has-background-grey-lighter has-text-centered">
                                                <p class="is-size-7 has-text-grey">Оберіть дату в календарі</p>
                                            </div>
                                        </div>
                                    </div>
                                    @error('appointment_date')
                                        <p class="help is-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="field">
                                    <label class="label">Час <span class="has-text-danger">*</span></label>
                                    <div id="time-slots-container" style="min-height: 4rem;">
                                        <div class="box has-background-grey-lighter has-text-centered">
                                            <p class="is-size-7 has-text-grey">Оберіть послугу, майстра та дату для перегляду доступного часу</p>
                                        </div>
                                    </div>
                                    <input type="hidden" id="appointment_time" name="appointment_time" value="{{ old('appointment_time') }}" required>
                                    @error('appointment_time')
                                        <p class="help is-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Контактні дані (для неавторизованих) -->
                            <div class="box has-background-light mb-5 {{ auth()->check() ? 'is-hidden' : '' }} guest-fields">
                                <h3 class="title is-5 has-text-primary mb-4">4. Ваші контактні дані</h3>
                                <div class="field">
                                    <label class="label">Ім'я <span class="has-text-danger">*</span></label>
                                    <div class="control">
                                        <input class="input" type="text" id="name" name="name" value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" {{ !auth()->check() || !auth()->user()->client ? 'required' : '' }}>
                                    </div>
                                    @error('name')
                                        <p class="help is-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="field">
                                    <label class="label">Email <span class="has-text-danger">*</span></label>
                                    <div class="control">
                                        <input class="input" type="email" id="email" name="email" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" {{ !auth()->check() || !auth()->user()->client ? 'required' : '' }}>
                                    </div>
                                    @error('email')
                                        <p class="help is-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="field">
                                    <label class="label">Телефон <span class="has-text-danger">*</span></label>
                                    <div class="control">
                                        <input class="input" type="tel" id="phone" name="phone" value="{{ old('phone', auth()->check() && auth()->user()->client ? auth()->user()->client->phone : '') }}" placeholder="+380XXXXXXXXX" {{ !auth()->check() || !auth()->user()->client ? 'required' : '' }}>
                                    </div>
                                    @error('phone')
                                        <p class="help is-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            @auth
                                @if(auth()->user()->client)
                                    <input type="hidden" name="client_id" value="{{ auth()->user()->client->id }}">
                                @endif
                            @endauth

                            <!-- Коментар -->
                            <div class="box has-background-light mb-5">
                                <h3 class="title is-5 has-text-primary mb-4">5. Додаткові побажання (необов'язково)</h3>
                                <div class="field">
                                    <label class="label">Коментар</label>
                                    <div class="control">
                                        <textarea class="textarea" id="notes" name="notes" placeholder="Ваші побажання або примітки..." style="min-height: 100px;">{{ old('notes') }}</textarea>
                                    </div>
                                    @error('notes')
                                        <p class="help is-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="button is-primary is-fullwidth is-large" id="submit-btn">Забронювати</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/uk.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('service_id');
            const employeeSelect = document.getElementById('employee_id');
            const dateInput = document.getElementById('appointment_date');
            const timeSlotsContainer = document.getElementById('time-slots-container');
            const timeInput = document.getElementById('appointment_time');
            const submitBtn = document.getElementById('submit-btn');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let selectedTime = null;

            // Отримати інформацію про послугу
            const services = @json($services->keyBy('id'));
            
            serviceSelect.addEventListener('change', function() {
                const serviceId = this.value;
                if (serviceId) {
                    const service = services[serviceId];
                    if (service) {
                        document.getElementById('service-duration').textContent = service.duration;
                        document.getElementById('service-price').textContent = service.price;
                        document.getElementById('service-info').classList.remove('is-hidden');
                    }
                    loadEmployees(serviceId);
                } else {
                    document.getElementById('service-info').classList.add('is-hidden');
                    employeeSelect.innerHTML = '<option value="">-- Спочатку оберіть послугу --</option>';
                    timeSlotsContainer.innerHTML = '<div class="box has-background-grey-lighter has-text-centered"><p class="is-size-7 has-text-grey">Оберіть послугу, майстра та дату для перегляду доступного часу</p></div>';
                }
                resetTimeSelection();
            });

            // Отримати інформацію про майстра (для початкового стану, якщо є)
            let employeesData = @json($employees ? $employees->keyBy('id') : []);
            
            // Оновлювати дані про майстрів при завантаженні через API
            function updateEmployeesData(data) {
                employeesData = {};
                if (data && Array.isArray(data)) {
                    data.forEach(emp => {
                        employeesData[emp.id] = emp;
                    });
                }
            }
            
            employeeSelect.addEventListener('change', function() {
                const employeeId = this.value;
                if (employeeId && employeesData[employeeId]) {
                    const employee = employeesData[employeeId];
                    document.getElementById('employee-rating').textContent = '⭐ ' + (employee.rating || '0.0');
                    document.getElementById('employee-specialization').textContent = employee.specialization || 'Не вказано';
                    document.getElementById('employee-info').classList.remove('is-hidden');
                } else {
                    document.getElementById('employee-info').classList.add('is-hidden');
                }
                if (dateInput.value) {
                    loadAvailableTime();
                }
                resetTimeSelection();
            });

            // Ініціалізація Flatpickr календаря в inline режимі
            const dateInputDisplay = document.getElementById('appointment_date_input');
            const selectedDateInfo = document.getElementById('selected-date-info');
            const noDateSelected = document.getElementById('no-date-selected');
            const selectedDay = document.getElementById('selected-day');
            const selectedDateFull = document.getElementById('selected-date-full');
            
            const flatpickrInstance = flatpickr(dateInputDisplay, {
                inline: true,
                dateFormat: "Y-m-d",
                minDate: "today",
                maxDate: new Date().fp_incr(90), // +3 місяці (90 днів)
                locale: flatpickr.l10ns.uk, // Використовувати українську локалізацію
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr && selectedDates.length > 0) {
                        // Оновити hidden input
                        dateInput.value = dateStr;
                        
                        // Показати інформацію про обрану дату
                        const selectedDate = selectedDates[0];
                        const day = selectedDate.getDate();
                        const month = selectedDate.toLocaleDateString('uk-UA', { month: 'long' });
                        const year = selectedDate.getFullYear();
                        const weekday = selectedDate.toLocaleDateString('uk-UA', { weekday: 'long' });
                        
                        selectedDay.textContent = day;
                        selectedDateFull.textContent = `${weekday}, ${day} ${month} ${year} р.`;
                        
                        selectedDateInfo.classList.remove('is-hidden');
                        noDateSelected.classList.add('is-hidden');
                        
                        // Завантажити доступний час якщо вибрано послугу та майстра
                        if (employeeSelect.value && serviceSelect.value) {
                            loadAvailableTime();
                        }
                        resetTimeSelection();
                    } else {
                        dateInput.value = '';
                        selectedDateInfo.classList.add('is-hidden');
                        noDateSelected.classList.remove('is-hidden');
                    }
                }
            });
            
            // Встановити дату при завантаженні, якщо вона є
            if (dateInput.value) {
                flatpickrInstance.setDate(dateInput.value, false);
                const selectedDate = new Date(dateInput.value + 'T00:00:00');
                const day = selectedDate.getDate();
                const month = selectedDate.toLocaleDateString('uk-UA', { month: 'long' });
                const year = selectedDate.getFullYear();
                const weekday = selectedDate.toLocaleDateString('uk-UA', { weekday: 'long' });
                
                selectedDay.textContent = day;
                selectedDateFull.textContent = `${weekday}, ${day} ${month} ${year} р.`;
                
                selectedDateInfo.classList.remove('is-hidden');
                noDateSelected.classList.add('is-hidden');
            }

            function loadEmployees(serviceId) {
                if (!serviceId) {
                    employeeSelect.innerHTML = '<option value="">-- Спочатку оберіть послугу --</option>';
                    return;
                }

                employeeSelect.innerHTML = '<option value="">Завантаження...</option>';
                employeeSelect.disabled = true;

                fetch(`/api/services/${serviceId}/employees`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    employeeSelect.disabled = false;
                    employeeSelect.innerHTML = '<option value="">-- Оберіть майстра --</option>';
                    
                    if (data && Array.isArray(data) && data.length > 0) {
                        // Оновити дані про майстрів
                        updateEmployeesData(data);
                        
                        data.forEach(employee => {
                            const option = document.createElement('option');
                            option.value = employee.id;
                            const specialization = employee.specialization ? ' - ' + employee.specialization : '';
                            const rating = employee.rating ? ' (⭐ ' + employee.rating + ')' : '';
                            option.textContent = employee.name + specialization + rating;
                            employeeSelect.appendChild(option);
                        });
                    } else {
                        employeeSelect.innerHTML = '<option value="">Немає доступних майстрів для цієї послуги</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading employees:', error);
                    employeeSelect.disabled = false;
                    employeeSelect.innerHTML = '<option value="">Помилка завантаження майстрів</option>';
                });
            }

            function loadAvailableTime() {
                const employeeId = employeeSelect.value;
                const serviceId = serviceSelect.value;
                const date = dateInput.value;

                if (!employeeId || !serviceId || !date) {
                    return;
                }

                timeSlotsContainer.innerHTML = '<div class="box has-text-centered"><p class="has-text-primary">Завантаження доступного часу...</p></div>';
                submitBtn.disabled = true;

                const params = new URLSearchParams({
                    employee_id: employeeId,
                    service_id: serviceId,
                    date: date
                });

                fetch(`/api/available-time?${params}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || err.message || 'Помилка при завантаженні доступного часу');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        timeSlotsContainer.innerHTML = `<div class="notification is-danger is-light">${data.error}</div>`;
                        submitBtn.disabled = false;
                        return;
                    }

                    if (!data.available_slots || data.available_slots.length === 0) {
                        const message = data.message || 'На цю дату немає доступного часу. Оберіть іншу дату.';
                        timeSlotsContainer.innerHTML = `<div class="notification is-warning is-light">${message}</div>`;
                        submitBtn.disabled = false;
                        return;
                    }

                    const slotsHtml = '<div class="columns is-multiline is-mobile">' +
                        data.available_slots.map(slot => 
                            `<div class="column is-2-desktop is-3-tablet is-4-mobile">
                                <div class="box time-slot has-text-centered" data-time="${slot.time}" style="cursor: pointer;">
                                    ${slot.display}
                                </div>
                            </div>`
                        ).join('') +
                        '</div>';
                    
                    timeSlotsContainer.innerHTML = slotsHtml;

                    // Додати обробники подій для слотів
                    document.querySelectorAll('.time-slot').forEach(slot => {
                        slot.addEventListener('click', function() {
                            if (this.classList.contains('is-disabled')) {
                                return;
                            }
                            
                            // Зняти виділення з інших слотів
                            document.querySelectorAll('.time-slot').forEach(s => {
                                s.classList.remove('selected', 'has-background-primary', 'has-text-white');
                                s.classList.add('has-background-white');
                            });
                            
                            // Виділити обраний слот
                            this.classList.add('selected', 'has-background-primary', 'has-text-white');
                            this.classList.remove('has-background-white');
                            selectedTime = this.dataset.time;
                            timeInput.value = selectedTime;
                            submitBtn.disabled = false;
                        });
                    });
                })
                .catch(error => {
                    console.error('Error loading available time:', error);
                    const errorMessage = error.message || 'Помилка при завантаженні доступного часу. Спробуйте ще раз.';
                    timeSlotsContainer.innerHTML = `<div class="notification is-danger is-light">${errorMessage}</div>`;
                    submitBtn.disabled = false;
                });
            }

            function resetTimeSelection() {
                selectedTime = null;
                timeInput.value = '';
                submitBtn.disabled = true;
            }

            // Ініціалізація при завантаженні сторінки
            if (serviceSelect.value) {
                serviceSelect.dispatchEvent(new Event('change'));
            }

            // Обробка відправки форми
            const bookingForm = document.getElementById('booking-form');
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(e) {
                    // Перевірити, що всі обов'язкові поля заповнені
                    if (!serviceSelect.value || !employeeSelect.value || !dateInput.value || !timeInput.value) {
                        e.preventDefault();
                        alert('Будь ласка, заповніть всі обов\'язкові поля перед відправкою форми.');
                        return false;
                    }

                    // Показати індикатор завантаження
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="icon"><i class="fas fa-spinner fa-spin"></i></span><span>Обробка...</span>';
                });
            }
        });
    </script>
@endpush
