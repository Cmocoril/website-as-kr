<?php
session_start();
require_once 'php/db_connect.php';

// 1. ПРОВЕРКА ДОСТУПА: Белый список email сотрудников
// Добавь сюда email тех, кто должен иметь доступ к аналитике
$allowed_emails = [
    'x77722855@gmail.com',  // Твой email из БД
    'admin@lespolyana.ru',  // Можно добавить других
];

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['email'], $allowed_emails)) {
    die('<h2 style="color:red; text-align:center; margin-top:50px;">Доступ запрещен. Эта страница только для сотрудников.</h2><p style="text-align:center;"><a href="index.php">Вернуться на главную</a></p>');
}

// 2. ОБРАБОТКА ДАТ ИЗ ФОРМЫ
$date_from = $_POST['date_from'] ?? date('Y-m-01'); // По умолчанию: 1-е число текущего месяца
$date_to = $_POST['date_to'] ?? date('Y-m-d');      // По умолчанию: сегодня

// 3. SQL ЗАПРОСЫ (работают с текущей структурой БД)

// А) Сумма продаж (общая выручка по подтвержденным броням)
$stmt = $pdo->prepare("
    SELECT SUM(total_price) AS total_amount 
    FROM bookings 
    WHERE status != 'cancelled' 
    AND created_at >= :date_from 
    AND created_at <= :date_to
");
$stmt->execute(['date_from' => $date_from . ' 00:00:00', 'date_to' => $date_to . ' 23:59:59']);
$total_sales = $stmt->fetch()['total_amount'] ?? 0;

// Б) Среднее значение (средний чек)
$stmt = $pdo->prepare("
    SELECT AVG(total_price) AS average_price 
    FROM bookings 
    WHERE status != 'cancelled' 
    AND created_at >= :date_from 
    AND created_at <= :date_to
");
$stmt->execute(['date_from' => $date_from . ' 00:00:00', 'date_to' => $date_to . ' 23:59:59']);
$average_check = $stmt->fetch()['average_price'] ?? 0;

// В) Прибыль (упрощенно: 30% от выручки)
// В реальном проекте здесь был бы JOIN с таблицей расходов
$profit = $total_sales * 0.30;

// Г) Наиболее популярная позиция (домик)
$stmt = $pdo->prepare("
    SELECT c.name, COUNT(b.id) AS total_bookings
    FROM bookings b
    JOIN cabins c ON b.cabin_id = c.id
    WHERE b.status != 'cancelled' 
    AND b.created_at >= :date_from 
    AND b.created_at <= :date_to
    GROUP BY c.id, c.name
    ORDER BY total_bookings DESC
    LIMIT 1
");
$stmt->execute(['date_from' => $date_from . ' 00:00:00', 'date_to' => $date_to . ' 23:59:59']);
$popular_cabin = $stmt->fetch();

// Д) Группировка данных (продажи по каждому домику)
$stmt = $pdo->prepare("
    SELECT c.name, SUM(b.total_price) AS total, COUNT(b.id) AS bookings_count
    FROM bookings b
    JOIN cabins c ON b.cabin_id = c.id
    WHERE b.status != 'cancelled' 
    AND b.created_at >= :date_from 
    AND b.created_at <= :date_to
    GROUP BY c.id, c.name
    ORDER BY total DESC
");
$stmt->execute(['date_from' => $date_from . ' 00:00:00', 'date_to' => $date_to . ' 23:59:59']);
$grouped_sales = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аналитика - Турбаза</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .analytics-container { max-width: 1000px; margin: 2rem auto; padding: 2rem; }
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .metric-card { background: #f8f9fa; padding: 1.5rem; border-radius: 10px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .metric-card h3 { font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; }
        .metric-card .value { font-size: 1.8rem; font-weight: bold; color: #2c3e50; }
        .metric-card .value.profit { color: #27ae60; }
        .filter-form { background: #fff; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .filter-form .form-group { margin-bottom: 0; }
        .popular-badge { background: #3498db; color: white; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.9rem; }
        .grouped-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .grouped-table th, .grouped-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ddd; }
        .grouped-table th { background: #2c3e50; color: white; }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">🌲 Лесная поляна</div>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="cabinet.php">Личный кабинет</a></li>
                <li><a href="php/logout.php">Выйти</a></li>
            </ul>
        </nav>
    </header>

    <div class="analytics-container">
        <h2>📊 Аналитический модуль</h2>
        
        <!-- Форма фильтрации по периоду -->
        <form method="POST" class="filter-form">
            <div class="form-group">
                <label for="date_from">Дата начала:</label>
                <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($date_from) ?>" required>
            </div>
            <div class="form-group">
                <label for="date_to">Дата окончания:</label>
                <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($date_to) ?>" required>
            </div>
            <button type="submit" class="btn">Рассчитать</button>
        </form>

        <!-- Основные показатели -->
        <div class="metrics-grid">
            <div class="metric-card">
                <h3>💰 Сумма продаж</h3>
                <div class="value"><?= number_format($total_sales, 0, ',', ' ') ?> ₽</div>
            </div>
            <div class="metric-card">
                <h3>📈 Средний чек</h3>
                <div class="value"><?= number_format($average_check, 0, ',', ' ') ?> ₽</div>
            </div>
            <div class="metric-card">
                <h3>📉 Прибыль (30%)</h3>
                <div class="value profit"><?= number_format($profit, 0, ',', ' ') ?> ₽</div>
            </div>
            <div class="metric-card">
                <h3>⭐ Популярный домик</h3>
                <div class="value" style="font-size: 1.2rem;">
                    <?= $popular_cabin ? htmlspecialchars($popular_cabin['name']) : 'Нет данных' ?>
                    <?php if ($popular_cabin): ?>
                        <br><span class="popular-badge"><?= $popular_cabin['total_bookings'] ?> бронирований</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Группировка по домикам -->
        <h3>📋 Продажи по домикам</h3>
        <?php if (count($grouped_sales) > 0): ?>
            <table class="grouped-table">
                <thead>
                    <tr>
                        <th>Домик</th>
                        <th>Количество бронирований</th>
                        <th>Общая сумма</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grouped_sales as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= $row['bookings_count'] ?></td>
                            <td><?= number_format($row['total'], 0, ',', ' ') ?> ₽</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #888;">Нет данных за выбранный период</p>
        <?php endif; ?>
    </div>
</body>
</html>