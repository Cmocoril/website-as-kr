<?php
session_start();

// Если пользователь не авторизован — отправляем на страницу входа
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>