<?php
// config/add_operation.php - обработчик добавления операции
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once 'database.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['date'] ?? date('Y-m-d');

    if ($amount > 0 && $category_id > 0) {
        try {
            // Проверяем, что категория принадлежит пользователю И получаем её название
            $check_stmt = $db->prepare("SELECT id, name FROM categories WHERE id = ? AND user_id = ?");
            $check_stmt->execute([$category_id, $user_id]);

            if ($check_stmt->rowCount() > 0) {
                // Получаем название категории
                $category = $check_stmt->fetch(PDO::FETCH_ASSOC);
                $category_name = $category['name'];
                
                // Добавляем операцию с сохранением названия категории
                $stmt = $db->prepare("
                    INSERT INTO transactions (user_id, category_id, category_name, amount, description, date) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$user_id, $category_id, $category_name, $amount, $description, $date]);

                $_SESSION['success'] = '✅ Операция успешно добавлена!';
            } else {
                $_SESSION['error'] = '❌ Ошибка: неверная категория';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = '❌ Ошибка при сохранении: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = '❌ Заполните все обязательные поля';
    }
}

header('Location: ../dashboard.php');
exit();
?>