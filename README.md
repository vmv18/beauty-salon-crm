# Beauty Salon CRM

Проєкт Laravel з Docker для системи управління салоном краси.

## 🐳 Середовище розробки

Цей проєкт використовує **Docker-середовище розробки** (Development Environment) на основі Docker Compose. Це контейнеризоване середовище, яке забезпечує ізольоване та переносне середовище для розробки Laravel додатків.

### Характеристики середовища:

- **Контейнеризація**: Всі сервіси (PHP, MySQL, Nginx, Redis, MailHog) працюють у окремих Docker контейнерах
- **Ізоляція**: Кожен сервіс має свою ізольовану середовище, що запобігає конфліктам залежностей
- **Переносність**: Середовище працює однаково на Windows, macOS та Linux
- **Автоматизація**: Автоматичне налаштування прав доступу, мережі та залежностей
- **Гаряча перезавантаження**: Зміни в коді відображаються миттєво завдяки volume mapping
- **Кешування**: Redis для швидкого кешування даних та сесій

### Архітектура середовища:

![Docker Environment Architecture](docs/architecture.svg)

**Потік даних:**
- **HTTP запити** → Nginx (порт 80) → PHP-FPM (порт 9000) → Laravel додаток
- **Запити до БД** → PHP-FPM → MySQL (порт 3306)
- **Кешування** → Laravel → Redis (порт 6379) - для кешу, сесій та черг
- **Email повідомлення** → Laravel → MailHog (порт 1025/8025)

## 🛠️ Технологічний стек

### Backend
- **Laravel 12** (остання версія) - PHP фреймворк
- **Blade** - шаблонізатор Laravel
- **PHP 8.4-FPM** - сервер обробки PHP

### База даних
- **MySQL 8.0** - реляційна база даних

### Веб-сервер
- **Nginx** - веб-сервер та reverse proxy

### Інструменти розробки
- **MailHog** - локальний SMTP сервер для тестування email
- **Redis 7** - in-memory кеш, сесії та черги
- **Docker & Docker Compose** - контейнеризація та оркестрація

## 📋 Вимоги

- Docker Desktop (або Docker Engine + Docker Compose)
- Git (опціонально)

## 🚀 Швидкий старт

### 1. Клонуйте репозиторій (якщо потрібно)

```bash
git clone <repository-url>
cd beauty-salon-crm
```

### 2. Налаштуйте .env файл

```bash
cp .env.example .env
```

Відредагуйте `.env` файл згідно з вашими налаштуваннями (за замовчуванням вже налаштовано для Docker).

### 3. Запустіть Docker контейнери

```bash
docker-compose up -d --build
```

Ця команда:
- Збере образ PHP з необхідними розширеннями (включаючи Redis extension)
- Запустить контейнери для Nginx, PHP-FPM, MySQL, Redis та MailHog
- Автоматично налаштує права доступу до директорій storage та cache

### 4. Встановіть залежності Composer

```bash
docker-compose exec app composer install
```

### 5. Згенеруйте ключ додатку (якщо потрібно)

```bash
docker-compose exec app php artisan key:generate
```

### 6. Запустіть міграції бази даних

```bash
docker-compose exec app php artisan migrate
```

## 🌐 Доступ до сервісів

Після запуску контейнерів доступні наступні сервіси:

| Сервіс | URL | Опис |
|--------|-----|------|
| **Веб-додаток** | http://localhost | Головна сторінка Laravel додатку |
| **MailHog UI** | http://localhost:8025 | Веб-інтерфейс для перегляду тестових email |
| **MySQL** | localhost:3306 | Прямий доступ до бази даних |
| **Redis** | localhost:6379 | Прямий доступ до Redis (для кешу, сесій та черг) |

### Параметри підключення до MySQL:
- **Host**: `mysql` (всередині Docker мережі) або `localhost` (з хост-машини)
- **Port**: `3306`
- **Database**: `beauty_salon`
- **Username**: `root`
- **Password**: `root`

## 📁 Структура проєкту

