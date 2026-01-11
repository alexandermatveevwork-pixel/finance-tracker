<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все операции | Финансовый трекер</title>
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/all_operations.css">
</head>
<body>
    <div class="dashboard-box">
        <!-- Шапка -->
        <div class="all-operations-header">
            <div>
                <h1>📋 Все операции</h1>
                <p style="color: #666; margin-top: 5px;">👤 <?php echo htmlspecialchars($user_email); ?></p>
            </div>
            <a href="dashboard.php" class="btn-back">← Назад в кабинет</a>
        </div>
        
        <!-- Сообщения -->
        <?php if (!empty($success_message)): ?>
        <div class="message-success">
            ✅ <?php echo htmlspecialchars($success_message); ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
        <div class="message-error">
            ❌ <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>
        
        <!-- Сводка -->
        <div class="operations-summary">
            <h3>Общая статистика <span class="operations-count"><?php echo $total_operations; ?> операций</span></h3>
            <div class="summary-stats">
                <div class="summary-item">
                    <div class="summary-label">📈 Доходы</div>
                    <div class="summary-value stat-income"><?php echo number_format($income, 2, '.', ' '); ?> ₽</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">📉 Расходы</div>
                    <div class="summary-value stat-expense"><?php echo number_format($expense, 2, '.', ' '); ?> ₽</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">💰 Баланс</div>
                    <div class="summary-value stat-balance"><?php echo number_format($balance, 2, '.', ' '); ?> ₽</div>
                </div>
            </div>
        </div>
        
        <!-- Действия -->
        <div class="operations-actions">
            <a href="export_page.php" target="_blank" class="btn-export">📤 Экспорт данных</a>
            <a href="dashboard.php#add-operation" class="btn-back">➕ Добавить операцию</a>
        </div>
        
        <!-- ТАБЛИЦА ВСЕХ ОПЕРАЦИЙ -->
        <div class="operations-table">
            <h3>📊 Все операции (<?php echo $total_operations; ?>)</h3>
            
            <?php if (empty($transactions)): ?>
                <div class="no-operations-large">
                    <h3>😕 Операций пока нет</h3>
                    <p>Добавьте первую операцию в личном кабинете!</p>
                    <a href="dashboard.php" class="btn-back" style="margin-top: 20px;">➕ Добавить первую операцию</a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Категория</th>
                                <th>Описание</th>
                                <th>Сумма</th>
                                <th>Действия</th>
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
                                    <a href="config/delete_operation.php?id=<?php echo $transaction['id']; ?>&return=all" 
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
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>