<?php
if (isLoggedIn()) {
    redirect('?page=home');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    
    // Валидация
    if (strlen($username) < 3) {
        $errors[] = 'Логин должен содержать минимум 3 символа';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать минимум 6 символов';
    }
    if ($password !== $passwordConfirm) {
        $errors[] = 'Пароли не совпадают';
    }
    
    if (empty($errors)) {
        require_once __DIR__ . '/../includes/auth.php';
        $result = register($username, $email, $password, $fullName);
        
        if ($result['success']) {
            setFlash('success', 'Регистрация успешна! Теперь вы можете войти.');
            redirect('?page=login');
        } else {
            $errors[] = $result['error'];
        }
    }
}
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark border-gray-800">
                    <div class="card-header border-gray-800 text-center">
                        <h3 class="fw-bold mb-0">
                            <i class="bi bi-person-plus me-2"></i>Регистрация
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <?= csrfField() ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Логин *</label>
                                    <input type="text" 
                                           class="form-control bg-black border-gray-800 text-white" 
                                           id="username" 
                                           name="username"
                                           value="<?= e($_POST['username'] ?? '') ?>"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Полное имя</label>
                                    <input type="text" 
                                           class="form-control bg-black border-gray-800 text-white" 
                                           id="full_name" 
                                           name="full_name"
                                           value="<?= e($_POST['full_name'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" 
                                       class="form-control bg-black border-gray-800 text-white" 
                                       id="email" 
                                       name="email"
                                       value="<?= e($_POST['email'] ?? '') ?>"
                                       required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Пароль *</label>
                                    <input type="password" 
                                           class="form-control bg-black border-gray-800 text-white" 
                                           id="password" 
                                           name="password"
                                           required>
                                    <div class="form-text">Минимум 6 символов</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirm" class="form-label">Повторите пароль *</label>
                                    <input type="password" 
                                           class="form-control bg-black border-gray-800 text-white" 
                                           id="password_confirm" 
                                           name="password_confirm"
                                           required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-person-plus me-2"></i>Зарегистрироваться
                            </button>
                        </form>
                        
                        <hr class="my-4 border-gray-800">
                        
                        <div class="text-center">
                            <p class="text-gray-400 mb-2">Уже есть аккаунт?</p>
                            <a href="?page=login" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Войти
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>