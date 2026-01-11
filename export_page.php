<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// Получаем минимальную и максимальную даты операций
$date_range = ['min' => date('Y-m-01'), 'max' => date('Y-m-d')];
try {
    $stmt = $db->prepare("SELECT MIN(date) as min_date, MAX(date) as max_date FROM transactions WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $range = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($range['min_date']) {
        $date_range['min'] = $range['min_date'];
        $date_range['max'] = $range['max_date'];
    }
} catch (Exception $e) {
    // Используем значения по умолчанию
}

require_once 'html/export_page_form.php';