@push('scripts')
<script>
    let notificationsPollInterval;

    // Завантажити сповіщення при завантаженні сторінки
    document.addEventListener('DOMContentLoaded', function() {
        @auth
            @if(auth()->user()->hasRole(['admin', 'manager', 'master', 'client']))
                loadNotifications();
                updateUnreadCount();
                
                // Оновлювати сповіщення кожні 30 секунд
                notificationsPollInterval = setInterval(function() {
                    const content = document.getElementById('notificationsContent');
                    if (content && content.classList.contains('is-hidden')) {
                        updateUnreadCount();
                    } else if (content && !content.classList.contains('is-hidden')) {
                        loadNotifications();
                    }
                }, 30000);
            @endif
        @endauth
        
        // Mobile menu toggle (Bulma navbar-burger)
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const navbarMenu = document.getElementById('navbarMenu');
        
        if (mobileMenuButton && navbarMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenuButton.classList.toggle('is-active');
                navbarMenu.classList.toggle('is-active');
            });
        }
    });

    function toggleNotifications() {
        const dropdown = document.getElementById('notificationsDropdown');
        const content = document.getElementById('notificationsContent');
        const isActive = !content.classList.contains('is-hidden');
        
        if (isActive) {
            content.classList.add('is-hidden');
        } else {
            content.classList.remove('is-hidden');
            loadNotifications();
        }
    }

    function loadNotifications() {
        fetch('/api/notifications?limit=20', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401) {
                    console.warn('Unauthorized: User not authenticated');
                    return { notifications: [], unread_count: 0 };
                }
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const list = document.getElementById('notificationsList');
            if (!list) return;
            
            // Перевірка, чи data існує та є валідним
            if (!data) {
                list.innerHTML = '<div class="notification-item has-text-grey">Помилка завантаження сповіщень</div>';
                return;
            }
            
            // Перевірка, чи data є масивом або об'єктом з notifications
            let notifications = [];
            if (Array.isArray(data)) {
                notifications = data;
            } else if (data && typeof data === 'object') {
                notifications = data.notifications || data.data || [];
            }
            
            // Перевірка, чи notifications є масивом
            if (!Array.isArray(notifications)) {
                console.error('Notifications is not an array:', notifications);
                list.innerHTML = '<div class="notification-item has-text-grey">Помилка формату даних</div>';
                return;
            }
            
            if (notifications.length === 0) {
                list.innerHTML = '<div class="notification-item has-text-grey">Немає сповіщень</div>';
                return;
            }

            list.innerHTML = notifications.map(notif => {
                const icon = getIcon(notif.type);
                const isUnread = !notif.read_at;
                const bgClass = isUnread ? 'has-background-info-light' : '';
                const message = notif.message || notif.text || 'Немає тексту';
                const createdAt = notif.created_at || notif.created || '';
                const notifId = notif.id || '';
                const url = notif.url || '#';
                
                return `
                    <div class="notification-item ${bgClass}" onclick="markAsRead('${notifId}', '${url}')" style="cursor: pointer;">
                        <div class="is-flex is-align-items-center" style="gap: 1rem;">
                            <span style="font-size: 1.5rem;">${icon}</span>
                            <div style="flex: 1;">
                                <p class="has-text-weight-semibold" style="margin-bottom: 0.25rem;">${message}</p>
                                <p class="is-size-7 has-text-grey">${createdAt}</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            const list = document.getElementById('notificationsList');
            if (list) {
                list.innerHTML = '<div class="notification-item has-text-danger">Помилка завантаження сповіщень</div>';
            }
        });
    }

    function updateUnreadCount() {
        fetch('/api/notifications/unread-count', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401) {
                    console.warn('Unauthorized: User not authenticated');
                    return { count: 0 };
                }
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const badge = document.getElementById('notificationsBadge');
            if (!badge) return;
            
            if (data && data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.classList.remove('is-hidden');
            } else {
                badge.classList.add('is-hidden');
            }
        })
        .catch(error => {
            console.error('Error updating unread count:', error);
        });
    }

    function markAsRead(id, url) {
        fetch(`/api/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUnreadCount();
                loadNotifications();
                
                // Перейти на URL сповіщення
                if (url && url !== '#') {
                    window.location.href = url;
                }
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    function markAllAsRead() {
        fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUnreadCount();
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
        });
    }

    function getIcon(type) {
        const icons = {
            'new_appointment': '📅',
            'appointment_cancelled': '❌',
            'new_client': '👤',
        };
        return icons[type] || '🔔';
    }

    // Закрити dropdown при кліку поза ним
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('notificationsDropdown');
        const content = document.getElementById('notificationsContent');
        if (dropdown && content && !dropdown.contains(event.target)) {
            content.classList.add('is-hidden');
        }
    });
</script>
@endpush

