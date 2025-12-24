<?php
requireRole(ROLE_ADMIN);

$db = getDB();

// Допустимые позиции
$validPositions = ['вратарь', 'защитник', 'полузащитник', 'нападающий'];

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Добавление игрока
    if ($action === 'add_player') {
        $position = mb_strtolower(trim($_POST['position']));
        
        if (!in_array($position, $validPositions)) {
            setFlash('error', 'Недопустимая позиция игрока');
            redirect('?page=admin');
        }
        
        $stmt = $db->prepare("
            INSERT INTO players (name, position, number, image, nationality, height, weight, bio)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($_POST['name']),
            $position,
            (int)$_POST['number'],
            $_POST['image'] ?: 'assets/img/players/default.jpg',
            $_POST['nationality'] ?: 'Россия',
            $_POST['height'] ? (int)$_POST['height'] : null,
            $_POST['weight'] ? (int)$_POST['weight'] : null,
            $_POST['bio'] ?: null
        ]);
        logActivity('create', 'player', $db->lastInsertId(), "Добавлен игрок: {$_POST['name']}");
        setFlash('success', 'Игрок успешно добавлен');
        redirect('?page=admin');
    }
    
    // Удаление игрока
    if ($action === 'delete_player' && isset($_POST['player_id'])) {
        $stmt = $db->prepare("SELECT name FROM players WHERE id = ?");
        $stmt->execute([$_POST['player_id']]);
        $player = $stmt->fetch();
        
        $stmt = $db->prepare("DELETE FROM players WHERE id = ?");
        $stmt->execute([$_POST['player_id']]);
        
        logActivity('delete', 'player', $_POST['player_id'], "Удалён игрок: {$player['name']}");
        setFlash('success', 'Игрок удалён');
        redirect('?page=admin');
    }
    
    // Обновление игрока
    if ($action === 'update_player' && isset($_POST['player_id'])) {
        $position = mb_strtolower(trim($_POST['position']));
        
        if (!in_array($position, $validPositions)) {
            setFlash('error', 'Недопустимая позиция игрока');
            redirect('?page=admin&edit=' . $_POST['player_id']);
        }
        
        $stmt = $db->prepare("
            UPDATE players SET 
                name = ?, position = ?, number = ?, image = ?,
                nationality = ?, height = ?, weight = ?, bio = ?,
                matches_played = ?, goals = ?, assists = ?,
                yellow_cards = ?, red_cards = ?, is_active = ?
            WHERE id = ?
        ");
        $stmt->execute([
            trim($_POST['name']),
            $position,
            (int)$_POST['number'],
            $_POST['image'] ?: 'assets/img/players/default.jpg',
            $_POST['nationality'] ?: 'Россия',
            $_POST['height'] ? (int)$_POST['height'] : null,
            $_POST['weight'] ? (int)$_POST['weight'] : null,
            $_POST['bio'] ?: null,
            (int)($_POST['matches_played'] ?? 0),
            (int)($_POST['goals'] ?? 0),
            (int)($_POST['assists'] ?? 0),
            (int)($_POST['yellow_cards'] ?? 0),
            (int)($_POST['red_cards'] ?? 0),
            isset($_POST['is_active']) ? 1 : 0,
            (int)$_POST['player_id']
        ]);
        
        logActivity('update', 'player', $_POST['player_id'], "Обновлён игрок: {$_POST['name']}");
        setFlash('success', 'Данные игрока обновлены');
        redirect('?page=admin');
    }
    
    // Обновление количества товара на складе
    if ($action === 'update_stock' && isset($_POST['product_id'])) {
        $stmt = $db->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([(int)$_POST['stock'], (int)$_POST['product_id']]);
        setFlash('success', 'Количество обновлено');
        redirect('?page=admin');
    }
    
    // Удаление товара
    if ($action === 'delete_product' && isset($_POST['product_id'])) {
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([(int)$_POST['product_id']]);
        logActivity('delete', 'product', $_POST['product_id'], "Удалён товар");
        setFlash('success', 'Товар удалён');
        redirect('?page=admin');
    }
    
    // Добавление товара
    if ($action === 'add_product') {
        $stmt = $db->prepare("
            INSERT INTO products (name, description, price, image, sizes, stock) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($_POST['product_name']),
            trim($_POST['product_description'] ?? ''),
            (float)$_POST['product_price'],
            $_POST['product_image'] ?: 'assets/img/shop/default.jpg',
            $_POST['product_sizes'] ?: 'M',
            (int)$_POST['product_stock']
        ]);
        logActivity('create', 'product', $db->lastInsertId(), "Добавлен товар: {$_POST['product_name']}");
        setFlash('success', 'Товар добавлен');
        redirect('?page=admin');
    }
    
    // Обновление статуса заказа
    if ($action === 'update_order_status' && isset($_POST['order_id'])) {
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], (int)$_POST['order_id']]);
        logActivity('update', 'order', $_POST['order_id'], "Статус заказа изменён на: {$_POST['status']}");
        setFlash('success', 'Статус заказа обновлён');
        redirect('?page=admin');
    }
}

