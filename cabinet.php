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
            
            <button class="burger" id="burger-btn">☰</button>
            
            
            <ul id="nav-menu">
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
                    <th>Действия</th> 
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
                        
                        // Заполняем первые 4 ячейки
                        row.insertCell().textContent = booking.cabin_name;
                        row.insertCell().textContent = booking.check_in;
                        row.insertCell().textContent = booking.check_out;
                        row.insertCell().textContent = booking.total_price + ' ₽';

                        // Ячейка со статусом
                        const statusCell = row.insertCell();
                        const span = document.createElement('span');
                        span.className = 'status ' + booking.status;
                        span.textContent = booking.status;
                        statusCell.appendChild(span);

                        // Ячейка с кнопкой "Отменить"
                        const actionCell = row.insertCell();
                        
                        // Показываем кнопку только если бронь не отменена
                        if (booking.status !== 'cancelled') {
                            const btn = document.createElement('button');
                            btn.className = 'btn-small btn-cancel';
                            btn.textContent = 'Отменить';
                            btn.dataset.id = booking.id; // Сохраняем ID брони в кнопку!
                            
                            // Вешаем обработчик клика
                            btn.addEventListener('click', function() {
                                cancelBooking(this.dataset.id, this, statusCell, span);
                            });

                            actionCell.appendChild(btn);
                        }
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
        function cancelBooking(bookingId, btn, statusCell, statusSpan) {
            // Спрашиваем подтверждение (чтобы не отменить случайно)
            if (!confirm('Вы уверены, что хотите отменить эту бронь?')) {
                return; 
            }

            // Отключаем кнопку, чтобы не нажали дважды
            btn.disabled = true;
            btn.textContent = 'Отмена...';

            // Формируем данные для отправки
            const formData = new FormData();
            formData.append('id', bookingId);

            // Отправляем POST-запрос на наш новый сервер
            fetch('php/cancel_booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Успех! Меняем внешний вид прямо на экране, без перезагрузки
                    statusSpan.className = 'status cancelled';
                    statusSpan.textContent = 'cancelled';
                    
                    // Удаляем кнопку, так как бронь уже отменена
                    btn.remove();
                } else {
                    // Если ошибка — показываем сообщение и возвращаем кнопку
                    alert(data.error || 'Ошибка при отмене');
                    btn.disabled = false;
                    btn.textContent = 'Отменить';
                }
            })
            .catch(() => {
                alert('Ошибка сервера. Попробуйте позже.');
                btn.disabled = false;
                btn.textContent = 'Отменить';
            });
        }
    </script>
    <script src="js/script.js"></script>
</body>
</html>