<?php
session_start();
require_once 'db_connect.php';

// 1. Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

// 2. Принимаем только POST-запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
    exit;
}

$user_id = $_SESSION['user_id'];
// Получаем ID брони (приводим к числу)
$booking_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($booking_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный ID брони']);
    exit;
}

try {
    // 3. Обновляем статус. 
    // Важно: в WHERE мы указываем и id, и user_id!
    // Это защита: пользователь не сможет отменить чужую бронь.
    $stmt = $pdo->prepare("
        UPDATE bookings 
        SET status = 'cancelled' 
        WHERE id = ? AND user_id = ? AND status != 'cancelled'
    ");
    $stmt->execute([$booking_id, $user_id]);

    // Если ни одна строка не обновилась — значит, бронь не найдена или уже отменена
    if ($stmt->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Бронь не найдена или уже отменена']);
        exit;
    }

    // 4. Отправляем успешный ответ в формате JSON
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка БД']);
}
?>