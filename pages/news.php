<?php
$db = getDB();
$news = $db->query("
    SELECT n.*, u.full_name as author_name 
    FROM news n 
    LEFT JOIN users u ON n.author_id = u.id 
    WHERE n.is_published = 1 
    ORDER BY n.published_at DESC
")->fetchAll();
?>

<section class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-5">Новости клуба</h1>
        
        <div class="row">
            <?php if (empty($news)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Новостей пока нет
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($news as $item): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-dark border-gray-800 h-100">
                        <?php if ($item['image_url']): ?>
                        <img src="<?= e($item['image_url']) ?>" class="card-img-top" 
                             alt="<?= e($item['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?= e($item['title']) ?></h5>
                            <p class="card-text text-gray-400 flex-grow-1"><?= e($item['excerpt']) ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-gray-500">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= date('d.m.Y', strtotime($item['published_at'])) ?>
                                </small>
                                <a href="?page=news&id=<?= $item['id'] ?>" class="btn btn-sm btn-primary">
                                    Читать <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-footer border-gray-800 small text-gray-500">
                            <i class="bi bi-person me-1"></i><?= e($item['author_name'] ?? 'Редакция') ?>
                            <?php if ($item['views'] > 0): ?>
                            <span class="float-end">
                                <i class="bi bi-eye me-1"></i><?= $item['views'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>