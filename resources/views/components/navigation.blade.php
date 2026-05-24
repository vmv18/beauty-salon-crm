<nav class="navbar is-fixed-top has-shadow" role="navigation" aria-label="main navigation">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item logo-link" href="{{ route('landing') }}">
                <span class="logo-text">Beauty Salon</span>
            </a>

            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu" id="mobile-menu-button">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarMenu" class="navbar-menu">
            <div class="navbar-start">
                @auth
                    @if(auth()->user()->hasRole(['admin', 'manager']))
                        @if(auth()->user()->hasRole('admin'))
                            <a class="navbar-item @if(request()->routeIs('admin.dashboard')) is-active has-background-primary has-text-white @endif" href="{{ route('admin.dashboard') }}">
                                Dashboard
                            </a>
                        @endif
                        <a class="navbar-item @if(request()->routeIs('clients.*')) is-active has-background-primary has-text-white @endif" href="{{ route('clients.index') }}">
                            Клієнти
                        </a>
                        <a class="navbar-item @if(request()->routeIs('appointments.*') && !request()->routeIs('appointments.calendar')) is-active has-background-primary has-text-white @endif" href="{{ route('appointments.index') }}">
                            Записи
                        </a>
                        <a class="navbar-item @if(request()->routeIs('appointments.calendar')) is-active has-background-primary has-text-white @endif" href="{{ route('appointments.calendar') }}">
                            Календар
                        </a>
                        <a class="navbar-item @if(request()->routeIs('employees.*')) is-active has-background-primary has-text-white @endif" href="{{ route('employees.index') }}">
                            Майстри
                        </a>
                        <a class="navbar-item @if(request()->routeIs('services.*')) is-active has-background-primary has-text-white @endif" href="{{ route('services.index') }}">
                            Послуги
                        </a>
                        <a class="navbar-item @if(request()->routeIs('payments.*')) is-active has-background-primary has-text-white @endif" href="{{ route('payments.index') }}">
                            Платежі
                        </a>
                        <a class="navbar-item @if(request()->routeIs('reviews.*')) is-active has-background-primary has-text-white @endif" href="{{ route('reviews.index') }}">
                            Відгуки
                        </a>
                        @if(auth()->user()->hasRole('admin'))
                            <a class="navbar-item @if(request()->routeIs('admin.contact-messages.*')) is-active has-background-primary has-text-white @endif" href="{{ route('admin.contact-messages.index') }}">
                                Повідомлення
                                @php
                                    $unreadCount = \App\Models\ContactMessage::where('is_read', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="tag is-danger is-small">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <a class="navbar-item @if(request()->routeIs('admin.reports.*')) is-active has-background-primary has-text-white @endif" href="{{ route('admin.reports.index') }}">
                                Звіти
                            </a>
                        @endif
                    @else
                        <a class="navbar-item" href="{{ route('public.services') }}">Послуги</a>
                        <a class="navbar-item" href="{{ route('public.employees') }}">Наші майстри</a>
                        <a class="navbar-item" href="{{ route('public.gallery') }}">Галерея</a>
                        <a class="navbar-item" href="{{ route('public.contact') }}">Контакти</a>
                    @endif
                @else
                    <a class="navbar-item" href="{{ route('public.services') }}">Послуги</a>
                    <a class="navbar-item" href="{{ route('public.employees') }}">Наші майстри</a>
                    <a class="navbar-item" href="{{ route('public.gallery') }}">Галерея</a>
                    <a class="navbar-item" href="{{ route('public.contact') }}">Контакти</a>
                @endauth
            </div>

            <div class="navbar-end">
                @auth
                    @if(auth()->user()->hasRole(['admin', 'manager', 'master', 'client']))
                        <div class="navbar-item" id="notificationsDropdown" style="flex-grow: 0; flex-shrink: 0; width: auto; padding: 0;">
                            <a class="notifications-button" onclick="toggleNotifications()" style="display: flex; align-items: center; justify-content: flex-start; padding: 0.5rem; background: transparent; border: none; color: #4a5568;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 1.25rem; height: 1.25rem; color: inherit;">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <span class="notification-badge is-hidden" id="notificationsBadge">0</span>
                            </a>
                            <div class="notifications-dropdown is-hidden" id="notificationsContent">
                                <div class="notifications-header">
                                    <strong>Сповіщення</strong>
                                    <button class="button is-small is-text" onclick="markAllAsRead()">Відмітити всі як прочитані</button>
                                </div>
                                <hr class="notifications-divider">
                                <div id="notificationsList" class="notifications-list">
                                    <div class="notification-item">Завантаження...</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <span class="navbar-item is-hidden-touch">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->hasRole(['client', 'master']))
                        <div class="navbar-item">
                            <a class="button is-text" href="@if(auth()->user()->hasRole('master')){{ route('master.dashboard') }}@else{{ route('client.dashboard') }}@endif" title="Кабінет">
                                @svg('heroicon-o-user-circle', 'icon')
                            </a>
                        </div>
                    @endif
                    <div class="navbar-item">
                        <form method="POST" action="{{ route('logout') }}" class="is-inline">
                            @csrf
                            <button type="submit" class="button is-text" title="Вийти">
                                @svg('heroicon-o-arrow-right-on-rectangle', 'icon')
                            </button>
                        </form>
                    </div>
                @else
                    <div class="navbar-item">
                        <a class="button is-text" href="{{ route('login') }}" title="Вхід">
                            @svg('heroicon-o-arrow-left-on-rectangle', 'icon')
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

@include('components.navigation-styles')
@include('components.navigation-scripts')

