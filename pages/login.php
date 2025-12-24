<?php
// Если уже авторизован - редирект
if (isLoggedIn()) {
    redirect('?page=home');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        require_once __DIR__ . '/../includes/auth.php';
        $result = login($username, $password);
        
        if ($result['success']) {
            $redirectUrl = $_SESSION['redirect_after_login'] ?? '?page=home';
            unset($_SESSION['redirect_after_login']);
            
            // Редирект в зависимости от роли
            $user = $result['user'];
            if ($user['role'] === 'admin') {
                redirect('?page=admin');
            } elseif ($user['role'] === 'moderator') {
                redirect('?page=moderator');
            } else {
                redirect($redirectUrl);
            }
        } else {
            $error = $result['error'];
        }
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card bg-dark border-gray-800">
                    <div class="card-header border-gray-800 text-center">
                        <h3 class="fw-bold mb-0">
                            <i class="bi bi-person-circle me-2"></i>Вход в систему
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <?= csrfField() ?>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Логин или Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-black border-gray-800">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control bg-black border-gray-800 text-white" 
                                           id="username" 
                                           name="username" 
                                           value="<?= e($_POST['username'] ?? '') ?>"
                                           placeholder="Введите логин или email"
                                           required 
                                           autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Пароль</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-black border-gray-800">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control bg-black border-gray-800 text-white" 
                                           id="password" 
                                           name="password"
                                           placeholder="Введите пароль"
                                           required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                            </button>
                        </form>
                        
                        <hr class="my-4 border-gray-800">
                        
                        <div class="text-center">
                            <p class="text-gray-400 mb-2">Нет аккаунта?</p>
                            <a href="?page=register" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-person-plus me-1"></i>Зарегистрироваться
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Тестовые аккаунты для защиты -->
                <div class="card bg-dark border-gray-800 mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-info-circle me-2"></i>Тестовые аккаунты:
                        </h6>
                        <div class="small">
                            <p class="mb-1"><strong>Админ:</strong> admin / password</p>
                            <p class="mb-1"><strong>Модератор:</strong> moderator / password</p>
                            <p class="mb-0"><strong>Пользователь:</strong> user / password</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>