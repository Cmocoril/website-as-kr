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
    <title>Регистрация - Турбаза</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🌲 Лесная поляна</div>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="login.php">Вход</a></li>
            </ul>
        </nav>
    </header>

    <div class="form-container">
        <h2>Регистрация</h2>

        <?php if (!empty($errors)): ?>
            <div class="form-errors" style="color:#c0392b; margin-bottom:1rem;">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="php/register.php" method="POST">
            <div class="form-group">
                <label for="username">Имя</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone" placeholder="+7 (999) 123-45-67">
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label for="confirm_password">Подтвердите пароль</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Зарегистрироваться</button>
        </form>
        <div class="form-footer">
            <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
        </div>
    </div>
</body>
</html>