// Получаем данные
$players = $db->query("SELECT * FROM players ORDER BY position, number")->fetchAll();
$stats = [
    'players' => count($players),
    'news' => $db->query("SELECT COUNT(*) FROM news")->fetchColumn(),
    'contacts' => $db->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn(),
    'users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'orders' => $db->query("SELECT COUNT(*) FROM orders WHERE status = 'в пути'")->fetchColumn(),
    'products' => $db->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn()
];

// Редактирование игрока
$editPlayer = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM players WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editPlayer = $stmt->fetch();
}
?>

<section class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-shield-lock me-2"></i>Панель администратора
            </h1>
            <span class="badge bg-danger fs-6">
                <i class="bi bi-person-badge me-1"></i>
                <?= e(getCurrentUser()['full_name']) ?>
            </span>
        </div>
        
        <?php showFlash(); ?>
        
        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="fw-bold"><?= $stats['players'] ?></h4>
                                <p class="mb-0 small">Игроков</p>
                            </div>
                            <i class="bi bi-people fs-2 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="fw-bold"><?= $stats['news'] ?></h4>
                                <p class="mb-0 small">Новостей</p>
                            </div>
                            <i class="bi bi-newspaper fs-2 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="fw-bold"><?= $stats['contacts'] ?></h4>
                                <p class="mb-0 small">Сообщений</p>
                            </div>
                            <i class="bi bi-envelope fs-2 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="fw-bold"><?= $stats['users'] ?></h4>
                                <p class="mb-0 small">Пользователей</p>
                            </div>
                            <i class="bi bi-person-check fs-2 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="fw-bold"><?= $stats['orders'] ?></h4>
                                <p class="mb-0 small">Заказов в пути</p>
                            </div>
                            <i class="bi bi-truck fs-2 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="card bg-secondary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="fw-bold"><?= $stats['products'] ?></h4>
                                <p class="mb-0 small">Товаров</p>
                            </div>
                            <i class="bi bi-shop fs-2 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Форма добавления/редактирования игрока -->
            <div class="col-lg-4 mb-4">
                <div class="card bg-dark border-gray-800">
                    <div class="card-header border-gray-800">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-person-plus me-2"></i>
                            <?= $editPlayer ? 'Редактировать игрока' : 'Добавить игрока' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="<?= $editPlayer ? 'update_player' : 'add_player' ?>">
                            <?php if ($editPlayer): ?>
                            <input type="hidden" name="player_id" value="<?= $editPlayer['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label">ФИО игрока *</label>
                                <input type="text" name="name" class="form-control bg-black border-gray-800 text-white" 
                                       value="<?= e($editPlayer['name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Позиция *</label>
                                    <select name="position" class="form-select bg-black border-gray-800 text-white" required>
                                        <option value="">Выберите...</option>
                                        <option value="вратарь" <?= ($editPlayer['position'] ?? '') === 'вратарь' ? 'selected' : '' ?>>Вратарь</option>
                                        <option value="защитник" <?= ($editPlayer['position'] ?? '') === 'защитник' ? 'selected' : '' ?>>Защитник</option>
                                        <option value="полузащитник" <?= ($editPlayer['position'] ?? '') === 'полузащитник' ? 'selected' : '' ?>>Полузащитник</option>
                                        <option value="нападающий" <?= ($editPlayer['position'] ?? '') === 'нападающий' ? 'selected' : '' ?>>Нападающий</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Номер *</label>
                                    <input type="number" name="number" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= e($editPlayer['number'] ?? '') ?>" min="1" max="99" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Фото (путь)</label>
                                <input type="text" name="image" class="form-control bg-black border-gray-800 text-white" 
                                       value="<?= e($editPlayer['image'] ?? 'assets/img/players/default.jpg') ?>"
                                       placeholder="assets/img/players/player1.jpg">
                            </div>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Национальность</label>
                                    <input type="text" name="nationality" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= e($editPlayer['nationality'] ?? 'Россия') ?>">
                                </div>
                                <div class="col-3 mb-3">
                                    <label class="form-label">Рост</label>
                                    <input type="number" name="height" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= e($editPlayer['height'] ?? '') ?>" placeholder="см">
                                </div>
                                <div class="col-3 mb-3">
                                    <label class="form-label">Вес</label>
                                    <input type="number" name="weight" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= e($editPlayer['weight'] ?? '') ?>" placeholder="кг">
                                </div>
                            </div>
                            
                            <?php if ($editPlayer): ?>
                            <div class="row">
                                <div class="col-4 mb-3">
                                    <label class="form-label">Матчи</label>
                                    <input type="number" name="matches_played" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= $editPlayer['matches_played'] ?? 0 ?>" min="0">
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="form-label">Голы</label>
                                    <input type="number" name="goals" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= $editPlayer['goals'] ?? 0 ?>" min="0">
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="form-label">Передачи</label>
                                    <input type="number" name="assists" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= $editPlayer['assists'] ?? 0 ?>" min="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">ЖК</label>
                                    <input type="number" name="yellow_cards" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= $editPlayer['yellow_cards'] ?? 0 ?>" min="0">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">КК</label>
                                    <input type="number" name="red_cards" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= $editPlayer['red_cards'] ?? 0 ?>" min="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                           <?= ($editPlayer['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">Активен в составе</label>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Биография</label>
                                <textarea name="bio" class="form-control bg-black border-gray-800 text-white" rows="3"><?= e($editPlayer['bio'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>
                                    <?= $editPlayer ? 'Сохранить изменения' : 'Добавить игрока' ?>
                                </button>
                                <?php if ($editPlayer): ?>
                                <a href="?page=admin" class="btn btn-outline-secondary">Отмена</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Список игроков -->
            <div class="col-lg-8">
                <div class="card bg-dark border-gray-800">
                    <div class="card-header border-gray-800 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-people me-2"></i>Состав команды
                        </h5>
                        <span class="badge bg-primary"><?= count($players) ?> игроков</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Фото</th>
                                        <th>Имя</th>
                                        <th>Позиция</th>
                                        <th>Статистика</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($players)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-gray-400">
                                            Нет игроков. Добавьте первого игрока через форму слева.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($players as $player): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?= $player['number'] ?></span></td>
                                        <td>
                                            <img src="<?= e($player['image']) ?>" alt="" 
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;"
                                                 onerror="this.src='assets/img/players/default.jpg'">
                                        </td>
                                        <td>
                                            <strong><?= e($player['name']) ?></strong>
                                            <?php if (($player['nationality'] ?? 'Россия') !== 'Россия'): ?>
                                            <br><small class="text-gray-400"><?= e($player['nationality']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark"><?= e($player['position']) ?></span>
                                        </td>
                                        <td>
                                            <small>
                                                <span title="Матчи"><?= $player['matches_played'] ?? 0 ?> М</span> |
                                                <span title="Голы" class="text-success"><?= $player['goals'] ?? 0 ?> Г</span> |
                                                <span title="Передачи" class="text-info"><?= $player['assists'] ?? 0 ?> П</span>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($player['is_active'] ?? 1): ?>
                                            <span class="badge bg-success">Активен</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Неактивен</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?page=admin&edit=<?= $player['id'] ?>" 
                                                   class="btn btn-outline-primary" title="Редактировать">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Удалить игрока <?= e($player['name']) ?>?')">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="delete_player">
                                                    <input type="hidden" name="player_id" value="<?= $player['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger" title="Удалить">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Управление товарами -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-dark border-gray-800">
                    <div class="card-header border-gray-800 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-shop me-2"></i>Управление товарами
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="bi bi-plus-lg me-1"></i>Добавить товар
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Фото</th>
                                        <th>Название</th>
                                        <th>Цена</th>
                                        <th>Размеры</th>
                                        <th>На складе</th>
                                        <th>Продано</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $productsAdmin = $db->query("SELECT * FROM products ORDER BY id")->fetchAll();
                                    if (empty($productsAdmin)):
                                    ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-gray-400">
                                            Нет товаров. Добавьте первый товар.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($productsAdmin as $prod): ?>
                                    <tr>
                                        <td><?= $prod['id'] ?></td>
                                        <td>
                                            <img src="<?= e($prod['image']) ?>" alt="" 
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;"
                                                 onerror="this.src='assets/img/shop/default.jpg'">
                                        </td>
                                        <td><?= e($prod['name']) ?></td>
                                        <td><?= number_format($prod['price'], 0, '', ' ') ?> ₽</td>
                                        <td><span class="badge bg-secondary"><?= e($prod['sizes']) ?></span></td>
                                        <td>
                                            <form method="POST" class="d-flex gap-1" style="width: 120px;">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="update_stock">
                                                <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                                <input type="number" name="stock" value="<?= $prod['stock'] ?>" 
                                                       class="form-control form-control-sm bg-black border-gray-800 text-white"
                                                       style="width: 70px;" min="0">
                                                <button type="submit" class="btn btn-outline-success btn-sm">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td><span class="badge bg-info"><?= $prod['total_sold'] ?></span></td>
                                        <td>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Удалить товар?')">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Заказы клиентов -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-dark border-gray-800">
                    <div class="card-header border-gray-800">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-bag-check me-2"></i>Заказы клиентов
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>№</th>
                                        <th>Клиент</th>
                                        <th>Товары</th>
                                        <th>Сумма</th>
                                        <th>Адрес</th>
                                        <th>Дата</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $ordersAdmin = $db->query("
                                        SELECT o.*, u.full_name, u.email 
                                        FROM orders o 
                                        JOIN users u ON o.user_id = u.id 
                                        ORDER BY o.created_at DESC
                                    ")->fetchAll();
                                    
                                    if (empty($ordersAdmin)): 
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-gray-400">Заказов пока нет</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($ordersAdmin as $ord): ?>
                                        <?php
                                        $orderItems = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                                        $orderItems->execute([$ord['id']]);
                                        $items = $orderItems->fetchAll();
                                        ?>
                                    <tr>
                                        <td><strong>#<?= $ord['id'] ?></strong></td>
                                        <td>
                                            <?= e($ord['full_name']) ?><br>
                                            <small class="text-gray-400"><?= e($ord['email']) ?></small>
                                        </td>
                                        <td>
                                            <?php foreach ($items as $item): ?>
                                                <small><?= e($item['product_name']) ?> (<?= $item['size'] ?>) × <?= $item['quantity'] ?></small><br>
                                            <?php endforeach; ?>
                                        </td>
                                        <td><strong><?= number_format($ord['total_amount'], 0, '', ' ') ?> ₽</strong></td>
                                        <td style="max-width: 200px;"><small><?= e($ord['delivery_address']) ?></small></td>
                                        <td><small><?= date('d.m.Y H:i', strtotime($ord['created_at'])) ?></small></td>
                                        <td>
                                            <form method="POST">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="update_order_status">
                                                <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                <select name="status" class="form-select form-select-sm bg-black border-gray-800 text-white"
                                                        onchange="this.form.submit()" style="width: 140px;">
                                                    <option value="в пути" <?= $ord['status'] === 'в пути' ? 'selected' : '' ?>>🚚 В пути</option>
                                                    <option value="доставлено" <?= $ord['status'] === 'доставлено' ? 'selected' : '' ?>>✅ Доставлено</option>
                                                    <option value="отменён" <?= $ord['status'] === 'отменён' ? 'selected' : '' ?>>❌ Отменён</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

<!-- Модальное окно добавления товара -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-gray-800">
                <h5 class="modal-title">Добавить товар</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="add_product">
                    
                    <div class="mb-3">
                        <label class="form-label">Название *</label>
                        <input type="text" name="product_name" class="form-control bg-black border-gray-800 text-white" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="product_description" class="form-control bg-black border-gray-800 text-white" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Цена (₽) *</label>
                            <input type="number" name="product_price" class="form-control bg-black border-gray-800 text-white" 
                                   min="1" step="0.01" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">На складе *</label>
                            <input type="number" name="product_stock" class="form-control bg-black border-gray-800 text-white" 
                                   value="100" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Размеры (через запятую)</label>
                        <input type="text" name="product_sizes" class="form-control bg-black border-gray-800 text-white" 
                               value="S,M,L" placeholder="S,M,L">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Изображение (путь)</label>
                        <input type="text" name="product_image" class="form-control bg-black border-gray-800 text-white" 
                               value="assets/img/shop/default.jpg">
                    </div>
                </div>
                <div class="modal-footer border-gray-800">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>