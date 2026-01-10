<?php
//удаление операции
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once 'database.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header('Location: ../dashboard.php');
    exit();
}

$operation_id = intval($_GET['id']);

try {
    // Проверяем, что операция принадлежит пользователю
    $check_stmt = $db->prepare("SELECT id FROM transactions WHERE id = ? AND user_id = ?");
    $check_stmt->execute([$operation_id, $user_id]);
    
    if ($check_stmt->rowCount() > 0) {
        // Удаляем операцию
        $delete_stmt = $db->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
        $delete_stmt->execute([$operation_id, $user_id]);
        
        $_SESSION['success'] = '✅ Операция удалена';
    } else {
        $_SESSION['error'] = '❌ Операция не найдена или нет доступа';
    }
} catch (Exception $e) {
    $_SESSION['error'] = '❌ Ошибка при удалении: ' . $e->getMessage();
}

header('Location: ../dashboard.php');
exit();
?>