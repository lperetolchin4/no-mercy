<?php
require_once 'config/database.php';

echo "<h2>Проверка SQLite базы данных</h2>";

try {
    $db = getDB();
    
    echo "<p style='color: green;'>✅ Подключение к SQLite успешно!</p>";
    
    // Проверяем таблицы
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Таблицы в базе:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><strong>$table</strong></li>";
    }
    echo "</ul>";
    
    // Проверяем новости
    $stmt = $db->query("SELECT COUNT(*) as count FROM news");
    $newsCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Количество новостей: <strong>{$newsCount['count']}</strong></p>";
    
    // Проверяем контакты
    $stmt = $db->query("SELECT COUNT(*) as count FROM contacts");
    $contactsCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Количество сообщений в форме: <strong>{$contactsCount['count']}</strong></p>";
    
    echo "<p style='color: blue;'>✅ SQLite база работает корректно!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Ошибка: " . $e->getMessage() . "</p>";
    echo "<p>Проверьте права на запись в папку data/</p>";
}
?>