<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

//Получаем категории пользователя
$categories = [];
try {
    $stmt = $db->prepare("SELECT id, name, type FROM categories WHERE user_id = ? ORDER BY name");
    $stmt->execute([$user_id]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Пока игнорируем ошибку
}

//Получаем последние 5 операций
$transactions = [];
try {
    $stmt = $db->prepare("
        SELECT t.amount, t.date, t.description, c.name as category_name, c.type as category_type 
        FROM transactions t 
        JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? 
        ORDER BY t.date DESC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Пока игнорируем ошибку
}

//Собираем из БД доходы и расходы в 2 переменные
$income = 0;
$expense = 0;
try {
    //Складываем все доходы
    $stmt = $db->prepare("
        SELECT SUM(t.amount) as total 
        FROM transactions t 
        JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? AND c.type = 'income'
    ");
    $stmt->execute([$user_id]);
    $income_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $income = $income_result['total'] ?? 0;
    
    // Складываем все расходы
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
    // Пока игнорируем ошибку
}

$balance = $income - $expense;

require_once 'html/dashboard_form.php';