@extends('layouts.app')

@section('title', 'Календар записів - Beauty Salon CRM')

@push('styles')
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <style>
        /* Покращення видимості тексту в календарі */
        .fc {
            font-family: inherit;
        }
        
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            color: #1f2937 !important;
        }
        
        .fc-button {
            font-weight: 500 !important;
            padding: 0.5rem 1rem !important;
        }
        
        .fc-button-primary {
            background-color: #9333ea !important;
            border-color: #9333ea !important;
            color: white !important;
        }
        
        .fc-button-primary:hover {
            background-color: #7e22ce !important;
            border-color: #7e22ce !important;
        }
        
        .fc-button-primary:not(:disabled):active,
        .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #6b21a8 !important;
            border-color: #6b21a8 !important;
        }
        
        .fc-daygrid-day-number {
            font-weight: 500 !important;
            color: #374151 !important;
            padding: 0.5rem !important;
        }
        
        .fc-col-header-cell {
            font-weight: 600 !important;
            color: #1f2937 !important;
            padding: 0.75rem !important;
            background-color: #f9fafb !important;
        }
        
        .fc-col-header-cell-cushion {
            color: #1f2937 !important;
        }
        
        .fc-event {
            border-radius: 0.375rem !important;
            padding: 0 !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
            min-height: 60px !important;
            background-color: var(--fc-event-bg-color, #667eea) !important;
            border-color: var(--fc-event-border-color, #667eea) !important;
            color: #ffffff !important;
            overflow: hidden !important;
        }
        
        .fc-event-main {
            padding: 0 !important;
            overflow: hidden !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        .fc-event-main-frame {
            padding: 0 !important;
            overflow: hidden !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        .fc-event-title-container {
            padding: 0 !important;
            overflow: hidden !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        /* Кольори за статусами */
        .fc-event[data-status="scheduled"] {
            background-color: #9333ea !important;
            border-color: #9333ea !important;
        }
        
        .fc-event[data-status="confirmed"] {
            background-color: #16a34a !important;
            border-color: #16a34a !important;
        }
        
        .fc-event[data-status="completed"] {
            background-color: #0891b2 !important;
            border-color: #0891b2 !important;
        }
        
        .fc-event[data-status="cancelled"] {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
        }
        
        .fc-event-title {
            font-weight: 600 !important;
            color: white !important;
            padding: 0 !important;
            line-height: 1.4 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
        }
        
        .fc-event-title-container {
            padding: 0 !important;
        }
        
        .fc-event-time {
            font-weight: 700 !important;
            color: white !important;
            margin-right: 0.5rem !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
            font-size: 0.9rem !important;
        }
        
        .fc-daygrid-event {
            margin: 0.25rem 0 !important;
        }
        
        .fc-event-main {
            padding: 0 !important;
        }
        
        .fc-event-main-frame {
            padding: 0 !important;
        }
        
        .fc-day-today {
            background-color: #fef3c7 !important;
        }
        
        .fc-daygrid-day-frame {
            min-height: 120px !important;
        }
        
        .fc-daygrid-day-events {
            margin-top: 0.5rem !important;
        }
        
        .fc-scrollgrid {
            border-color: #e5e7eb !important;
        }
        
        .fc-scrollgrid-section-header td {
            border-color: #e5e7eb !important;
        }
        
        .fc-daygrid-day {
            border-color: #e5e7eb !important;
        }
        
        .fc-daygrid-day-top {
            padding: 0.5rem !important;
        }
        
        /* Покращення для мобільних пристроїв */
        @media (max-width: 768px) {
            .fc-toolbar-title {
                font-size: 1.25rem !important;
            }
            
            .fc-button {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.875rem !important;
            }
            
            .fc-event-title {
                font-size: 0.75rem !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <a href="{{ route('appointments.index') }}" class="text-purple-600 no-underline hover:underline mb-4 inline-block">← Назад до списку</a>
        
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-purple-600 m-0">📅 Календар записів</h1>
            <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">+ Новий запис</a>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <form id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="flex flex-col">
                    <label for="employee_id" class="mb-2 text-gray-700 font-medium text-sm">Майстер</label>
                    <select id="employee_id" name="employee_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Всі майстри</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="status" class="mb-2 text-gray-700 font-medium text-sm">Статус</label>
                    <select id="status" name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Всі статуси</option>
                        <option value="scheduled">Заплановано</option>
                        <option value="confirmed">Підтверджено</option>
                        <option value="completed">Виконано</option>
                        <option value="cancelled">Скасовано</option>
                    </select>
                </div>
                <div>
                    <button type="button" onclick="applyFilters()" class="w-full px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">Застосувати фільтри</button>
                </div>
            </form>
            
            <div class="flex gap-4 mt-4 flex-wrap">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-purple-600"></div>
                    <span class="text-sm text-gray-700">Заплановано</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-green-600"></div>
                    <span class="text-sm text-gray-700">Підтверджено</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-cyan-600"></div>
                    <span class="text-sm text-gray-700">Виконано</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-red-600"></div>
                    <span class="text-sm text-gray-700">Скасовано</span>
                </div>
            </div>
        </div>

        <div id="calendar" class="mt-4"></div>
    </div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            let currentFilters = {
                employee_id: '',
                status: ''
            };

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                locale: 'uk',
                firstDay: 1, // Понеділок
                height: 'auto',
                editable: true,
                droppable: false,
                eventResize: true,
                eventDrop: function(info) {
                    updateAppointmentTime(info.event, info);
                },
                eventResize: function(info) {
                    updateAppointmentTime(info.event, info);
                },
                eventClick: function(info) {
                    window.location.href = `/appointments/${info.event.id}`;
                },
                eventDidMount: function(info) {
                    // Додати data-атрибут для статусу для CSS селекторів
                    const status = info.event.extendedProps.status;
                    if (status) {
                        info.el.setAttribute('data-status', status);
                    }
                    
                    // Переконатися, що колір застосовано
                    if (info.event.backgroundColor) {
                        info.el.style.backgroundColor = info.event.backgroundColor;
                        info.el.style.borderColor = info.event.borderColor || info.event.backgroundColor;
                    }
                    if (info.event.color) {
                        info.el.style.backgroundColor = info.event.color;
                        info.el.style.borderColor = info.event.color;
                    }
                    
                    // Переконатися, що текст білий
                    info.el.style.color = '#ffffff';
                    const titleEl = info.el.querySelector('.fc-event-title');
                    if (titleEl) {
                        titleEl.style.color = '#ffffff';
                    }
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    const params = new URLSearchParams({
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                        ...currentFilters
                    });
                    
                    fetch(`/api/appointments-calendar?${params}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error('Error fetching appointments:', error);
                            failureCallback(error);
                        });
                },
                eventContent: function(arg) {
                    const props = arg.event.extendedProps;
                    const timeStr = arg.timeText || '';
                    return {
                        html: `
                            <div style="padding: 6px 8px; font-size: 0.875rem; line-height: 1.5; color: #ffffff !important; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4); box-sizing: border-box; width: 100%; height: 100%; overflow: hidden;">
                                <div style="font-weight: 700; margin-bottom: 3px; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ffffff !important;">${timeStr} ${props.client_name || ''}</div>
                                <div style="font-weight: 500; font-size: 0.85rem; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ffffff !important;">${props.service_name || ''}</div>
                                <div style="font-weight: 500; font-size: 0.8rem; opacity: 0.95; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ffffff !important;">${props.employee_name || ''}</div>
                            </div>
                        `
                    };
                }
            });

            calendar.render();

            function updateAppointmentTime(event, info) {
                const appointmentId = event.id;
                const start = event.start;
                const end = event.end || event.start;

                fetch(`/api/appointments/${appointmentId}/update-time`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        start: start.toISOString(),
                        end: end.toISOString()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Appointment time updated');
                    } else {
                        alert('Помилка: ' + data.message);
                        // Повернути подію на попереднє місце
                        info.revert();
                    }
                })
                .catch(error => {
                    console.error('Error updating appointment:', error);
                    alert('Помилка при оновленні запису');
                    // Повернути подію на попереднє місце
                    info.revert();
                });
            }

            window.applyFilters = function() {
                currentFilters = {
                    employee_id: document.getElementById('employee_id').value,
                    status: document.getElementById('status').value
                };
                calendar.refetchEvents();
            };
        });
    </script>
@endsection

