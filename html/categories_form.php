<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои категории | Финансовый трекер</title>
    <link rel="stylesheet" href="css/categories.css">
</head>
<body>
    <div class="categories-container">
        <!-- Шапка -->
        <div class="categories-header">
            <h1>Мои категории</h1>
            <div class="categories-header-right">
                <a href="dashboard.php" class="back-link">← Назад</a>
                <a href="add_categories.php" class="btn-add-category">Добавить</a>
            </div>
        </div>
        
        <!-- Сообщения -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message-success">
                ✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message-error">
                ❌ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Список категорий -->
        <div class="categories-list">
            <?php if (empty($categories)): ?>
                <div class="empty-state">
                    <p>😕 Категорий пока нет</p>
                    <p>Добавьте первую категорию для учета доходов или расходов</p>
                    <a href="add_categories.php" class="btn-add-category" style="margin-top: 20px;">➕ Добавить первую категорию</a>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                <div class="category-item <?php echo $category['type'] == 'income' ? 'category-income' : 'category-expense'; ?>">
                    <div class="category-info">
                        <h4><?php echo htmlspecialchars($category['name']); ?></h4>
                        <div class="category-meta">
                            <?php echo $category['type'] == 'income' ? '📈 Доход' : '📉 Расход'; ?>
                            • Создано: <?php echo date('d.m.Y', strtotime($category['created_at'])); ?>
                        </div>
                    </div>
                    <div>
                        <a href="config/delete_category.php?id=<?php echo $category['id']; ?>" class="btn-delete-category" onclick="return confirm('Удалить категорию?\n\n')">
                            🗑️ Удалить
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>