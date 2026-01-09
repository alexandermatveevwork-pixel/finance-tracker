<?php

//Подключение к БД

$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'finance_tracker';

try {
    $db = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
        $user,
        $pass
    );
        
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //echo "Всё ок, работает";
        
} catch(PDOException $e) {
    die("Нет подключения к БД" . $e->getMessage());
}
