<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет | Финансовый трекер</title>
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="dashboard-box">
        <!-- Шапка -->
        <div class="dashboard-header">
            <h1>💰 Личный кабинет</h1>
            <div class="user-info">
                <span class="user-email">👤 <?php echo htmlspecialchars($user_email); ?></span>
                <a href="config/logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>
        
        <!-- Приветствие -->
        <div class="welcome-message">
            <h2 class="welcome-title">✅ Вы успешно вошли!</h2>
            <p>
                Добро пожаловать в ваш финансовый трекер. Здесь вы можете управлять доходами и расходами.
            </p>
        </div>
        
        <!-- Статистика с РЕАЛЬНЫМИ данными -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <div>📈 Доходы</div>
                <div class="stat-value stat-income"><?php echo number_format($income, 2, '.', ' '); ?> ₽</div>
                <small>За все время</small>
            </div>
            
            <div class="stat-card">
                <div>📉 Расходы</div>
                <div class="stat-value stat-expense"><?php echo number_format($expense, 2, '.', ' '); ?> ₽</div>
                <small>За все время</small>
            </div>
            
            <div class="stat-card">
                <div>💰 Баланс</div>
                <div class="stat-value stat-balance"><?php echo number_format($balance, 2, '.', ' '); ?> ₽</div>
                <small>Текущий</small>
            </div>
        </div>
        
        <!-- ФОРМА ДОБАВЛЕНИЯ ОПЕРАЦИИ -->
        <div class="add-operation-form">
            <h3>➕ Добавить операцию</h3>
            <form method="POST" action="config/add_operation.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="amount">Сумма (₽)</label>
                        <input type="number" step="0.01" id="amount" name="amount" required 
                               placeholder="0.00" min="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Категория</label>
                        <select id="category" name="category_id" required>
                            <option value="">Выберите категорию</option>
                            <optgroup label="📈 Доходы">
                                <?php foreach ($categories as $cat): 
                                    if ($cat['type'] == 'income'): ?>
                                    <option value="<?php echo $cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endif; endforeach; ?>
                            </optgroup>
                            <optgroup label="📉 Расходы">
                                <?php foreach ($categories as $cat): 
                                    if ($cat['type'] == 'expense'): ?>
                                    <option value="<?php echo $cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endif; endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Дата</label>
                        <input type="date" id="date" name="date" 
                               value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание (необязательно)</label>
                    <input type="text" id="description" name="description" 
                           placeholder="Например: Зарплата за январь">
                </div>
                
                    <button type="submit" class="btn-submit">💾 Сохранить операцию</button>
            </form>
        </div>
        
        <!-- ТАБЛИЦА ОПЕРАЦИЙ -->
        <div class="operations-table">
            <h3>📝 Последние операции</h3>
            
            <?php if (empty($transactions)): ?>
                <div class="no-operations">
                    <p>Операций пока нет. Добавьте первую!</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Категория</th>
                            <th>Описание</th>
                            <th>Сумма</th>
                            <th>Удалить</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): 
                            $is_income = $transaction['category_type'] == 'income';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['description'] ?: '-'); ?></td>
                            <td class="<?php echo $is_income ? 'amount-income' : 'amount-expense'; ?>">
                                <?php echo $is_income ? '+' : '-'; ?>
                                <?php echo number_format($transaction['amount'], 2, '.', ' '); ?> ₽
                            </td>
                            <td class="action-cell">
                            <a href="config/delete_operation.php?id=<?php echo $transaction['id']; ?>" 
                            class="delete-btn" 
                            onclick="return confirm('Удалить операцию?\n\n<?php echo htmlspecialchars($transaction['category_name']) . ' - ' . number_format($transaction['amount'], 2, '.', ' ') . ' ₽\nДата: ' . $transaction['date']; ?>')"
                            title="Удалить операцию">
                            ✕
                            </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Ссылки -->
        <div class="actions">
            <a href="index.php" class="btn-home">← На главную</a>
            <a href="categories.php" class="btn-home">🏷️ Добавить категории</a>
        </div>
    </div>
</body>
</html>