```
beauty-salon-crm/
├── app/                          # Основний код додатку
│   ├── Http/
│   │   └── Controllers/         # Контролери
│   ├── Models/                   # Eloquent моделі
│   ├── Providers/               # Service Providers
│   └── ...
├── bootstrap/                    # Файли ініціалізації
│   ├── cache/                   # Кешовані файли
│   └── app.php
├── config/                       # Файли конфігурації
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   └── ...
├── database/                     # База даних
│   ├── migrations/              # Міграції БД
│   ├── seeders/                 # Seeders для тестових даних
│   ├── factories/               # Factories для моделей
│   └── database.sqlite          # SQLite для тестування
├── docs/                         # Документація
│   └── architecture.svg         # Діаграма архітектури
├── docker/                       # Docker конфігурації
│   └── nginx/
│       └── default.conf         # Конфігурація Nginx
├── public/                       # Публічна директорія (web root)
│   ├── index.php
│   ├── favicon.ico
│   └── ...
├── resources/                    # Ресурси
│   ├── views/                   # Blade шаблони
│   ├── css/                     # CSS файли
│   └── js/                      # JavaScript файли
├── routes/                       # Маршрути
│   ├── web.php                 # Web маршрути
│   └── console.php             # Console команди
├── storage/                      # Зберігання файлів
│   ├── app/                    # Завантажені файли
│   ├── framework/              # Фреймворк файли (cache, sessions, views)
│   └── logs/                   # Логи додатку
├── tests/                        # Тести
│   ├── Feature/                # Feature тести
│   └── Unit/                   # Unit тести
├── vendor/                       # Composer залежності (не комітиться)
├── .env                         # Змінні середовища (не комітиться)
├── .env.example                 # Приклад конфігурації
├── .dockerignore                # Файли, що ігноруються при збірці Docker
├── artisan                      # CLI команди Laravel
├── composer.json                # Composer залежності
├── composer.lock                # Зафіксовані версії залежностей
├── docker-compose.yml           # Конфігурація Docker Compose
├── Dockerfile                   # Образ PHP-FPM з розширеннями
├── docker-entrypoint.sh         # Скрипт для налаштування прав доступу
├── package.json                 # NPM залежності
├── phpunit.xml                  # Конфігурація PHPUnit
├── plan.md                      # План розробки дипломної роботи
├── README.md                    # Документація проєкту
└── vite.config.js               # Конфігурація Vite
```

## 🔧 Команди Docker Compose

### Управління контейнерами

```bash
# Запустити всі контейнери
docker-compose up -d

# Зупинити всі контейнери
docker-compose down

# Перезапустити контейнери
docker-compose restart

# Переглянути статус контейнерів
docker-compose ps

# Переглянути логи
docker-compose logs -f

# Переглянути логи конкретного сервісу
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f nginx
```

### Робота з Laravel

```bash
# Виконати Artisan команду
docker-compose exec app php artisan <command>

# Приклади:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:list

# Встановити пакет через Composer
docker-compose exec app composer require <package>

# Оновити залежності
docker-compose exec app composer update

# Зайти в контейнер (інтерактивна сесія)
docker-compose exec app bash
```

### Робота з базою даних

```bash
# Підключитися до MySQL через командний рядок
docker-compose exec mysql mysql -u root -proot beauty_salon

# Створити резервну копію бази даних
docker-compose exec mysql mysqldump -u root -proot beauty_salon > backup.sql

# Відновити базу даних з резервної копії
docker-compose exec -T mysql mysql -u root -proot beauty_salon < backup.sql
```

## 🔐 Налаштування прав доступу

Проєкт автоматично налаштовує правильні права доступу до директорій `storage` та `bootstrap/cache` при старті контейнера через `docker-entrypoint.sh`.

Якщо виникнуть проблеми з правами доступу, виконайте:

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

## 🔔 Нагадування про записи

Система автоматично відправляє нагадування клієнтам про майбутні записи:

- **24-годинне нагадування**: Відправляється щодня о 09:00
- **2-годинне нагадування**: Відправляється щогодини

### Налаштування Queue

Queue налаштовано на використання Redis (`QUEUE_CONNECTION=redis`).

Для обробки черги виконайте:

```bash
docker-compose exec app php artisan queue:work redis
```

Або для постійної роботи в фоновому режимі:

```bash
docker-compose exec app php artisan queue:work redis --daemon
```

**Примітка:** Redis забезпечує швидшу обробку черг порівняно з database driver.

### Налаштування Scheduler

Для автоматичного запуску команд нагадувань налаштуйте cron:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Або в Docker для тестування:

