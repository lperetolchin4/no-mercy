<?php
if (!isLoggedIn()) {
    setFlash('error', 'Войдите в аккаунт для просмотра корзины');
    redirect('?page=login');
}

if (hasRole(ROLE_ADMIN) || hasRole(ROLE_MODERATOR)) {
    setFlash('error', 'Администраторы и модераторы не могут делать заказы');
    redirect('?page=shop');
}

$db = getDB();
$userId = getCurrentUser()['id'];

// Удаление из корзины
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'remove_item') {
        $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$_POST['cart_id'], $userId]);
        setFlash('success', 'Товар удалён из корзины');
        redirect('?page=cart');
    }
    
    if ($_POST['action'] === 'update_quantity') {
        $quantity = max(1, (int)$_POST['quantity']);
        $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$quantity, $_POST['cart_id'], $userId]);
        redirect('?page=cart');
    }
    
    if ($_POST['action'] === 'clear_cart') {
        $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        setFlash('success', 'Корзина очищена');
        redirect('?page=cart');
    }
    
    // Оформление заказа
    if ($_POST['action'] === 'checkout') {
        $address = trim($_POST['delivery_address'] ?? '');
        
        if (empty($address)) {
            setFlash('error', 'Укажите адрес доставки');
            redirect('?page=cart');
        }
        
        // Получаем товары из корзины
        $stmt = $db->prepare("
            SELECT c.*, p.name, p.price, p.stock 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$userId]);
        $cartItems = $stmt->fetchAll();
        
        if (empty($cartItems)) {
            setFlash('error', 'Корзина пуста');
            redirect('?page=cart');
        }
        
        // Проверяем наличие всех товаров
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            if ($item['stock'] < $item['quantity']) {
                setFlash('error', "Недостаточно товара '{$item['name']}' на складе");
                redirect('?page=cart');
            }
            $totalAmount += $item['price'] * $item['quantity'];
        }
        
        try {
            $db->beginTransaction();
            
            // Создаём заказ
            $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, delivery_address) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $totalAmount, $address]);
            $orderId = $db->lastInsertId();
            
            // Добавляем товары в заказ и обновляем склад
            foreach ($cartItems as $item) {
                $stmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, size, quantity, price) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$orderId, $item['product_id'], $item['name'], $item['size'], $item['quantity'], $item['price']]);
                
                // Уменьшаем количество на складе и увеличиваем счётчик продаж
                $stmt = $db->prepare("UPDATE products SET stock = stock - ?, total_sold = total_sold + ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['quantity'], $item['product_id']]);
            }
            
            // Очищаем корзину
            $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            $db->commit();
            
            logActivity('create', 'order', $orderId, "Оформлен заказ на сумму {$totalAmount} руб.");
            setFlash('success', "Заказ №{$orderId} успешно оформлен! Статус: В пути");
            redirect('?page=orders');
            
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('error', 'Ошибка оформления заказа');
            redirect('?page=cart');
        }
    }
}

// Получаем товары в корзине
$stmt = $db->prepare("
    SELECT c.*, p.name, p.price, p.image, p.stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}
?>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-cart3 me-2"></i>Корзина
            </h1>
            <a href="?page=shop" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Продолжить покупки
            </a>
        </div>
        
        <?php showFlash(); ?>
        
        <?php if (empty($cartItems)): ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-gray-600 mb-3"></i>
                <h3>Корзина пуста</h3>
                <p class="text-gray-400">Добавьте товары из магазина</p>
                <a href="?page=shop" class="btn btn-primary">
                    <i class="bi bi-shop me-1"></i>Перейти в магазин
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Товары в корзине -->
                <div class="col-lg-8 mb-4">
                    <div class="card bg-dark border-gray-800">
                        <div class="card-header border-gray-800 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Товары (<?= count($cartItems) ?>)</h5>
                            <form method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="clear_cart">
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Очистить корзину?')">
                                    <i class="bi bi-trash me-1"></i>Очистить
                                </button>
                            </form>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex p-3 border-bottom border-gray-800">
                                    <img src="<?= e($item['image']) ?>" 
                                         alt="<?= e($item['name']) ?>"
                                         class="rounded me-3"
                                         style="width: 80px; height: 80px; object-fit: cover;"
                                         onerror="this.src='assets/img/shop/default.jpg'">
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= e($item['name']) ?></h6>
                                        <p class="text-gray-400 small mb-2">
                                            Размер: <span class="badge bg-secondary"><?= e($item['size']) ?></span>
                                        </p>
                                        
                                        <div class="d-flex align-items-center gap-3">
                                            <form method="POST" class="d-flex align-items-center gap-2">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="update_quantity">
                                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                                       min="1" max="<?= $item['stock'] ?>"
                                                       class="form-control form-control-sm bg-black border-gray-800 text-white"
                                                       style="width: 70px;"
                                                       onchange="this.form.submit()">
                                                <span class="text-gray-400 small">× <?= number_format($item['price'], 0, '', ' ') ?> ₽</span>
                                            </form>
                                            
                                            <form method="POST" class="d-inline">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="remove_item">
                                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <span class="fw-bold fs-5"><?= number_format($item['price'] * $item['quantity'], 0, '', ' ') ?> ₽</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Оформление заказа -->
                <div class="col-lg-4">
                    <div class="card bg-dark border-gray-800 sticky-top" style="top: 100px;">
                        <div class="card-header border-gray-800">
                            <h5 class="mb-0">Оформление заказа</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-gray-400">Товары (<?= array_sum(array_column($cartItems, 'quantity')) ?> шт.)</span>
                                <span><?= number_format($totalAmount, 0, '', ' ') ?> ₽</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-gray-400">Доставка</span>
                                <span class="text-success">Бесплатно</span>
                            </div>
                            <hr class="border-gray-800">
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fs-5 fw-bold">Итого</span>
                                <span class="fs-5 fw-bold text-primary"><?= number_format($totalAmount, 0, '', ' ') ?> ₽</span>
                            </div>
                            
                            <form method="POST">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="checkout">
                                
                                <div class="mb-3">
                                    <label class="form-label">Адрес доставки *</label>
                                    <textarea name="delivery_address" 
                                              class="form-control bg-black border-gray-800 text-white" 
                                              rows="3" 
                                              placeholder="Город, улица, дом, квартира..."
                                              required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 btn-lg">
                                    <i class="bi bi-check-lg me-1"></i>Оформить заказ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>