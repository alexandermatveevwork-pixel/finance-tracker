<?php
// config/export_csv.php - экспорт операций в CSV
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once 'database.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// Получаем параметры периода
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // начало месяца по умолчанию
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // сегодня по умолчанию

// Получаем дополнительные параметры из формы
$include_stats = isset($_GET['include_stats']) ? (bool)$_GET['include_stats'] : true;
$include_headers = isset($_GET['include_headers']) ? (bool)$_GET['include_headers'] : true;
$group_by_category = isset($_GET['group_by_category']) ? (bool)$_GET['group_by_category'] : false;
$format = $_GET['format'] ?? 'csv'; // пока только CSV, но можно расширить

// Валидация дат
if (!strtotime($start_date) || !strtotime($end_date)) {
    die('❌ Неверный формат даты');
}

// Если start_date > end_date, меняем местами
if (strtotime($start_date) > strtotime($end_date)) {
    $temp = $start_date;
    $start_date = $end_date;
    $end_date = $temp;
}

try {
    // Получаем операции за период
    $stmt = $db->prepare("
        SELECT 
            t.id,
            t.date,
            t.category_name,
            t.description,
            t.amount,
            c.type as category_type,
            CASE 
                WHEN c.type = 'income' THEN 'Доход'
                WHEN c.type = 'expense' THEN 'Расход'
                ELSE 'Неизвестно'
            END as type_name
        FROM transactions t 
        LEFT JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = ? 
        AND t.date BETWEEN ? AND ?
        ORDER BY " . ($group_by_category ? "t.category_name, t.date DESC" : "t.date DESC, t.id DESC")
    );
    
    $stmt->execute([$user_id, $start_date, $end_date]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Если нет операций
    if (empty($transactions)) {
        die('❌ Нет операций за выбранный период');
    }
    
    // Подсчитываем статистику
    $total_income = 0;
    $total_expense = 0;
    $category_totals = [];
    
    foreach ($transactions as $transaction) {
        if ($transaction['category_type'] == 'income') {
            $total_income += $transaction['amount'];
        } else {
            $total_expense += $transaction['amount'];
        }
        
        // Суммируем по категориям для группировки
        $category = $transaction['category_name'];
        if (!isset($category_totals[$category])) {
            $category_totals[$category] = [
                'income' => 0,
                'expense' => 0,
                'count' => 0
            ];
        }
        
        if ($transaction['category_type'] == 'income') {
            $category_totals[$category]['income'] += $transaction['amount'];
        } else {
            $category_totals[$category]['expense'] += $transaction['amount'];
        }
        $category_totals[$category]['count']++;
    }
    
    $total_balance = $total_income - $total_expense;
    $total_operations = count($transactions);
    
    // Генерируем имя файла
    $filename = "финансы_" . 
                date('Y-m-d', strtotime($start_date)) . "_" . 
                date('Y-m-d', strtotime($end_date)) . "_" . 
                date('Ymd_His') . ".csv";
    
    // Устанавливаем заголовки для скачивания файла
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Открываем поток вывода
    $output = fopen('php://output', 'w');
    
    // Добавляем BOM для корректного отображения кириллицы в Excel
    fwrite($output, "\xEF\xBB\xBF");
    
    // Заголовок документа
    fputcsv($output, ["ФИНАНСОВЫЙ ТРЕКЕР - ЭКСПОРТ ОПЕРАЦИЙ"], ';');
    fputcsv($output, [], ';');
    
    // Информация о пользователе и периоде
    fputcsv($output, ["Пользователь:", $user_email], ';');
    fputcsv($output, ["Период:", "с " . $start_date . " по " . $end_date], ';');
    fputcsv($output, ["Дата экспорта:", date('d.m.Y H:i:s')], ';');
    fputcsv($output, [], ';');
    
    // Статистика (если включена)
    if ($include_stats) {
        fputcsv($output, ["ОБЩАЯ СТАТИСТИКА ЗА ПЕРИОД"], ';');
        fputcsv($output, ["Доходы:", number_format($total_income, 2, '.', ' ') . ' ₽'], ';');
        fputcsv($output, ["Расходы:", number_format($total_expense, 2, '.', ' ') . ' ₽'], ';');
        fputcsv($output, ["Баланс:", number_format($total_balance, 2, '.', ' ') . ' ₽'], ';');
        fputcsv($output, ["Количество операций:", $total_operations], ';');
        fputcsv($output, [], ';');
        
        // Статистика по категориям
        if (!empty($category_totals)) {
            fputcsv($output, ["СТАТИСТИКА ПО КАТЕГОРИЯМ"], ';');
            foreach ($category_totals as $category => $stats) {
                $category_balance = $stats['income'] - $stats['expense'];
                $sign = $category_balance >= 0 ? '+' : '';
                
                fputcsv($output, [
                    $category . ":",
                    "операций: " . $stats['count'] . ", " .
                    "баланс: " . $sign . number_format($category_balance, 2, '.', ' ') . ' ₽'
                ], ';');
            }
            fputcsv($output, [], ';');
        }
    }
    
    // Заголовки таблицы (если включены)
    if ($include_headers) {
        fputcsv($output, ["ДЕТАЛЬНЫЙ ОТЧЕТ ОПЕРАЦИЙ"], ';');
        fputcsv($output, [
            'Дата',
            'Тип операции',
            'Категория', 
            'Описание',
            'Сумма (₽)',
            'Примечание'
        ], ';');
    }
    
    // Вывод операций
    if ($group_by_category) {
        // Группировка по категориям
        $grouped_transactions = [];
        foreach ($transactions as $transaction) {
            $category = $transaction['category_name'];
            if (!isset($grouped_transactions[$category])) {
                $grouped_transactions[$category] = [];
            }
            $grouped_transactions[$category][] = $transaction;
        }
        
        // Выводим сгруппированные данные
        foreach ($grouped_transactions as $category => $items) {
            // Заголовок категории
            fputcsv($output, ["--- КАТЕГОРИЯ: " . $category . " ---"], ';');
            
            // Сумма по категории
            $cat_income = $category_totals[$category]['income'] ?? 0;
            $cat_expense = $category_totals[$category]['expense'] ?? 0;
            $cat_balance = $cat_income - $cat_expense;
            $sign = $cat_balance >= 0 ? '+' : '';
            
            fputcsv($output, [
                "Итого по категории:",
                $sign . number_format($cat_balance, 2, '.', ' ') . ' ₽',
                "(доходы: " . number_format($cat_income, 2, '.', ' ') . ' ₽, ' .
                "расходы: " . number_format($cat_expense, 2, '.', ' ') . ' ₽)'
            ], ';');
            
            // Операции в категории
            foreach ($items as $transaction) {
                $amount_display = number_format($transaction['amount'], 2, '.', ' ');
                $sign = $transaction['category_type'] == 'income' ? '+' : '-';
                
                fputcsv($output, [
                    $transaction['date'],
                    $transaction['type_name'],
                    $transaction['category_name'],
                    $transaction['description'] ?: '-',
                    $amount_display,
                    $sign . $amount_display . ' ₽'
                ], ';');
            }
            
            fputcsv($output, [], ';'); // Пустая строка между категориями
        }
    } else {
        // Обычный вывод (хронологический порядок)
        foreach ($transactions as $transaction) {
            $amount_display = number_format($transaction['amount'], 2, '.', ' ');
            $sign = $transaction['category_type'] == 'income' ? '+' : '-';
            
            fputcsv($output, [
                $transaction['date'],
                $transaction['type_name'],
                $transaction['category_name'],
                $transaction['description'] ?: '-',
                $amount_display,
                $sign . $amount_display . ' ₽'
            ], ';');
        }
    }
    
    // Подвал документа
    fputcsv($output, [], ';');
    fputcsv($output, ["--- КОНЕЦ ОТЧЕТА ---"], ';');
    fputcsv($output, ["Сгенерировано Финансовым Трекером"], ';');
    fputcsv($output, ["https://github.com/alexandermatveevwork-pixel/finance-tracker"], ';');
    
    fclose($output);
    
} catch (Exception $e) {
    // Если произошла ошибка, показываем сообщение
    die('❌ Ошибка при экспорте: ' . htmlspecialchars($e->getMessage()));
}