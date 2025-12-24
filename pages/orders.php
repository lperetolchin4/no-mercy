<?php
if (!isLoggedIn()) {
    setFlash('error', 'Войдите в аккаунт');
    redirect('?page=login');
}

$db = getDB();
$userId = getCurrentUser()['id'];

// Получаем заказы пользователя
$stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-bag-check me-2"></i>Мои заказы
            </h1>
            <a href="?page=shop" class="btn btn-outline-light">
                <i class="bi bi-shop me-1"></i>В магазин
            </a>
        </div>
        
        <?php showFlash(); ?>
        
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bag-x display-1 text-gray-600 mb-3"></i>
                <h3>У вас пока нет заказов</h3>
                <p class="text-gray-400">Оформите первый заказ в нашем магазине</p>
                <a href="?page=shop" class="btn btn-primary">
                    <i class="bi bi-shop me-1"></i>Перейти в магазин
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <?php
                    $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                    $stmt->execute([$order['id']]);
                    $items = $stmt->fetchAll();
                    ?>
                    <div class="col-12 mb-4">
                        <div class="card bg-dark border-gray-800">
                            <div class="card-header border-gray-800 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Заказ №<?= $order['id'] ?></h5>
                                    <small class="text-gray-400"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></small>
                                </div>
                                <span class="badge fs-6 <?= $order['status'] === 'доставлено' ? 'bg-success' : ($order['status'] === 'отменён' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                    <?php if ($order['status'] === 'в пути'): ?>
                                        <i class="bi bi-truck me-1"></i>
                                    <?php elseif ($order['status'] === 'доставлено'): ?>
                                        <i class="bi bi-check-circle me-1"></i>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle me-1"></i>
                                    <?php endif; ?>
                                    <?= mb_ucfirst($order['status']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="text-gray-400 mb-2">Товары:</h6>
                                        <?php foreach ($items as $item): ?>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>
                                                    <?= e($item['product_name']) ?> 
                                                    <span class="badge bg-secondary"><?= e($item['size']) ?></span>
                                                    × <?= $item['quantity'] ?>
                                                </span>
                                                <span><?= number_format($item['price'] * $item['quantity'], 0, '', ' ') ?> ₽</span>
                                            </div>
                                        <?php endforeach; ?>
                                        <hr class="border-gray-800">
                                        <div class="d-flex justify-content-between">
                                            <strong>Итого:</strong>
                                            <strong class="text-primary"><?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-gray-400 mb-2">Адрес доставки:</h6>
                                        <p class="mb-0"><?= e($order['delivery_address']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Вспомогательная функция
function mb_ucfirst($str) {
    return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
}
?>