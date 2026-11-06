<?php
require_once 'php/auth_check.php';
require_once 'php/db_connect.php';

$cabin_id = (int)($_GET['cabin_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM cabins WHERE id = ?");
$stmt->execute([$cabin_id]);
$cabin = $stmt->fetch();

if (!$cabin) {
    header('Location: index.php');
    exit;
}

$errors = $_SESSION['booking_errors'] ?? [];
unset($_SESSION['booking_errors']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование — <?= htmlspecialchars($cabin['name']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
     <header>
        <nav>
            <div class="logo">🌲 Лесная поляна</div>
            
            <button class="burger" id="burger-btn">☰</button>
            
            <ul id="nav-menu">
                <li><a href="index.php">Главная</a></li>
                <li><a href="cabinet.php">Личный кабинет</a></li>
                <li><a href="php/logout.php">Выйти</a></li>
            </ul>
        </nav>
    </header>

    <div class="form-container">
        <h2><?= htmlspecialchars($cabin['name']) ?></h2>
        <p style="text-align:center; color:#666; margin-bottom:1rem;">
            <?= number_format($cabin['price_per_night'], 0, '', ' ') ?> ₽/ночь · до <?= (int)$cabin['capacity'] ?> чел.
        </p>

        <?php if (!empty($errors)): ?>
            <div class="form-errors" style="color:#c0392b; margin-bottom:1rem;">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="php/create_booking.php" method="POST">
            <input type="hidden" name="cabin_id" value="<?= (int)$cabin['id'] ?>">
            <div class="form-group">
                <label for="check_in">Дата заезда</label>
                <input type="date" id="check_in" name="check_in" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label for="check_out">Дата выезда</label>
                <input type="date" id="check_out" name="check_out" required min="<?= date('Y-m-d') ?>">
            </div>
            <button type="submit" class="btn">Забронировать</button>
        </form>

        <div class="form-footer">
            <p><a href="cabinet.php">← Назад в кабинет</a></p>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>