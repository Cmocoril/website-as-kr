<?php
session_start();
$errors = $_SESSION['login_errors'] ?? [];      // в register.php: register_errors
$old    = $_SESSION['old_data']    ?? [];
unset($_SESSION['login_errors'], $_SESSION['old_data']);  // в register.php — register_errors
if (isset($_SESSION['user_id'])) {
    header('Location: cabinet.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Турбаза</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🌲 Лесная поляна</div>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="register.php">Регистрация</a></li>
            </ul>
        </nav>
    </header>

    <div class="form-container">
        <h2>Вход в аккаунт</h2>
        <?php if (!empty($errors)): ?>
            <div class="form-errors" style="color:#c0392b; margin-bottom:1rem;">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="php/login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Войти</button>
        </form>
        <div class="form-footer">
            <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
        </div>
    </div>
</body>
</html>