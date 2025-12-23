<?php
/**
 * Основной конфигурационный файл проекта
 */

// Настройки безопасности
define('APP_ENV', 'development'); // 'development' или 'production'

// Настройки сессии
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Поставьте 1 если используете HTTPS

// Настройки отображения ошибок
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Настройки времени
date_default_timezone_set('Europe/Moscow');

// Настройки загрузки файлов
ini_set('upload_max_filesize', '5M');
ini_set('post_max_size', '6M');
ini_set('max_execution_time', 30);

// Кодировка
header('Content-Type: text/html; charset=utf-8');

// Заголовки безопасности
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Подключаем файл с настройками базы данных
require_once __DIR__ . '/database.php';

/**
 * Функция для экранирования вывода
 * @param string $data Данные для экранирования
 * @return string Экранированные данные
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Функция для перенаправления
 * @param string $url URL для перенаправления
 * @param int $statusCode HTTP статус код
 */
function redirect($url, $statusCode = 303) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Проверка AJAX запроса
 * @return bool
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Генерация CSRF токена
 * @return string
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Проверка CSRF токена
 * @param string $token Токен для проверки
 * @return bool
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Логирование ошибок
 * @param Exception $exception Исключение для логирования
 */
function logError($exception) {
    $logMessage = sprintf(
        "[%s] %s: %s in %s:%d\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );
    
    error_log($logMessage, 3, __DIR__ . '/../logs/error.log');
}

/**
 * Проверка авторизации (заглушка для демо)
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Получение текущего URL
 * @return string
 */
function currentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
?>