```bash
docker-compose exec app php artisan schedule:work
```

### Тестування нагадувань

Для тестування нагадувань вручну:

```bash
# Нагадування за 24 години
docker-compose exec app php artisan appointments:send-reminders --hours=24

# Нагадування за 2 години
docker-compose exec app php artisan appointments:send-reminders --hours=2
```

## 🧪 Тестування Email

MailHog автоматично перехоплює всі email, що відправляються з додатку. Для перегляду:

1. Відкрийте http://localhost:8025 у браузері
2. Відправте тестовий email з додатку
3. Перегляньте його в інтерфейсі MailHog

## ⚡ Кешування

Проєкт використовує **Redis** для:
- **Кешування** конфігурації, views та запитів
- **Сесій** користувачів (швидший доступ порівняно з database)
- **Черг** для асинхронної обробки завдань

### Перевірка Redis

```bash
# Перевірити статус Redis
docker-compose exec redis redis-cli ping

# Переглянути статистику
docker-compose exec redis redis-cli info

# Перевірити підключення з PHP
docker-compose exec app php -r "echo extension_loaded('redis') ? 'Redis extension loaded' : 'Redis extension NOT loaded';"
```

### Керування кешем

```bash
# Очистити весь кеш
docker-compose exec app php artisan cache:clear

# Кешувати конфігурацію (production)
docker-compose exec app php artisan config:cache

# Очистити кеш конфігурації
docker-compose exec app php artisan config:clear

# Кешувати views (production)
docker-compose exec app php artisan view:cache

# Очистити кеш views
docker-compose exec app php artisan view:clear
```

Детальна інформація про кешування див. в [CACHING.md](CACHING.md)

## 🔄 Оновлення проєкту

```bash
# Перебудувати образи після змін у Dockerfile
docker-compose build

# Перебудувати та перезапустити
docker-compose up -d --build

# Оновити залежності Composer
docker-compose exec app composer update
```

## 🐛 Вирішення проблем

### Контейнер не запускається
```bash
# Перевірити логи
docker-compose logs app

# Перебудувати образ
docker-compose build --no-cache app
```

### Помилки з базою даних
```bash
# Перевірити, чи запущений MySQL
docker-compose ps mysql

# Перезапустити MySQL
docker-compose restart mysql
```

### Проблеми з правами доступу
```bash
# Виправити права вручну
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Проблеми з Redis
```bash
# Перевірити статус Redis контейнера
docker-compose ps redis

# Перевірити підключення до Redis
docker-compose exec redis redis-cli ping

# Перезапустити Redis
docker-compose restart redis

# Перебудувати контейнер app (якщо Redis extension не завантажений)
docker-compose build app
docker-compose up -d app
```

### Помилка 504 Gateway Time-out
Якщо виникає помилка 504, це може бути пов'язано з:
- Redis extension не завантажений - перебудуйте контейнер `app`
- Таймаути nginx занадто малі - перевірте `docker/nginx/default.conf`
- Redis контейнер не запущений - запустіть `docker-compose up -d redis`

## 📝 Змінні середовища

Основні змінні в `.env` файлі:

```env
DB_CONNECTION=mysql
DB_HOST=mysql          # Назва сервісу в docker-compose.yml
DB_PORT=3306
DB_DATABASE=beauty_salon
DB_USERNAME=root
DB_PASSWORD=root

MAIL_MAILER=smtp
MAIL_HOST=mailhog      # Назва сервісу MailHog
MAIL_PORT=1025

# Кешування через Redis
CACHE_STORE=redis

# Сесії через Redis
SESSION_DRIVER=redis

# Черги через Redis
QUEUE_CONNECTION=redis

# Налаштування Redis
REDIS_CLIENT=phpredis
REDIS_HOST=redis        # Назва сервісу Redis в docker-compose.yml
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0              # База даних для загальних даних
REDIS_CACHE_DB=1        # Окрема база даних для кешу
```

## 📚 Додаткова інформація

- [Документація Laravel](https://laravel.com/docs)
- [Документація Docker Compose](https://docs.docker.com/compose/)
- [Документація MailHog](https://github.com/mailhog/MailHog)
- [Документація Redis](https://redis.io/documentation)
- [Кешування в проєкті](CACHING.md)

## 📄 Ліцензія

Цей проєкт є приватним проєктом для системи управління салоном краси.

