<?php
// 1. Подключаем настройки БД
require_once 'config/database.php';

echo "<h2>🎯 Тест реального подключения к БД</h2>";

// 2. Проверяем что $db создан
if (!isset($db)) {
    die("❌ Ошибка: \$db не создан");
}

echo "✅ 1. Объект PDO создан<br>";

// 3. Пробуем выполнить ПРОСТОЙ запрос
try {
    echo "✅ 2. Пытаемся выполнить запрос...<br>";
    
    // Запрос 1: Какая версия MySQL?
    $stmt = $db->query("SELECT VERSION() as mysql_version");
    $version = $stmt->fetch();
    echo "✅ 3. Версия MySQL: " . $version['mysql_version'] . "<br>";
    
    // Запрос 2: Есть ли наша таблица users?
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    $has_users = $stmt->fetch();
    
    if (!empty($has_users)) {
        echo "✅ 4. Таблица 'users' существует!<br>";
        
        // Запрос 3: Сколько записей в users?
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $count = $stmt->fetch();
        echo "✅ 5. Записей в таблице users: " . $count['count'] . "<br>";
    } else {
        echo "❌ 4. Таблица 'users' НЕ существует!<br>";
        echo "<p style='color: orange;'>Нужно создать таблицу через phpMyAdmin</p>";
    }
    
    // Запрос 4: Покажем все таблицы
    echo "<h3>📋 Все таблицы в базе:</h3>";
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            $table_name = $table['Tables_in_finance_tracker'] ?? $table[0];
            echo "<li>" . htmlspecialchars($table_name) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Таблиц нет</p>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #ffe6e6; padding: 15px; border-radius: 5px;'>";
    echo "❌ Ошибка при запросе:<br>";
    echo "<strong>" . htmlspecialchars($e->getMessage()) . "</strong>";
    echo "</div>";
}

echo "<hr>";
echo "<p style='color: green; font-weight: bold;'>🎉 Если видите версию MySQL - подключение РАБОТАЕТ!</p>";
?>