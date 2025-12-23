<?php
/**
 * Подключение к базе данных SQLite
 * Этот файл подключается на страницах, где нужен доступ к БД
 */

// Подключаем конфигурацию
require_once __DIR__ . '/../config/database.php';

// Создаем глобальное подключение
try {
    $pdo = getDB();
} catch (Exception $e) {
    // Показываем понятную ошибку
    die('<div style="background: #333; color: white; padding: 20px; border-radius: 10px; margin: 20px;">
        <h2 style="color: #ff6b6b;">⚠️ Ошибка подключения к базе данных</h2>
        <p>Произошла ошибка при подключении к SQLite базе данных.</p>
        <p><strong>Детали:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
        <p>Проверьте:</p>
        <ol>
            <li>Существует ли папка <code>data/</code> в корне проекта</li>
            <li>Есть ли права на запись в папку <code>data/</code></li>
            <li>Не занят ли файл базы данных другой программой</li>
        </ol>
    </div>');
}

// Проверяем соединение (опционально)
try {
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    die("База данных недоступна. Ошибка: " . $e->getMessage());
}
?>