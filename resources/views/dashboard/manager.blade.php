<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель менеджера - Beauty Salon CRM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="box">
                <h1 class="title is-3 has-text-success">📊 Панель менеджера</h1>
                
                <div class="notification is-light mb-5">
                    <p><strong>Користувач:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Роль:</strong> 
                        @foreach($user->roles as $role)
                            <span class="tag is-success mr-2">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </p>
                </div>

                <p class="mb-4">Ви можете управляти записами, клієнтами та звітами.</p>

                <form method="POST" action="{{ route('logout') }}" class="is-inline">
                    @csrf
                    <button type="submit" class="button is-danger">Вийти</button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
