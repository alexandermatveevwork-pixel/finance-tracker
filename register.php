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
                    $success = 'Регистрация прошла успешно';
                    $_POST['email'] = '';
                } else {
                    $error = 'Ошибка при сохранении в БД';
                }
            }
        } catch (PDOException $e) {
            $error = 'Ошибка БД: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | Финансовый трекер</title>
    <style>
        /* Стили как на главной странице */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-3px);
        }
        
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: bold;
        }
        
        .error {
            background: #ffe6e6;
            color: #cc0000;
            border: 2px solid #ff9999;
        }
        
        .success {
            background: #e6ffe6;
            color: #006600;
            border: 2px solid #99ff99;
        }
        
        .links {
            text-align: center;
            margin-top: 30px;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            font-size: 16px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        /* Блок отладки */
        .debug {
            background: #f8f9fa;
            padding: 15px;
            margin-top: 30px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>📝 Регистрация</h1>
        <p class="subtitle">Создайте аккаунт для управления финансами</p>
        
        <?php if ($error): ?>
            <div class="message error">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="example@gmail.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" 
                       placeholder="Минимум 6 символов" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Подтвердите пароль:</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       placeholder="Повторите пароль" required>
            </div>
            
            <button type="submit" class="btn">Зарегистрироваться</button>
        </form>
        
        <div class="links">
            <a href="login.php">🔐 Уже есть аккаунт? Войдите</a>
            <a href="index.php">🏠 На главную страницу</a>
        </div>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="debug">
            <strong>Информация для отладки:</strong><br>
            Метод запроса: <?php echo $_SERVER['REQUEST_METHOD']; ?><br>
            Время: <?php echo date('H:i:s'); ?><br>
            <?php if (isset($db)): ?>
                Статус БД: ✅ Подключена<br>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>