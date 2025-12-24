<?php
/**
 * Подключение к базе данных
 * Этот файл подключается на страницах, где нужен доступ к БД
 */

// Подключаем конфигурацию (если ещё не подключена)
if (!function_exists('getDB')) {
    require_once __DIR__ . '/../config/database.php';
}

// Создаем глобальное подключение для совместимости
try {
    $pdo = getDB();
} catch (Exception $e) {
    die('<div style="background: #333; color: white; padding: 20px; border-radius: 10px; margin: 20px;">
        <h2 style="color: #ff6b6b;">⚠️ Ошибка подключения к базе данных</h2>
        <p>Произошла ошибка при подключении к MySQL базе данных.</p>
        <p><strong>Детали:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
        <p>Проверьте:</p>
        <ol>
            <li>Запущены ли Docker контейнеры: <code>docker-compose ps</code></li>
            <li>Доступен ли MySQL контейнер</li>
        </ol>
    </div>');
}