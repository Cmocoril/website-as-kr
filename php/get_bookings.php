<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json; charset=utf-8');

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT username, email, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Получаем историю бронирований с названиями домиков
$stmt = $pdo->prepare("
    SELECT 
        b.id,
        c.name AS cabin_name,
        b.check_in,
        b.check_out,
        b.total_price,
        b.status,
        b.created_at
    FROM bookings b
    JOIN cabins c ON b.cabin_id = c.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

// Возвращаем JSON
echo json_encode([
    'user' => $user,
    'bookings' => $bookings
]);
?>