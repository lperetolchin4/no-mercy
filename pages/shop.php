<?php
$db = getDB();

// Получаем товары
$products = $db->query("SELECT * FROM products WHERE is_active = 1 ORDER BY name")->fetchAll();

// Определяем хит продаж (товар с максимальным total_sold)
$hitProduct = $db->query("SELECT id FROM products WHERE is_active = 1 ORDER BY total_sold DESC LIMIT 1")->fetch();
$hitProductId = $hitProduct['id'] ?? 0;

// Обработка добавления в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    if (!isLoggedIn()) {
        setFlash('error', 'Войдите в аккаунт, чтобы добавить товар в корзину');
        redirect('?page=login');
    }
    
    if (hasRole(ROLE_ADMIN) || hasRole(ROLE_MODERATOR)) {
        setFlash('error', 'Администраторы и модераторы не могут делать заказы');
        redirect('?page=shop');
    }
    
    $productId = (int)$_POST['product_id'];
    $size = $_POST['size'] ?? 'M';
    $quantity = (int)($_POST['quantity'] ?? 1);
    $userId = getCurrentUser()['id'];
    
    // Проверяем наличие товара
    $stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product || $product['stock'] < $quantity) {
        setFlash('error', 'Недостаточно товара на складе');
        redirect('?page=shop');
    }
    
    // Добавляем или обновляем корзину
    $stmt = $db->prepare("
        INSERT INTO cart (user_id, product_id, size, quantity) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + ?
    ");
    $stmt->execute([$userId, $productId, $size, $quantity, $quantity]);
    
    setFlash('success', 'Товар добавлен в корзину!');
    redirect('?page=shop');
}
?>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-shop me-2"></i>Магазин
            </h1>
            <?php if (isLoggedIn() && !hasRole(ROLE_ADMIN) && !hasRole(ROLE_MODERATOR)): ?>
                <?php
                $cartCount = $db->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
                $cartCount->execute([getCurrentUser()['id']]);
                $count = $cartCount->fetch()['count'] ?? 0;
                ?>
                <a href="?page=cart" class="btn btn-outline-primary position-relative">
                    <i class="bi bi-cart3 me-1"></i>Корзина
                    <?php if ($count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $count ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
        
        <?php showFlash(); ?>
        
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>Товары скоро появятся!
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <?php $sizes = explode(',', $product['sizes']); ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card bg-dark border-gray-800 h-100">
                            <?php if ($product['id'] == $hitProductId && $product['total_sold'] > 0): ?>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger fs-6">
                                        <i class="bi bi-fire me-1"></i>Хит продаж
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <img src="<?= e($product['image']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= e($product['name']) ?>"
                                 style="height: 250px; object-fit: cover;"
                                 onerror="this.src='assets/img/shop/default.jpg'">
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold"><?= e($product['name']) ?></h5>
                                <p class="card-text text-gray-400 small"><?= e($product['description']) ?></p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fs-4 fw-bold text-primary"><?= number_format($product['price'], 0, '', ' ') ?> ₽</span>
                                        <span class="badge <?= $product['stock'] > 10 ? 'bg-success' : ($product['stock'] > 0 ? 'bg-warning' : 'bg-danger') ?>">
                                            <?php if ($product['stock'] > 0): ?>
                                                В наличии: <?= $product['stock'] ?> шт.
                                            <?php else: ?>
                                                Нет в наличии
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($product['stock'] > 0): ?>
                                        <form method="POST" class="d-flex gap-2">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="add_to_cart">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            
                                            <select name="size" class="form-select form-select-sm bg-black border-gray-800 text-white" style="width: 80px;">
                                                <?php foreach ($sizes as $size): ?>
                                                    <option value="<?= trim($size) ?>"><?= trim($size) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            
                                            <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" 
                                                   class="form-control form-control-sm bg-black border-gray-800 text-white" style="width: 70px;">
                                            
                                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                                <i class="bi bi-cart-plus me-1"></i>В корзину
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100" disabled>Нет в наличии</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>