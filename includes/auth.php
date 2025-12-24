<?php
/**
 * Функции авторизации
 */

/**
 * Авторизация пользователя
 */
function login($username, $password) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'error' => 'Неверный логин или пароль'];
    }
    
    // Устанавливаем сессию
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['full_name'] ?? $user['username'];
    
    // Логируем вход
    logActivity('login', 'user', $user['id']);
    
    return ['success' => true, 'user' => $user];
}

/**
 * Выход из системы
 */
function logout() {
    logActivity('logout', 'user', $_SESSION['user_id'] ?? null);
    
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Регистрация пользователя
 */
function register($username, $email, $password, $fullName = '') {
    $db = getDB();
    
    // Проверяем существование
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Пользователь с таким логином или email уже существует'];
    }
    
    // Создаём пользователя
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO users (username, email, password, full_name, role) 
        VALUES (?, ?, ?, ?, 'user')
    ");
    
    try {
        $stmt->execute([$username, $email, $hashedPassword, $fullName]);
        return ['success' => true, 'user_id' => $db->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Ошибка при создании пользователя'];
    }
}

/**
 * Смена пароля
 */
function changePassword($userId, $oldPassword, $newPassword) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($oldPassword, $user['password'])) {
        return ['success' => false, 'error' => 'Неверный текущий пароль'];
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $userId]);
    
    logActivity('password_change', 'user', $userId);
    
    return ['success' => true];
}
?>