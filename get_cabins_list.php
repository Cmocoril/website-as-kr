<?php
require_once 'php/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->prepare("SELECT id, name, price_per_night FROM cabins ORDER BY price_per_night ASC");
    $stmt->execute();
    echo json_encode($stmt->fetchAll());
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>