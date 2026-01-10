<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить категорию | Финансовый трекер</title>
    <link rel="stylesheet" href="css/add_categories.css">
    <link rel="stylesheet" href="css/forms.css">
</head>
<body>
    <div class="add-category-container">
        <a href="categories.php" class="add-category-back">← Назад к списку категорий</a>
        
        <h1>➕ Добавить категорию</h1>
        <p style="color: #666; margin-bottom: 30px;">Создайте новую категорию для учета доходов или расходов</p>
        
        <?php if (isset($error)): ?>
            <div class="message-error">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Название категории</label>
                <input type="text" id="name" name="name" required placeholder="Например: Лекарства, Образование, Инвестиции" class="category-input">
            </div>
            
            <div class="form-group">
                <label>Тип категории</label>
                <div class="type-selector">
                    <label class="type-option type-income <?php echo ($_POST['type'] ?? 'income') == 'income' ? 'selected' : ''; ?>">
                        <input type="radio" name="type" value="income" 
                               <?php echo ($_POST['type'] ?? 'income') == 'income' ? 'checked' : ''; ?>>
                        📈 Доход
                    </label>
                    
                    <label class="type-option type-expense <?php echo ($_POST['type'] ?? 'income') == 'expense' ? 'selected' : ''; ?>">
                        <input type="radio" name="type" value="expense" 
                               <?php echo ($_POST['type'] ?? 'income') == 'expense' ? 'checked' : ''; ?>>
                        📉 Расход
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 18px;">
                💾 Сохранить категорию
            </button>
        </form>
        
        <div class="examples-box">
            <h4>💡 Примеры категорий:</h4>
            <div class="examples-grid">
                <div>
                    <strong>Доходы:</strong>
                    <ul class="examples-list">
                        <li>Зарплата</li>
                        <li>Фриланс</li>
                        <li>Дивиденды</li>
                        <li>Сдача квартиры</li>
                    </ul>
                </div>
                <div>
                    <strong>Расходы:</strong>
                    <ul class="examples-list">
                        <li>Продукты</li>
                        <li>Транспорт</li>
                        <li>Лекарства</li>
                        <li>Развлечения</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Переключатель типа категории
        document.querySelectorAll('.type-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.type-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input').checked = true;
            });
        });
    </script>
</body>
</html>