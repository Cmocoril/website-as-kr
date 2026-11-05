<?php
session_start();
require_once 'db_connect.php';

// Авторизация
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Только POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$user_id   = $_SESSION['user_id'];
$cabin_id  = (int)($_POST['cabin_id'] ?? 0);
$check_in  = $_POST['check_in']  ?? '';
$check_out = $_POST['check_out'] ?? '';
$today     = date('Y-m-d');

$errors = [];

// Валидация
if (!$cabin_id) {
    $errors[] = 'Не указан домик';
}
if (!strtotime($check_in) || $check_in < $today) {
    $errors[] = 'Некорректная дата заезда';
}
if (!strtotime($check_out) || $check_out <= $check_in) {
    $errors[] = 'Дата выезда должна быть позже даты заезда';
}

// Проверка пересечения с существующими бронями
if (empty($errors)) {
    $stmt = $pdo->prepare("
        SELECT 1 FROM bookings
        WHERE cabin_id = ?
          AND status IN ('pending', 'confirmed')
          AND check_in < ?
          AND check_out > ?
        LIMIT 1
    ");
    $stmt->execute([$cabin_id, $check_out, $check_in]);
    if ($stmt->fetch()) {
        $errors[] = 'Эти даты уже заняты. Выберите другие.';
    }
}

// Расчёт стоимости и INSERT
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT price_per_night FROM cabins WHERE id = ?");
    $stmt->execute([$cabin_id]);
    $cabin = $stmt->fetch();

    if (!$cabin) {
        $errors[] = 'Домик не найден';
    } else {
        $nights = (strtotime($check_out) - strtotime($check_in)) / 86400;
        $total  = $nights * $cabin['price_per_night'];

        $pdo->prepare("
            INSERT INTO bookings (user_id, cabin_id, check_in, check_out, total_price, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ")->execute([$user_id, $cabin_id, $check_in, $check_out, $total]);

        header('Location: ../cabinet.php');
        exit;
    }
}

// Если были ошибки — назад на форму
$_SESSION['booking_errors'] = $errors;
header("Location: ../booking.php?cabin_id=$cabin_id");
exit;