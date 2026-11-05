<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if (empty($email) || empty($password)) {
    $errors[] = "Заполните все поля";
}

if (empty($errors)) {
    // Ищем пользователя по email
    $stmt = $pdo->prepare("SELECT id, username, email, phone, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Проверяем пароль
    if ($user && password_verify($password, $user['password'])) {
        // Успешный вход — создаём сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'];
        
        header('Location: ../cabinet.php');
        exit;
    } else {
        $errors[] = "Неверный email или пароль";
    }
}

// Если ошибка — возвращаем на форму
$_SESSION['login_errors'] = $errors;
header('Location: ../login.php');
exit;
?>