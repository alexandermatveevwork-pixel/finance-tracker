<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | Финансовый трекер</title>
    <link rel="stylesheet" href="css/forms.css">
</head>
<body>
    <div class="form-container">
        <h1>🔐 Вход</h1>
        <p class="subtitle">Войдите в свой аккаунт</p>
        
        <?php if ($success): ?>
            <div class="message success">
                ✅ <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error">
                ❌ <?php echo htmlspecialchars($error); ?>
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
                       placeholder="Введите ваш пароль" required>
            </div>
            
            <button type="submit" class="btn">Войти</button>
        </form>
        
        <div class="links">
            <a href="register.php">📝 Нет аккаунта? Зарегистрируйтесь</a>
            <a href="index.php">🏠 На главную страницу</a>
        </div>
    </div>
</body>
</html>