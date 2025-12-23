<?php
// Получаем новости из БД
$stmt = $pdo->query("SELECT * FROM news ORDER BY published_at DESC");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-4">Новости</h1>
        
        <div class="row">
            <?php if (empty($news)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Новости пока отсутствуют
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($news as $item): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card bg-dark border-gray-800 h-100">
                        <?php if ($item['image_url']): ?>
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($item['title']) ?></h5>
                            <p class="card-text text-gray-400 flex-grow-1"><?= htmlspecialchars($item['excerpt']) ?></p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-gray-500">
                                        <?= date('d.m.Y', strtotime($item['published_at'])) ?>
                                        <?php if ($item['author']): ?>
                                        <br>Автор: <?= htmlspecialchars($item['author']) ?>
                                        <?php endif; ?>
                                    </small>
                                    <a href="?page=news&id=<?= $item['id'] ?>" class="btn btn-primary btn-sm">Читать</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Пагинация -->
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link bg-dark border-gray-800 text-gray-400" href="#">Назад</a>
                </li>
                <li class="page-item active">
                    <a class="page-link bg-primary border-primary" href="#">1</a>
                </li>
                <li class="page-item">
                    <a class="page-link bg-dark border-gray-800 text-white" href="#">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link bg-dark border-gray-800 text-white" href="#">3</a>
                </li>
                <li class="page-item">
                    <a class="page-link bg-dark border-gray-800 text-gray-400" href="#">Вперед</a>
                </li>
            </ul>
        </nav>
    </div>
</section>