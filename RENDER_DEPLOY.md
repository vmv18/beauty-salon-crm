# Інструкція з розгортання на Render.com

## Передумови

1. Обліковий запис на [Render.com](https://render.com)
2. Репозиторій на GitHub або GitLab
3. Проєкт закомічений в git

## Крок 1: Підготовка репозиторію

Переконайтеся, що всі файли закомічені:

```bash
git add .
git commit -m "Prepare for Render deployment"
git push origin master
```

## Крок 2: Створення сервісів на Render.com

### Варіант А: Автоматичне розгортання через render.yaml

1. Увійдіть в [Render Dashboard](https://dashboard.render.com)
2. Натисніть "New +" → "Blueprint"
3. Підключіть ваш GitHub/GitLab репозиторій
4. Render автоматично виявить `render.yaml` і створить всі сервіси

### Варіант Б: Ручне створення

#### 2.1. Створення PostgreSQL бази даних

1. Натисніть "New +" → "PostgreSQL"
2. Налаштування:
   - **Name**: `beauty-salon-db`
   - **Database**: `beauty_salon`
   - **User**: `beauty_salon_user`
   - **Region**: `Frankfurt` (або найближчий до вас)
   - **Plan**: `Free`
3. Натисніть "Create Database"
4. Скопіюйте **Internal Database URL** (потрібен для налаштування)

#### 2.2. Створення Redis

1. Натисніть "New +" → "Redis"
2. Налаштування:
   - **Name**: `beauty-salon-redis`
   - **Region**: `Frankfurt` (той самий, що і база даних)
   - **Plan**: `Free`
3. Натисніть "Create Redis"
4. Скопіюйте **Internal Redis URL**

#### 2.3. Створення Web сервісу

1. Натисніть "New +" → "Web Service"
2. Підключіть ваш GitHub/GitLab репозиторій
3. Налаштування:
   - **Name**: `beauty-salon-crm`
   - **Environment**: `PHP`
   - **Region**: `Frankfurt`
   - **Branch**: `master` (або ваша основна гілка)
   - **Root Directory**: `/` (залиште порожнім)
   - **Build Command**:
     ```bash
     composer install --no-dev --optimize-autoloader && php artisan key:generate --force && php artisan storage:link && php artisan migrate --force && php artisan db:seed --force && npm install && npm run build
     ```
   - **Start Command**:
     ```bash
     php artisan serve --host=0.0.0.0 --port=$PORT
     ```
   - **Plan**: `Free`

## Крок 3: Налаштування змінних середовища

У налаштуваннях Web сервісу додайте Environment Variables:

### Обов'язкові змінні:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
LOG_LEVEL=error

# Database (використовуйте Internal Database URL з Render)
DB_CONNECTION=pgsql
DB_HOST=<з Internal Database URL>
DB_PORT=5432
DB_DATABASE=beauty_salon
DB_USERNAME=beauty_salon_user
DB_PASSWORD=<з Internal Database URL>

# Redis (використовуйте Internal Redis URL з Render)
REDIS_HOST=<з Internal Redis URL>
REDIS_PORT=6379
REDIS_PASSWORD=<з Internal Redis URL>
REDIS_CLIENT=phpredis

# Cache & Sessions
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Filesystem
FILESYSTEM_DISK=local

# Mail (налаштуйте пізніше для production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="Beauty Salon CRM"
```

### Генерація APP_KEY

Після першого деплою виконайте в Shell:

```bash
php artisan key:generate --force
```

Або додайте вручну в Environment Variables (генерується автоматично в buildCommand).

## Крок 4: Розгортання

1. Натисніть "Manual Deploy" → "Deploy latest commit"
2. Дочекайтеся завершення build (5-10 хвилин)
3. Перевірте логи, якщо є помилки

## Крок 5: Перевірка

1. Відкрийте URL вашого сервісу (наприклад: `https://beauty-salon-crm.onrender.com`)
2. Перевірте, що сайт працює
3. Перевірте, що зображення відображаються (через `/storage/...`)

## Важливі примітки

### Обмеження безкоштовного плану:

- **Sleep після неактивності**: Сервіс "засинає" після 15 хвилин неактивності
- **Обмеження ресурсів**: 512MB RAM, 0.5 CPU
- **PostgreSQL**: 1GB диску
- **Redis**: 25MB пам'яті

### Рекомендації:

1. **Для production**: Розгляньте платні плани або VPS
2. **Зображення**: Файли в `storage/app/public` зберігаються між деплоями, але для production краще використати хмарний storage (S3, Cloudinary)
3. **Email**: Налаштуйте реальний SMTP (Gmail, SendGrid) замість MailHog
4. **Моніторинг**: Налаштуйте health checks та моніторинг

### Вирішення проблем:

#### Помилка "Class 'Redis' not found"
- Перевірте, що Redis extension встановлено
- У buildCommand додайте: `pecl install redis && docker-php-ext-enable redis`

#### Помилка підключення до бази даних
- Перевірте Internal Database URL
- Переконайтеся, що використовуєте правильні credentials

#### Зображення не відображаються
- Виконайте: `php artisan storage:link`
- Перевірте права доступу до `storage/app/public`

#### Помилка міграцій
- Перевірте, що PostgreSQL extension встановлено
- У buildCommand додайте: `docker-php-ext-install pdo_pgsql`

## Оновлення проєкту

Після змін в коді:

```bash
git add .
git commit -m "Update code"
git push origin master
```

Render автоматично виявить зміни і розгорне нову версію.

## Додаткові ресурси

- [Render.com Documentation](https://render.com/docs)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [PostgreSQL on Render](https://render.com/docs/databases)

