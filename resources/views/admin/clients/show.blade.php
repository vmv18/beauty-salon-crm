@extends('layouts.app')

@section('title', 'Профіль клієнта - Beauty Salon CRM')

@push('styles')
@endpush

@section('content')
    <div class="container">
        <nav class="breadcrumb mb-5" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Панель управління</a></li>
                <li><a href="{{ route('clients.index') }}">Клієнти</a></li>
                <li class="is-active"><a href="#" aria-current="page">{{ $client->user->name }}</a></li>
            </ul>
        </nav>

        <div class="box">
            <h1 class="title is-3 has-text-primary mb-5">👤 Профіль клієнта</h1>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('clients.edit', $client) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">Редагувати</a>
                <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-gray-700 transition">Назад</a>
            </div>
        </div>

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

        <div class="flex gap-2 mb-8 border-b-2 border-gray-200">
            <button class="px-6 py-4 bg-none border-none cursor-pointer text-base border-b-3 border-transparent -mb-[2px] tab active text-purple-600 border-purple-600 font-semibold" onclick="showTab('info')">Загальна інформація</button>
            <button class="px-6 py-4 bg-none border-none cursor-pointer text-base text-gray-600 border-b-3 border-transparent -mb-[2px] tab" onclick="showTab('loyalty')">⭐ Програма лояльності</button>
            <button class="px-6 py-4 bg-none border-none cursor-pointer text-base text-gray-600 border-b-3 border-transparent -mb-[2px] tab" onclick="showTab('appointments')">Записи</button>
            <button class="px-6 py-4 bg-none border-none cursor-pointer text-base text-gray-600 border-b-3 border-transparent -mb-[2px] tab" onclick="showTab('payments')">Платежі</button>
        </div>

        <div id="tab-info" class="tab-content block">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-600 mb-4">Основна інформація</h3>
                    @if($client->photo)
                        <div class="mb-4 text-center">
                            <img src="{{ Storage::url($client->photo) }}" alt="Фото клієнта" class="max-w-[200px] max-h-[200px] rounded-lg border-2 border-purple-600 shadow-md mx-auto">
                        </div>
                    @endif
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Ім'я</div>
                        <div class="text-gray-900">{{ $client->user->name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Email</div>
                        <div class="text-gray-900">{{ $client->email ?? $client->user->email ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Телефон</div>
                        <div class="text-gray-900">{{ $client->phone ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Статус</div>
                        <div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $client->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $client->status == 'active' ? 'Активний' : 'Неактивний' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-600 mb-4">Додаткова інформація</h3>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Дата народження</div>
                        <div class="text-gray-900">{{ $client->date_of_birth ? $client->date_of_birth->format('d.m.Y') : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Стать</div>
                        <div class="text-gray-900">
                            @if($client->gender)
                                {{ $client->gender == 'male' ? 'Чоловік' : ($client->gender == 'female' ? 'Жінка' : 'Інше') }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Адреса</div>
                        <div class="text-gray-900">{{ $client->address ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-600 mb-4">Системна інформація</h3>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">ID клієнта</div>
                        <div class="text-gray-900">#{{ $client->id }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">ID користувача</div>
                        <div class="text-gray-900">#{{ $client->user_id }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Дата створення</div>
                        <div class="text-gray-900">{{ $client->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="font-semibold text-gray-700 mb-1">Останнє оновлення</div>
                        <div class="text-gray-900">{{ $client->updated_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($client->notes)
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-600 mb-4">Примітки</h3>
                    <div class="text-gray-900 whitespace-pre-wrap">{{ $client->notes }}</div>
                </div>
            @endif
        </div>

        <div id="tab-loyalty" class="tab-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-6">⭐ Статус лояльності</h3>
                    <div class="mb-4">
                        <div class="text-white/90 mb-1">Рівень</div>
                        <div class="text-2xl font-bold mt-2">
                            @if($client->loyalty_level === 'bronze')
                                🥉 {{ $client->loyalty_level_name }}
                            @elseif($client->loyalty_level === 'silver')
                                🥈 {{ $client->loyalty_level_name }}
                            @else
                                🥇 {{ $client->loyalty_level_name }}
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-white/90 mb-1">Знижка</div>
                        <div class="text-xl font-semibold">
                            @if($client->loyalty_discount > 0)
                                {{ number_format($client->loyalty_discount, 0) }}%
                            @else
                                Немає знижки
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-600 mb-4">💰 Поточний баланс</h3>
                    <div class="mb-4">
                        <div class="text-3xl font-bold text-purple-600 mt-2">
                            {{ number_format($client->loyalty_points ?? 0, 0) }} балів
                        </div>
                        <div class="mt-2 text-gray-600 text-sm">
                            = {{ number_format($client->convertPointsToDiscount($client->loyalty_points ?? 0), 2) }} грн знижки
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="font-semibold text-gray-700 mb-1">Всього зароблено</div>
                        <div class="text-lg font-semibold">
                            {{ number_format($client->total_loyalty_points_earned ?? 0, 0) }} балів
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-600 mb-4">📊 До наступного рівня</h3>
                    @php
                        $currentEarned = $client->total_loyalty_points_earned ?? 0;
                        $nextLevel = 'gold';
                        $nextLevelPoints = 10000;
                        if ($client->loyalty_level === 'bronze') {
                            $nextLevel = 'silver';
                            $nextLevelPoints = 5000;
                        } elseif ($client->loyalty_level === 'silver') {
                            $nextLevel = 'gold';
                            $nextLevelPoints = 10000;
                        } else {
                            $nextLevel = null;
                        }
                        $pointsNeeded = $nextLevel ? max(0, $nextLevelPoints - $currentEarned) : 0;
                        $progress = $nextLevel ? min(100, ($currentEarned / $nextLevelPoints) * 100) : 100;
                    @endphp
                    @if($nextLevel)
                        <div class="mb-4">
                            <div class="font-semibold text-gray-700 mb-1">До {{ $nextLevel === 'silver' ? 'Срібного' : 'Золотого' }} рівня</div>
                            <div class="text-lg font-semibold mt-2">
                                {{ number_format($pointsNeeded, 0) }} балів
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="bg-gray-200 h-5 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-600 to-purple-800 h-full transition-all duration-300" style="width: {{ $progress }}%;"></div>
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                {{ number_format($progress, 1) }}% завершено
                            </div>
                        </div>
                    @else
                        <div>
                            <div class="text-lg text-green-600 font-semibold">
                                ✅ Ви досягли максимального рівня!
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg mt-8">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">📝 Історія балів</h3>
                @php
                    $loyaltyHistory = $client->loyaltyPoints()->latest()->limit(20)->get();
                @endphp
                @if($loyaltyHistory->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse mt-4">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="p-3 text-left border-b-2 border-gray-300 text-sm font-semibold">Дата</th>
                                    <th class="p-3 text-left border-b-2 border-gray-300 text-sm font-semibold">Тип</th>
                                    <th class="p-3 text-left border-b-2 border-gray-300 text-sm font-semibold">Бали</th>
                                    <th class="p-3 text-left border-b-2 border-gray-300 text-sm font-semibold">Баланс</th>
                                    <th class="p-3 text-left border-b-2 border-gray-300 text-sm font-semibold">Опис</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loyaltyHistory as $point)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 border-b border-gray-200 text-sm">
                                            {{ $point->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="p-3 border-b border-gray-200 text-sm">
                                            @if($point->type === 'earned')
                                                <span class="text-green-600">+ Зараховано</span>
                                            @elseif($point->type === 'spent')
                                                <span class="text-red-600">- Витрачено</span>
                                            @else
                                                <span class="text-yellow-600">⚠ Застаріло</span>
                                            @endif
                                        </td>
                                        <td class="p-3 border-b border-gray-200 text-sm font-semibold">
                                            @if($point->points > 0)
                                                <span class="text-green-600">+{{ number_format($point->points, 0) }}</span>
                                            @else
                                                <span class="text-red-600">{{ number_format($point->points, 0) }}</span>
                                            @endif
                                        </td>
                                        <td class="p-3 border-b border-gray-200 text-sm">
                                            {{ number_format($point->balance_after, 0) }}
                                        </td>
                                        <td class="p-3 border-b border-gray-200 text-sm text-gray-600">
                                            {{ $point->description ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-600">
                        <p class="text-lg">📊 Історія балів порожня</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Бали будуть нараховані після завершення першої послуги
                        </p>
                    </div>
                @endif
            </div>

            <div class="bg-blue-50 p-6 rounded-lg mt-8">
                <h3 class="text-lg font-semibold text-purple-600 mb-4">ℹ️ Як працює програма лояльності</h3>
                <ul class="mt-4 ml-6 leading-loose text-gray-700 list-disc">
                    <li><strong>Нарахування:</strong> 1% від суми послуги (мінімум 10 балів) після завершення</li>
                    <li><strong>Конвертація:</strong> 1 бал = 0.1 грн знижки</li>
                    <li><strong>Бронзовий рівень:</strong> 0-4,999 балів (без знижки)</li>
                    <li><strong>Срібний рівень:</strong> 5,000-9,999 балів (5% знижка на всі послуги)</li>
                    <li><strong>Золотий рівень:</strong> 10,000+ балів (10% знижка на всі послуги)</li>
                </ul>
            </div>
        </div>

        <div id="tab-appointments" class="tab-content hidden">
            @if($appointments->count() > 0)
                <div class="mb-4">
                    <a href="{{ route('payments.create', ['client_id' => $client->id]) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">+ Додати платіж</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Дата</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Час</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Послуга</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Майстер</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Ціна</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Статус</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 border-b border-gray-200">{{ $appointment->appointment_date->format('d.m.Y') }}</td>
                                    <td class="p-4 border-b border-gray-200">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                    <td class="p-4 border-b border-gray-200">{{ $appointment->service->name }}</td>
                                    <td class="p-4 border-b border-gray-200">{{ $appointment->employee->user->name }}</td>
                                    <td class="p-4 border-b border-gray-200">{{ number_format($appointment->price, 0) }} грн</td>
                                    <td class="p-4 border-b border-gray-200">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($appointment->status === 'scheduled') bg-yellow-100 text-yellow-800
                                            @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($appointment->status === 'completed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            @if($appointment->status === 'scheduled') Заплановано
                                            @elseif($appointment->status === 'confirmed') Підтверджено
                                            @elseif($appointment->status === 'completed') Виконано
                                            @else Скасовано
                                            @endif
                                        </span>
                                    </td>
                                    <td class="p-4 border-b border-gray-200">
                                        <a href="{{ route('appointments.show', $appointment) }}" class="px-3 py-1 bg-purple-600 text-white rounded text-xs font-medium hover:bg-purple-700 transition no-underline">Деталі</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="has-text-centered" style="margin-top: 3rem;">
                    {{ $appointments->links('vendor.pagination.custom') }}
                </div>
            @else
                <div class="text-center py-12 text-gray-600">
                    <p class="text-lg">📅 Немає записів</p>
                </div>
            @endif
        </div>

        <div id="tab-payments" class="tab-content hidden">
            @if(isset($totalPaid) || isset($pendingPayments))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-600 mb-4">Статистика платежів</h3>
                        <div class="mb-4">
                            <div class="font-semibold text-gray-700 mb-1">Всього оплачено</div>
                            <div class="text-xl font-semibold text-green-600">
                                {{ number_format($totalPaid ?? 0, 2) }} грн
                            </div>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-700 mb-1">Очікує оплати</div>
                            <div class="text-xl font-semibold text-yellow-600">
                                {{ number_format($pendingPayments ?? 0, 2) }} грн
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('payments.create', ['client_id' => $client->id]) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg no-underline text-sm font-medium hover:bg-purple-700 transition">+ Додати платіж</a>
            </div>

            @if($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Дата</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Сума</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Спосіб оплати</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Статус</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Запис</th>
                                <th class="p-4 text-left border-b-2 border-gray-300 font-semibold">Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 border-b border-gray-200">{{ $payment->payment_date->format('d.m.Y') }}</td>
                                    <td class="p-4 border-b border-gray-200 font-semibold">{{ number_format($payment->amount, 2) }} грн</td>
                                    <td class="p-4 border-b border-gray-200">{{ $payment->payment_method_name }}</td>
                                    <td class="p-4 border-b border-gray-200">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($payment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                            @elseif($payment->status === 'refunded') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $payment->status_name }}
                                        </span>
                                    </td>
                                    <td class="p-4 border-b border-gray-200">
                                        @if($payment->appointment)
                                            <a href="{{ route('appointments.show', $payment->appointment) }}" class="text-purple-600 no-underline hover:underline">
                                                {{ $payment->appointment->service->name }} ({{ $payment->appointment->appointment_date->format('d.m.Y') }})
                                            </a>
                                        @else
                                            <span class="text-gray-500">Без прив'язки</span>
                                        @endif
                                    </td>
                                    <td class="p-4 border-b border-gray-200">
                                        <a href="{{ route('payments.show', $payment) }}" class="px-3 py-1 bg-purple-600 text-white rounded text-xs font-medium hover:bg-purple-700 transition no-underline">Деталі</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="has-text-centered" style="margin-top: 3rem;">
                    {{ $payments->links('vendor.pagination.custom') }}
                </div>
            @else
                <div class="text-center py-12 text-gray-600">
                    <p class="text-lg">💰 Немає платежів</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Приховати всі вкладки
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active', 'text-purple-600', 'border-purple-600', 'font-semibold');
                tab.classList.add('text-gray-600');
            });

            // Показати обрану вкладку
            const targetTab = document.getElementById('tab-' + tabName);
            if (targetTab) {
                targetTab.classList.remove('hidden');
                targetTab.classList.add('block');
            }
            
            // Активувати кнопку табу
            event.target.classList.add('active', 'text-purple-600', 'border-purple-600', 'font-semibold');
            event.target.classList.remove('text-gray-600');
        }
    </script>
@endsection

