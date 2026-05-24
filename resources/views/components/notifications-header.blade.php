<div class="notifications-header-wrapper">
    <style>
        .notifications-header-wrapper { position: fixed; top: 0; left: 0; right: 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1rem 2rem; z-index: 1000; }
        .notifications-header-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .notifications-logo { font-size: 1.5rem; font-weight: 700; color: #667eea; text-decoration: none; }
        .notifications-header-right { display: flex; align-items: center; gap: 1.5rem; }
        .notifications-dropdown { position: relative; }
        .notifications-icon { position: relative; cursor: pointer; padding: 0.5rem; border-radius: 50%; transition: background 0.3s; font-size: 1.5rem; }
        .notifications-icon:hover { background: #f0f0f0; }
        .notifications-badge { position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; width: 22px; height: 22px; display: none; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; }
        .notifications-dropdown-content { position: absolute; right: 0; top: 100%; margin-top: 0.5rem; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 400px; max-height: 500px; overflow-y: auto; display: none; z-index: 1000; }
        .notifications-dropdown.active .notifications-dropdown-content { display: block; }
        .notifications-header { padding: 1rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
        .notifications-header h3 { font-size: 1rem; font-weight: 600; color: #333; }
        .mark-all-read { background: none; border: none; color: #667eea; cursor: pointer; font-size: 0.875rem; padding: 0.25rem 0.5rem; }
        .mark-all-read:hover { text-decoration: underline; }
        .notifications-list { max-height: 400px; overflow-y: auto; }
        .notification-item { padding: 1rem; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.2s; display: flex; gap: 1rem; }
        .notification-item:hover { background: #f9fafb; }
        .notification-item.unread { background: #eff6ff; }
        .notification-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
        .notification-icon.new-appointment { background: #dbeafe; color: #2563eb; }
        .notification-icon.appointment-cancelled { background: #fee2e2; color: #dc2626; }
        .notification-icon.new-client { background: #d1fae5; color: #059669; }
        .notification-content { flex: 1; }
        .notification-message { font-size: 0.875rem; color: #333; margin-bottom: 0.25rem; }
        .notification-time { font-size: 0.75rem; color: #6b7280; }
        .notification-empty { padding: 2rem; text-align: center; color: #6b7280; }
        .user-menu { display: flex; align-items: center; gap: 1rem; }
        .user-name { color: #333; font-weight: 500; }
        .logout-btn { padding: 0.5rem 1rem; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 0.875rem; }
        .logout-btn:hover { background: #5568d3; }
        body { padding-top: 70px; }
        @media (max-width: 768px) {
            .notifications-dropdown-content { width: 320px; }
            .notifications-header-wrapper { padding: 1rem; }
        }
    </style>
    <div class="notifications-header-content">
        <a href="{{ route('landing') }}" class="notifications-logo">💅 Beauty Salon CRM</a>
        <div class="notifications-header-right">
            @auth
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="notifications-icon" onclick="toggleNotifications()">
                        🔔
                        <span class="notifications-badge" id="notificationsBadge">0</span>
                    </div>
                    <div class="notifications-dropdown-content" id="notificationsContent">
                        <div class="notifications-header">
                            <h3>Сповіщення</h3>
                            <button class="mark-all-read" onclick="markAllAsRead()">Відмітити всі як прочитані</button>
                        </div>
                        <div class="notifications-list" id="notificationsList">
                            <div class="notification-empty">Завантаження...</div>
                        </div>
                    </div>
                </div>
                <div class="user-menu">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">Вийти</button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
    <script>
        let notificationsPollInterval;

        // Завантажити сповіщення при завантаженні сторінки
        document.addEventListener('DOMContentLoaded', function() {
            updateUnreadCount();
            
            // Оновлювати сповіщення кожні 30 секунд
            notificationsPollInterval = setInterval(function() {
                if (!document.getElementById('notificationsDropdown').classList.contains('active')) {
                    updateUnreadCount();
                }
            }, 30000);
        });

        function toggleNotifications() {
            const dropdown = document.getElementById('notificationsDropdown');
            dropdown.classList.toggle('active');
            
            if (dropdown.classList.contains('active')) {
                loadNotifications();
            }
        }

        function loadNotifications() {
            fetch('/api/notifications?limit=20', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById('notificationsList');
                
                if (data.notifications.length === 0) {
                    list.innerHTML = '<div class="notification-empty">Немає сповіщень</div>';
                    return;
                }

                list.innerHTML = data.notifications.map(notif => {
                    const iconClass = getIconClass(notif.type);
                    const icon = getIcon(notif.type);
                    const isUnread = !notif.read_at;
                    const url = notif.url || '#';
                    const safeUrl = url.replace(/'/g, "\\'");
                    
                    return `
                        <div class="notification-item ${isUnread ? 'unread' : ''}" onclick="markAsRead('${notif.id}', '${safeUrl}')">
                            <div class="notification-icon ${iconClass}">${icon}</div>
                            <div class="notification-content">
                                <div class="notification-message">${escapeHtml(notif.message)}</div>
                                <div class="notification-time">${notif.created_at}</div>
                            </div>
                        </div>
                    `;
                }).join('');
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
        }

        function updateUnreadCount() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('/api/notifications/unread-count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationsBadge');
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error updating unread count:', error);
            });
        }

        function markAsRead(id, url) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
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
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
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

        function getIconClass(type) {
            const classes = {
                'new_appointment': 'new-appointment',
                'appointment_cancelled': 'appointment-cancelled',
                'new_client': 'new-client',
            };
            return classes[type] || 'new-appointment';
        }

        function getIcon(type) {
            const icons = {
                'new_appointment': '📅',
                'appointment_cancelled': '❌',
                'new_client': '👤',
            };
            return icons[type] || '🔔';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Закрити dropdown при кліку поза ним
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationsDropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    </script>
</div>

