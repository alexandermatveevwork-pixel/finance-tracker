<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экспорт данных | Финансовый трекер</title>
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/export.css">
</head>
<body>
    <div class="export-container">
        <!-- Шапка -->
        <div class="export-header">
            <h1>📤 Экспорт данных</h1>
            <p class="user-info">👤 <?php echo htmlspecialchars($user_email); ?></p>
            <a href="all_operations.php" class="btn-close" title="Закрыть">×</a>
        </div>
        
        <!-- Основной контент -->
        <div class="export-content">
            <div class="export-card">
                <div class="card-header">
                    <h2>📊 Выберите параметры экспорта</h2>
                    <p>Настройте данные, которые хотите экспортировать</p>
                </div>
                
                <form method="GET" action="config/export_csv.php" target="_blank" class="export-form">
                    <!-- Период -->
                    <div class="form-section">
                        <h3>📅 Период</h3>
                        <div class="date-range">
                            <div class="date-input">
                                <label for="start_date">Начальная дата</label>
                                <input type="date" id="start_date" name="start_date" 
                                       value="<?php echo $date_range['min']; ?>"
                                       min="<?php echo $date_range['min']; ?>"
                                       max="<?php echo $date_range['max']; ?>"
                                       required>
                            </div>
                            <div class="date-separator">→</div>
                            <div class="date-input">
                                <label for="end_date">Конечная дата</label>
                                <input type="date" id="end_date" name="end_date" 
                                       value="<?php echo $date_range['max']; ?>"
                                       min="<?php echo $date_range['min']; ?>"
                                       max="<?php echo $date_range['max']; ?>"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Быстрые периоды -->
                        <div class="quick-periods">
                            <span>Быстрый выбор:</span>
                            <button type="button" class="period-btn" data-start="<?php echo date('Y-m-01'); ?>" data-end="<?php echo date('Y-m-d'); ?>">
                                Текущий месяц
                            </button>
                            <button type="button" class="period-btn" data-start="<?php echo date('Y-m-d', strtotime('-30 days')); ?>" data-end="<?php echo date('Y-m-d'); ?>">
                                Последние 30 дней
                            </button>
                            <button type="button" class="period-btn" data-start="<?php echo date('Y-01-01'); ?>" data-end="<?php echo date('Y-m-d'); ?>">
                                Текущий год
                            </button>
                            <button type="button" class="period-btn" data-start="<?php echo $date_range['min']; ?>" data-end="<?php echo $date_range['max']; ?>">
                                Весь период
                            </button>
                        </div>
                    </div>
                    
                    <!-- Формат -->
                    <div class="form-section">
                        <h3>📁 Формат файла</h3>
                        <div class="format-options">
                            <label class="format-option">
                                <input type="radio" name="format" value="csv" checked>
                                <div class="format-card">
                                    <div class="format-icon">📊</div>
                                    <div class="format-info">
                                        <strong>CSV</strong>
                                        <small>Таблица Excel/Google Sheets</small>
                                    </div>
                                </div>
                            </label>
                            
                            <!-- Можно добавить другие форматы позже -->
                            <label class="format-option">
                                <input type="radio" name="format" value="json" disabled>
                                <div class="format-card disabled">
                                    <div class="format-icon">📄</div>
                                    <div class="format-info">
                                        <strong>JSON</strong>
                                        <small>Скоро будет доступно</small>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="format-option">
                                <input type="radio" name="format" value="pdf" disabled>
                                <div class="format-card disabled">
                                    <div class="format-icon">📋</div>
                                    <div class="format-info">
                                        <strong>PDF</strong>
                                        <small>Скоро будет доступно</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Дополнительные настройки -->
                    <div class="form-section">
                        <h3>⚙️ Дополнительно</h3>
                        <div class="additional-options">
                            <label class="checkbox-option">
                                <input type="checkbox" name="include_stats" checked>
                                <span>Включить сводную статистику</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="include_headers" checked>
                                <span>Включить заголовки столбцов</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="group_by_category" checked>
                                <span>Группировать по категориям</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Предварительный просмотр -->
                    <div class="form-section preview-section">
                        <h3>👁️ Предварительный просмотр</h3>
                        <div class="preview-box">
                            <p>После настройки параметров нажмите "Сгенерировать", чтобы увидеть предварительный вид данных.</p>
                            <div class="preview-stats">
                                <div class="preview-stat">
                                    <span>Операций:</span>
                                    <strong id="preview-count">...</strong>
                                </div>
                                <div class="preview-stat">
                                    <span>Доходы:</span>
                                    <strong id="preview-income" class="stat-income">...</strong>
                                </div>
                                <div class="preview-stat">
                                    <span>Расходы:</span>
                                    <strong id="preview-expense" class="stat-expense">...</strong>
                                </div>
                                <div class="preview-stat">
                                    <span>Баланс:</span>
                                    <strong id="preview-balance" class="stat-balance">...</strong>
                                </div>
                            </div>
                            <button type="button" id="generate-preview" class="btn-preview">
                                🔄 Сгенерировать предпросмотр
                            </button>
                        </div>
                    </div>
                    
                    <!-- Кнопки действий -->
                    <div class="form-actions">
                        <a href="all_operations.php" class="btn-cancel">← Отмена</a>
                        <button type="submit" class="btn-export-main">
                            📥 Скачать файл
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Информационная панель -->
            <div class="info-sidebar">
                <div class="info-card">
                    <h3>💡 Как это работает?</h3>
                    <ul>
                        <li>Выберите период для экспорта</li>
                        <li>Настройте дополнительные параметры</li>
                        <li>Сгенерируйте предпросмотр при необходимости</li>
                        <li>Скачайте файл в выбранном формате</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>📊 Формат CSV</h3>
                    <ul>
                        <li>Совместим с Excel и Google Sheets</li>
                        <li>Корректная кодировка UTF-8</li>
                        <li>Разделитель — точка с запятой (;)</li>
                        <li>Автоматическое форматирование чисел</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>⚡ Быстрые советы</h3>
                    <ul>
                        <li>Экспортируйте данные регулярно для бэкапа</li>
                        <li>Используйте CSV для создания отчетов</li>
                        <li>Группировка по категориям упрощает анализ</li>
                        <li>Сохраняйте файлы с датой в названии</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
        <script>
        // Быстрые периоды
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('start_date').value = this.dataset.start;
                document.getElementById('end_date').value = this.dataset.end;
                updatePreview();
            });
        });
        
        // Обновление предпросмотра при изменении дат
        document.getElementById('start_date').addEventListener('change', updatePreview);
        document.getElementById('end_date').addEventListener('change', updatePreview);
        document.getElementById('generate-preview').addEventListener('click', updatePreview);
        
        // Функция обновления предпросмотра
        async function updatePreview() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const btn = document.getElementById('generate-preview');
            
            // Показываем загрузку
            btn.innerHTML = '⏳ Загрузка...';
            btn.disabled = true;
            
            try {
                // В реальном проекте здесь был бы AJAX запрос к серверу
                // Для демо просто покажем заглушку
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Обновляем предпросмотр
                document.getElementById('preview-count').textContent = '~';
                document.getElementById('preview-income').textContent = '~ ₽';
                document.getElementById('preview-expense').textContent = '~ ₽';
                document.getElementById('preview-balance').textContent = '~ ₽';
                
                // Показываем сообщение
                document.querySelector('.preview-box p').innerHTML = 
                    `Выбран период: <strong>${startDate}</strong> — <strong>${endDate}</strong><br>
                    Нажмите "Скачать файл" для получения данных.`;
                
            } catch (error) {
                console.error('Ошибка при обновлении предпросмотра:', error);
            } finally {
                btn.innerHTML = '🔄 Обновить предпросмотр';
                btn.disabled = false;
            }
        }
        
        // Инициализация предпросмотра
        document.addEventListener('DOMContentLoaded', updatePreview);
        
        // Закрытие по ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html>