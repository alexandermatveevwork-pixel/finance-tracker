<?php
// delete_category.php - простое удаление категории
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../categories.php');
    exit();
}

require_once 'database.php';

$user_id = $_SESSION['user_id'];
$category_id = intval($_GET['id']);

try {
    // Получаем название категории перед удалением (для сообщения)
    $stmt = $db->prepare("SELECT name FROM categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$category_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        $category_name = $category['name'];
        
        // Удаляем категорию
        $delete_stmt = $db->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
        $delete_stmt->execute([$category_id, $user_id]);
        
        $_SESSION['success'] = 'Категория "' . htmlspecialchars($category_name) . '" удалена';
    } else {
        $_SESSION['error'] = 'Категория не найдена';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Ошибка при удалении: ' . $e->getMessage();
}

header('Location: ../categories.php');
exit();
?>