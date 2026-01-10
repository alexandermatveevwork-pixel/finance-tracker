<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

$categories = [];
try {
    $stmt = $db->prepare("SELECT id, name, type, created_at FROM categories WHERE user_id = ? ORDER BY type, name");
    $stmt->execute([$user_id]);

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Ошибка загрузки категорий';
}

require_once 'html/categories_form.php';
