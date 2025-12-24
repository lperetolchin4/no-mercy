<?php
/**
 * Основной конфигурационный файл проекта
 */

// Настройки окружения
define('APP_ENV', 'development'); // 'development' или 'production'
define('APP_NAME', 'No MERCY FC');
define('APP_URL', 'http://localhost:8080');

// Настройки отображения ошибок (ДО любого вывода)
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Настройки времени
date_default_timezone_set('Europe/Moscow');

// Настройки сессии (ТОЛЬКО если сессия ещё не запущена)
if (session_status() === PHP_SESSION_NONE) {
    // Настройки безопасности сессии
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);
    
    // Запуск сессии
    session_start();
}

// Подключаем базу данных
require_once __DIR__ . '/database.php';

/**
 * Роли пользователей
 */
define('ROLE_ADMIN', 'admin');
define('ROLE_MODERATOR', 'moderator');
define('ROLE_USER', 'user');

/**
 * Экранирование вывода
 */
function e($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Перенаправление
 */
function redirect($url, $statusCode = 303) {
    if (!headers_sent()) {
        header('Location: ' . $url, true, $statusCode);
        exit;
    } else {
        echo '<script>window.location.href="' . $url . '";</script>';
        exit;
    }
}

/**
 * Проверка авторизации
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Получение текущего пользователя
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    static $user = null;
    
    if ($user === null) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
    
    return $user;
}

/**
 * Проверка роли
 */
function hasRole($role) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    if (is_array($role)) {
        return in_array($user['role'], $role);
    }
    
    return $user['role'] === $role;
}

/**
 * Проверка: является ли админом
 */
function isAdmin() {
    return hasRole(ROLE_ADMIN);
}

/**
 * Проверка: является ли модератором
 */
function isModerator() {
    return hasRole([ROLE_ADMIN, ROLE_MODERATOR]);
}

/**
 * Требование авторизации
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('?page=login');
    }
}

/**
 * Требование роли
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        http_response_code(403);
        die('
            <div style="font-family: Arial; max-width: 500px; margin: 100px auto; text-align: center; padding: 40px; background: #1a1a1a; color: #fff; border-radius: 10px;">
                <h1 style="color: #ff6b6b;">403</h1>
                <h2>Доступ запрещён</h2>
                <p>У вас нет прав для просмотра этой страницы.</p>
                <a href="?page=home" style="color: #4dabf7;">← Вернуться на главную</a>
            </div>
        ');
    }
}

/**
 * CSRF защита
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
}

/**
 * Flash-сообщения
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function showFlash() {
    $flash = getFlash();
    if ($flash) {
        $type = $flash['type'] === 'error' ? 'danger' : $flash['type'];
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                ' . e($flash['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
    }
}

/**
 * Логирование действий
 */
function logActivity($action, $entityType = null, $entityId = null, $details = null) {
    if (!isLoggedIn()) return;
    
    try {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, ip_address)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $action,
            $entityType,
            $entityId,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch (Exception $e) {
        // Молча игнорируем ошибки логирования
    }
}