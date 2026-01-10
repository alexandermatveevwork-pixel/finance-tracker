<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? 'expense';
    
    if (!empty($name)) {
        try {
            $check_stmt = $db->prepare("SELECT id FROM categories WHERE user_id = ? AND name = ?");
            $check_stmt->execute([$user_id, $name]);
            
            if ($check_stmt->rowCount() === 0) {
                $stmt = $db->prepare("INSERT INTO categories (user_id, name, type) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $name, $type]);
                
                $_SESSION['success'] = 'Категория "' . htmlspecialchars($name) . '" добавлена!';
                header('Location: categories.php');
                exit();
            } else {
                $error = 'Категория с таким названием уже существует';
            }
        } catch (Exception $e) {
            $error = 'Ошибка при сохранении: ' . $e->getMessage();
        }
    } else {
        $error = 'Введите название категории';
    }
}

require_once 'html/add_categories_form.php';