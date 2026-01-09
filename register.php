<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    //Проверки пустых полей
    if (empty($email) || empty($password)) {
        $error = 'Обязательные поля не заполнены';
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Неверный формат email';
    }

    //Проверка паролей
    elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    }
    elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    }

    // Если ошибок нет - проверяем email и регистрируем
    if (empty($error)) {
        try {
            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $error = 'Такой email уже занят';
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (email, password_hash) VALUES (?, ?)";
                $stmt = $db->prepare($sql);

                if ($stmt->execute([$email, $password_hash])) {
                    $_SESSION['registration_success'] = 'Регистрация прошла успешно!';
                    header('Location: login.php');
                    exit();
                } else {
                    $error = 'Ошибка при сохранении в БД';
                }
            }
        } catch (PDOException $e) {
            $error = 'Ошибка БД: ' . $e->getMessage();
        }
    }
}

require_once 'html/register_form.php';