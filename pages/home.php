<!-- Герой-секция -->
<section class="hero-section">
    <img src="assets/img/hero-bg.jpg" alt="Стадион No MERCY" class="hero-bg">
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="display-1 fw-bold mb-3">NO MERCY FC</h1>
                <p class="lead fs-3 mb-4">Вы думали, мы тут в футбол играем? А мы тут жизнь живем!</p>
                <a href="?page=schedule" class="btn btn-primary btn-lg px-5">Купить билеты</a>
            </div>
        </div>
    </div>
</section>

<!-- Ближайший матч -->
<section class="py-5 bg-dark">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Ближайший матч</h2>
        <div class="row align-items-center">
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="bg-black p-4 rounded">
                    <h3 class="fw-bold">No MERCY</h3>
                    <p class="text-gray-400">Дома</p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div>
                    <h4 class="fw-bold">AFL</h4>
                    <p class="text-gray-400">27 декабря 2025, 22:00</p>
                    <p class="mb-0">Мерси-Арена</p>
                    <div class="match-countdown mt-3">
                        <div id="countdown" class="fs-1 fw-bold"></div>
                        <p class="text-gray-400 mt-2">До начала матча осталось:</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-black p-4 rounded">
                    <h3 class="fw-bold">Система</h3>
                    <p class="text-gray-400">В гостях</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="?page=schedule" class="btn btn-primary px-5">Купить билеты</a>
        </div>
    </div>
</section>

<!-- Новости -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Последние новости</h2>
            <a href="?page=news" class="btn btn-outline-light">Все новости</a>
        </div>
        <div class="row">
            <?php
            // Используем getDB() вместо $pdo
            $db = getDB();
            $stmt = $db->query("SELECT * FROM news WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3");
            $newsItems = $stmt->fetchAll();
            
            if (empty($newsItems)): 
            ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Новостей пока нет
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($newsItems as $row): ?>
                <div class="col-md-4 mb-4">
                    <div class="card bg-dark border-gray-800 h-100">
                        <?php if ($row['image_url']): ?>
                        <img src="<?= e($row['image_url']) ?>" class="card-img-top" alt="<?= e($row['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= e($row['title']) ?></h5>
                            <p class="card-text text-gray-400"><?= e($row['excerpt']) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-gray-500"><?= date('d.m.Y', strtotime($row['published_at'])) ?></small>
                                <a href="?page=news&id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Читать</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- React компонент -->
<section class="py-5 bg-dark">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h2 class="fw-bold mb-2">Голосование за лучшего игрока</h2>
                    <p class="text-gray-400">Выберите самого ценного игрока финала кубка LFK</p>
                </div>
                <div id="top-scorer-container"></div>
                <script type="text/babel" src="assets/js/components/top-scorer.jsx"></script>
            </div>
        </div>
    </div>
</section>