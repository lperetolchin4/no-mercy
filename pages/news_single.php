<?php
// Проверяем наличие ID новости
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($news_id <= 0) {
    // Перенаправляем на страницу всех новостей
    header('Location: ?page=news');
    exit;
}

// Получаем новость из БД
try {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$news_id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$news) {
        header('Location: ?page=news');
        exit;
    }
} catch (Exception $e) {
    die("Ошибка загрузки новости: " . $e->getMessage());
}

// Получаем соседние новости для навигации
try {
    // Предыдущая новость
    $prevStmt = $pdo->prepare("SELECT id, title FROM news WHERE id < ? ORDER BY id DESC LIMIT 1");
    $prevStmt->execute([$news_id]);
    $prevNews = $prevStmt->fetch();
    
    // Следующая новость
    $nextStmt = $pdo->prepare("SELECT id, title FROM news WHERE id > ? ORDER BY id ASC LIMIT 1");
    $nextStmt->execute([$news_id]);
    $nextNews = $nextStmt->fetch();
} catch (Exception $e) {
    // Игнорируем ошибки в навигации
    $prevNews = $nextNews = null;
}
?>

<section class="py-5">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent">
                <li class="breadcrumb-item">
                    <a href="?page=home" class="text-gray-400 text-decoration-none">
                        <i class="bi bi-house-door"></i> Главная
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="?page=news" class="text-gray-400 text-decoration-none">Новости</a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    <?= htmlspecialchars(mb_strimwidth($news['title'], 0, 40, '...')) ?>
                </li>
            </ol>
        </nav>
        
        <article class="news-article">
            <!-- Заголовок и метаданные -->
            <header class="mb-5">
                <h1 class="fw-bold mb-3 display-5"><?= htmlspecialchars($news['title']) ?></h1>
                
                <div class="d-flex flex-wrap gap-4 text-gray-400 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar me-2"></i>
                        <?= date('d.m.Y', strtotime($news['published_at'])) ?>
                    </div>
                    
                    <?php if (!empty($news['author'])): ?>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person me-2"></i>
                        <?= htmlspecialchars($news['author']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock me-2"></i>
                        Время чтения: 3 мин
                    </div>
                </div>
                
                <?php if (!empty($news['image_url'])): ?>
                <div class="mb-4">
                    <img src="<?= htmlspecialchars($news['image_url']) ?>" 
                         alt="<?= htmlspecialchars($news['title']) ?>" 
                         class="img-fluid rounded shadow-lg"
                         style="max-height: 500px; object-fit: cover; width: 100%;">
                    <?php if (isset($news['image_caption'])): ?>
                    <div class="text-center text-gray-400 mt-2 small">
                        <?= htmlspecialchars($news['image_caption']) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="mb-4 bg-dark rounded d-flex align-items-center justify-content-center" 
                     style="height: 300px;">
                    <i class="bi bi-newspaper" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
                <?php endif; ?>
            </header>
            
            <!-- Краткое описание -->
            <?php if (!empty($news['excerpt'])): ?>
            <div class="alert alert-dark border-gray-800 mb-5">
                <div class="d-flex">
                    <i class="bi bi-quote fs-3 me-3 text-primary"></i>
                    <div>
                        <h5 class="fw-bold mb-2">Кратко о новости</h5>
                        <p class="mb-0 fs-5"><?= htmlspecialchars($news['excerpt']) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Основное содержание -->
            <div class="news-content mb-5">
                <?= $news['content'] ?>
            </div>
            
            <!-- Дополнительная информация -->
            <div class="card bg-dark border-gray-800 mb-5">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bi bi-info-circle me-2"></i>Дополнительная информация
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>
                                    <strong>Дата публикации:</strong> 
                                    <?= date('d.m.Y', strtotime($news['published_at'])) ?>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-person text-primary me-2"></i>
                                    <strong>Автор:</strong> 
                                    <?= !empty($news['author']) ? htmlspecialchars($news['author']) : 'Редакция клуба' ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-eye text-primary me-2"></i>
                                    <strong>Просмотры:</strong> 1,247
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-chat text-primary me-2"></i>
                                    <strong>Комментарии:</strong> 12
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Теги -->
            <div class="mb-5">
                <h6 class="fw-bold mb-3">Теги:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#" class="badge bg-primary text-decoration-none">Футбол</a>
                    <a href="#" class="badge bg-primary text-decoration-none">LFK</a>
                    <a href="#" class="badge bg-primary text-decoration-none">No MERCY</a>
                    <a href="#" class="badge bg-primary text-decoration-none">Новости клуба</a>
                    <a href="#" class="badge bg-primary text-decoration-none"><?= date('Y', strtotime($news['published_at'])) ?></a>
                </div>
            </div>
            
            <!-- Навигация между новостями -->
            <div class="row mt-5 pt-4 border-top border-gray-800">
                <div class="col-md-6 mb-3">
                    <?php if ($prevNews): ?>
                    <a href="?page=news&id=<?= $prevNews['id'] ?>" class="text-decoration-none">
                        <div class="card bg-dark border-gray-800 p-3 hover-lift">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-arrow-left-circle fs-4 text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-gray-400 mb-1 small">Предыдущая новость</div>
                                    <div class="fw-bold"><?= htmlspecialchars(mb_strimwidth($prevNews['title'], 0, 50, '...')) ?></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                    <div class="card bg-dark border-gray-800 p-3 opacity-50">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-arrow-left-circle fs-4 text-gray-600"></i>
                            </div>
                            <div>
                                <div class="text-gray-500 mb-1 small">Предыдущая новость</div>
                                <div class="fw-bold text-gray-600">Это первая новость</div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6 mb-3">
                    <?php if ($nextNews): ?>
                    <a href="?page=news&id=<?= $nextNews['id'] ?>" class="text-decoration-none">
                        <div class="card bg-dark border-gray-800 p-3 hover-lift text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="text-end me-0 ms-3">
                                    <div class="text-gray-400 mb-1 small">Следующая новость</div>
                                    <div class="fw-bold"><?= htmlspecialchars(mb_strimwidth($nextNews['title'], 0, 50, '...')) ?></div>
                                </div>
                                <div class="ms-3">
                                    <i class="bi bi-arrow-right-circle fs-4 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                    <div class="card bg-dark border-gray-800 p-3 opacity-50 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="text-end me-0 ms-3">
                                <div class="text-gray-500 mb-1 small">Следующая новость</div>
                                <div class="fw-bold text-gray-600">Это последняя новость</div>
                            </div>
                            <div class="ms-3">
                                <i class="bi bi-arrow-right-circle fs-4 text-gray-600"></i>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Кнопка назад ко всем новостям -->
            <div class="text-center mt-5 pt-5 border-top border-gray-800">
                <a href="?page=news" class="btn btn-primary btn-lg px-5 py-3">
                    <i class="bi bi-arrow-left me-2"></i> Вернуться ко всем новостям
                </a>
            </div>
        </article>
    </div>
</section>

<!-- Стили для новости -->
<style>
.news-article {
    max-width: 900px;
    margin: 0 auto;
}

.news-content {
    color: #e0e0e0;
    line-height: 1.8;
    font-size: 1.1rem;
}

.news-content h2 {
    color: white;
    margin-top: 2.5rem;
    margin-bottom: 1.5rem;
    font-size: 2rem;
    border-bottom: 2px solid #333;
    padding-bottom: 0.5rem;
}

.news-content h3 {
    color: white;
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.news-content h4 {
    color: #ccc;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.news-content p {
    margin-bottom: 1.5rem;
    text-align: justify;
}

.news-content ul, 
.news-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.news-content li {
    margin-bottom: 0.5rem;
    position: relative;
}

.news-content ul li:before {
    content: "•";
    color: #007bff;
    font-weight: bold;
    position: absolute;
    left: -1rem;
}

.news-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 2rem 0;
    border: 1px solid #333;
}

.news-content blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: #aaa;
    background: rgba(0, 123, 255, 0.05);
    padding: 1.5rem;
    border-radius: 0 8px 8px 0;
}

.news-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
    border: 1px solid #444;
}

.news-content th {
    background-color: #ffffffff;
    font-weight: bold;
    text-align: left;
    padding: 1rem;
    border: 1px solid #444;
}

.news-content td {
    padding: 1rem;
    border: 1px solid #444;
}

.news-content tr:nth-child(even) {
    background-color: rgba(255, 255, 255, 0.03);
}

.news-content code {
    background: #222;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}

.news-content pre {
    background: #222;
    padding: 1rem;
    border-radius: 8px;
    overflow-x: auto;
    border: 1px solid #333;
}

.news-content a {
    color: #007bff;
    text-decoration: none;
}

.news-content a:hover {
    text-decoration: underline;
}

.hover-lift {
    transition: transform 0.2s, box-shadow 0.2s;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

@media (max-width: 768px) {
    .news-content {
        font-size: 1rem;
    }
    
    .news-content h2 {
        font-size: 1.75rem;
    }
    
    .news-content h3 {
        font-size: 1.35rem;
    }
}
</style>