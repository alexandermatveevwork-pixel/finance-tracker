<?php

//Добавление операций (ДОХОД ИЛИ РАСХОД)

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
            $check_stmt = $db->prepare("SELECT id FROM categories WHERE id = ? AND user_id = ?");
            $check_stmt->execute([$category_id, $user_id]);

            if ($check_stmt->rowCount() > 0) {
                $stmt = $db->prepare("
                    INSERT INTO transactions (user_id, category_id, amount, description, date) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$user_id, $category_id, $amount, $description, $date]);

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