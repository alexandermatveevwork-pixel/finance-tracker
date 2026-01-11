<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// Получаем ВСЕ операции пользователя
$transactions = [];
try {
    $stmt = $db->prepare("
        SELECT 
            t.id,
            t.amount, 
            t.date, 
            t.description, 
            t.category_name,
            c.type as category_type
        FROM transactions t 
        LEFT JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? 
        ORDER BY t.date DESC, t.id DESC
    ");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $transactions = [];
}

// Получаем общую статистику
$income = 0;
$expense = 0;
try {
    $stmt = $db->prepare("
        SELECT SUM(t.amount) as total 
        FROM transactions t 
        JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? AND c.type = 'income'
    ");
    $stmt->execute([$user_id]);
    $income_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $income = $income_result['total'] ?? 0;
    
    $stmt = $db->prepare("
        SELECT SUM(t.amount) as total 
        FROM transactions t 
        JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? AND c.type = 'expense'
    ");
    $stmt->execute([$user_id]);
    $expense_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $expense = $expense_result['total'] ?? 0;
} catch (Exception $e) {
    $income = 0;
    $expense = 0;
}

$balance = $income - $expense;
$total_operations = count($transactions);

// Сообщения об успехе/ошибке
$success_message = '';
$error_message = '';

if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}

require_once 'html/all_operations_form.php';