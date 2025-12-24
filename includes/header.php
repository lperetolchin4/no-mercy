<?php
$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FC No MERCY - <?= ucfirst($page) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-black text-white">

<!-- Навигация -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="?page=home">
            <img src="assets/img/logo.png" alt="Logo" height="40" class="me-2" onerror="this.style.display='none'">
            <span class="text-white">No</span> MERCY
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'home' ? 'active' : '' ?>" href="?page=home">
                        <i class="bi bi-house me-1"></i>Главная
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'team' ? 'active' : '' ?>" href="?page=team">
                        <i class="bi bi-people me-1"></i>Команда
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'news' ? 'active' : '' ?>" href="?page=news">
                        <i class="bi bi-newspaper me-1"></i>Новости
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'shop' ? 'active' : '' ?>" href="?page=shop">
                        <i class="bi bi-shop me-1"></i>Магазин
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'about' ? 'active' : '' ?>" href="?page=about">
                        <i class="bi bi-info-circle me-1"></i>О клубе
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'contact' ? 'active' : '' ?>" href="?page=contact">
                        <i class="bi bi-envelope me-1"></i>Контакты
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <?php $user = getCurrentUser(); ?>
                    
                    <?php if (hasRole(ROLE_USER) && !hasRole(ROLE_ADMIN) && !hasRole(ROLE_MODERATOR)): ?>
                        <?php
                        $db = getDB();
                        $cartCount = $db->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
                        $cartCount->execute([$user['id']]);
                        $count = $cartCount->fetch()['count'] ?? 0;
                        ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'cart' ? 'active' : '' ?> position-relative" href="?page=cart">
                                <i class="bi bi-cart3 me-1"></i>Корзина
                                <?php if ($count > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                        <?= $count ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'orders' ? 'active' : '' ?>" href="?page=orders">
                                <i class="bi bi-bag-check me-1"></i>Заказы
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_ADMIN)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'admin' ? 'active' : '' ?>" href="?page=admin">
                                <i class="bi bi-shield-lock me-1"></i>Админ
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_MODERATOR)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'moderator' ? 'active' : '' ?>" href="?page=moderator">
                                <i class="bi bi-pencil-square me-1"></i>Модератор
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= e($user['full_name'] ?: $user['username']) ?>
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'moderator' ? 'warning' : 'secondary') ?> ms-1">
                                <?= $user['role'] ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="?page=profile">
                                    <i class="bi bi-person me-2"></i>Профиль
                                </a>
                            </li>
                            <?php if (hasRole(ROLE_USER) && !hasRole(ROLE_ADMIN) && !hasRole(ROLE_MODERATOR)): ?>
                            <li>
                                <a class="dropdown-item" href="?page=orders">
                                    <i class="bi bi-bag-check me-2"></i>Мои заказы
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="?page=logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Выйти
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $page === 'login' ? 'active' : '' ?>" href="?page=login">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Войти
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $page === 'register' ? 'active' : '' ?>" href="?page=register">
                            <i class="bi bi-person-plus me-1"></i>Регистрация
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>