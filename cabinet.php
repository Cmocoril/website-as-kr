<?php require_once 'php/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - Турбаза</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Лесная поляна</div>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="php/logout.php">Выйти</a></li>
            </ul>
        </nav>
    </header>

    <div class="cabinet-container">
        <h2>Личный кабинет</h2>
        
        <div class="user-info">
            <h3>Привет, <span id="username">Пользователь</span>!</h3>
            <p>Email: <span id="email">user@example.com</span></p>
            <p>Телефон: <span id="phone">+7 (999) 123-45-67</span></p>
        </div>

        <h3>Мои бронирования</h3>
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>Домик</th>
                    <th>Заезд</th>
                    <th>Выезд</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody id="bookings-body">
                <tr><td colspan="5" style="text-align:center; color:#888;">Загрузка…</td></tr>
            </tbody>
        </table>
    </div>

    <script>
        // Загрузка данных пользователя и бронирований
        fetch('php/get_bookings.php')
            .then(response => {
                if (response.status === 401) {
                    location.href = 'login.php';
                    return null;
                }
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.user) {
                    document.getElementById('username').textContent = data.user.username;
                    document.getElementById('email').textContent = data.user.email;
                    document.getElementById('phone').textContent = data.user.phone || 'Не указан';
                }
                
                if (data.bookings && data.bookings.length > 0) {
                    const tbody = document.getElementById('bookings-body');
                    data.bookings.forEach(booking => {
                        const row = tbody.insertRow();
                        row.insertCell().textContent = booking.cabin_name;
                        row.insertCell().textContent = booking.check_in;
                        row.insertCell().textContent = booking.check_out;
                        row.insertCell().textContent = booking.total_price + ' ₽';

                        const span = document.createElement('span');
                        span.className = 'status ' + booking.status;
                        span.textContent = booking.status;
                        row.insertCell().appendChild(span);
                    });
                } else {
                    document.getElementById('bookings-body').innerHTML = 
                        '<tr><td colspan="5" style="text-align: center;">У вас пока нет бронирований</td></tr>';
                }
            })
            .catch(() => {
                document.getElementById('bookings-body').innerHTML =
                    '<tr><td colspan="5" style="text-align:center; color:#c0392b;">' +
                    'Не удалось загрузить данные. Обновите страницу.</td></tr>';
            });
    </script>
</body>
</html>