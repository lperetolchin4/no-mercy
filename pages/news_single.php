<?php
$db = getDB();
$newsId = (int)($_GET['id'] ?? 0);

if (!$newsId) {
    redirect('?page=news');
}

$stmt = $db->prepare("
    SELECT n.*, u.full_name as author_name 
    FROM news n 
    LEFT JOIN users u ON n.author_id = u.id 
    WHERE n.id = ? AND n.is_published = 1
");
$stmt->execute([$newsId]);
$news = $stmt->fetch();

if (!$news) {
    redirect('?page=news');
}

// Увеличиваем счётчик просмотров
$db->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$newsId]);

// Получаем другие новости
$otherNews = $db->prepare("
    SELECT id, title, image_url, published_at 
    FROM news 
    WHERE id != ? AND is_published = 1 
    ORDER BY published_at DESC 
    LIMIT 3
");
$otherNews->execute([$newsId]);
$otherNews = $otherNews->fetchAll();
?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Хлебные крошки -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="?page=home" class="text-gray-400">Главная</a></li>
                        <li class="breadcrumb-item"><a href="?page=news" class="text-gray-400">Новости</a></li>
                        <li class="breadcrumb-item active text-white"><?= e(mb_substr($news['title'], 0, 30)) ?>...</li>
                    </ol>
                </nav>
                
                <!-- Заголовок -->
                <h1 class="fw-bold mb-3"><?= e($news['title']) ?></h1>
                
                <!-- Мета-информация -->
                <div class="d-flex flex-wrap gap-3 mb-4 text-gray-400">
                    <span><i class="bi bi-calendar me-1"></i><?= date('d.m.Y', strtotime($news['published_at'])) ?></span>
                    <span><i class="bi bi-person me-1"></i><?= e($news['author_name'] ?? 'Редакция') ?></span>
                    <span><i class="bi bi-eye me-1"></i><?= $news['views'] + 1 ?> просмотров</span>
                </div>
                
                <!-- Изображение -->
                <?php if ($news['image_url']): ?>
                <img src="<?= e($news['image_url']) ?>" alt="<?= e($news['title']) ?>" 
                     class="img-fluid rounded mb-4 w-100" style="max-height: 400px; object-fit: cover;">
                <?php endif; ?>
                
                <!-- Контент -->
                <div class="news-content mb-5">
                    <?= $news['content'] ?>
                </div>
                
                <!-- Кнопка назад -->
                <a href="?page=news" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Все новости
                </a>
            </div>
            
            <!-- Сайдбар -->
            <div class="col-lg-4">
                <div class="card bg-dark border-gray-800 sticky-top" style="top: 100px;">
                    <div class="card-header border-gray-800">
                        <h5 class="fw-bold mb-0">Другие новости</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($otherNews as $other): ?>
                        <div class="d-flex mb-3">
                            <?php if ($other['image_url']): ?>
                            <img src="<?= e($other['image_url']) ?>" alt="" 
                                 style="width: 80px; height: 60px; object-fit: cover;" class="rounded me-3">
                            <?php endif; ?>
                            <div>
                                <a href="?page=news&id=<?= $other['id'] ?>" class="text-white text-decoration-none fw-bold">
                                    <?= e(mb_substr($other['title'], 0, 50)) ?>...
                                </a>
                                <div class="small text-gray-500">
                                    <?= date('d.m.Y', strtotime($other['published_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.news-content {
    font-size: 1.1rem;
    line-height: 1.8;
}
.news-content h3 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}
.news-content ul, .news-content ol {
    margin-bottom: 1.5rem;
}
.news-content p {
    margin-bottom: 1.5rem;
}
</style>