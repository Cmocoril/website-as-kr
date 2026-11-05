<?php
session_start();
require_once 'db_connect.php';

// Проверяем, что форма отправлена методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

// Получаем данные из формы
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Валидация
$errors = [];

if (empty($username)) {
    $errors[] = "Введите имя";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Введите корректный email";
}
if (strlen($password) < 6) {
    $errors[] = "Пароль должен быть не менее 6 символов";
}
if ($password !== $confirm_password) {
    $errors[] = "Пароли не совпадают";
}

// Проверяем, существует ли уже пользователь с таким email
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Пользователь с таким email уже зарегистрирован";
    }
}

// Если есть ошибки — возвращаем на форму
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['old_data'] = [
        'username' => $username,
        'email' => $email,
        'phone' => $phone
    ];
    header('Location: ../register.php');
    exit;
}

// Хэшируем пароль и сохраняем пользователя
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (username, email, phone, password) 
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$username, $email, $phone, $hashed_password]);

// Автоматически логиним пользователя после регистрации
$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['username'] = $username;
$_SESSION['email'] = $email;
$_SESSION['phone'] = $phone;

header('Location: ../cabinet.php');
exit;
?>