<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | Финансовый трекер</title>
    <link rel="stylesheet" href="css/forms.css">
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
                ✅ <?php echo htmlspecialchars($success); ?>
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
                <div class="password-hint">Пароль может быть любой от 6 символов, но что бы менеджер паролей не ругался придумайте что нибудь уникальное (не "123456" или "admin")</div>
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
    </div>
</body>
</html>