<?php
session_start();

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

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// Получаем категории пользователя
$categories = [];
try {
    $stmt = $db->prepare("SELECT id, name, type FROM categories WHERE user_id = ? ORDER BY name");
    $stmt->execute([$user_id]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Пока игнорируем ошибку
}

// Получаем последние 5 операций (ОБНОВЛЕННЫЙ ЗАПРОС!)
$transactions = [];
try {
    $stmt = $db->prepare("
        SELECT 
            t.id,
            t.amount, 
            t.date, 
            t.description, 
            COALESCE(t.category_name, 'Без категории') as category_name,
            COALESCE(c.type, 'expense') as category_type 
        FROM transactions t 
        LEFT JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? 
        ORDER BY t.date DESC, t.id DESC
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Пока игнорируем ошибку
}

// Собираем из БД доходы и расходы в 2 переменные
$income = 0;
$expense = 0;
try {
    // Складываем все доходы (ЭТОТ ЗАПРОС ТОЖЕ НУЖНО ОБНОВИТЬ!)
    $stmt = $db->prepare("
        SELECT SUM(t.amount) as total 
        FROM transactions t 
        LEFT JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? AND (c.type = 'income' OR t.category_name IN 
            (SELECT name FROM categories WHERE type = 'income' AND user_id = ?))
    ");
    $stmt->execute([$user_id, $user_id]);
    $income_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $income = $income_result['total'] ?? 0;
    
    // Складываем все расходы (И ЭТОТ ТОЖЕ!)
    $stmt = $db->prepare("
        SELECT SUM(t.amount) as total 
        FROM transactions t 
        LEFT JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? AND (c.type = 'expense' OR t.category_name IN 
            (SELECT name FROM categories WHERE type = 'expense' AND user_id = ?))
    ");
    $stmt->execute([$user_id, $user_id]);
    $expense_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $expense = $expense_result['total'] ?? 0;
} catch (Exception $e) {
    // Пока игнорируем ошибку
    try {
        // Старый запрос для совместимости
        $stmt = $db->prepare("SELECT SUM(amount) as total FROM transactions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        // Просто делим пополам для примера
        $income = $total / 2;
        $expense = $total / 2;
    } catch (Exception $e2) {
        // Игнорируем
    }
}

$balance = $income - $expense;

require_once 'html/dashboard_form.php';