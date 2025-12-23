<?php
require_once 'includes/db_connect.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Управление голосованием</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .card { margin-bottom: 20px; }
        .player-card { transition: transform 0.3s; }
        .player-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="bi bi-bar-chart-line me-2"></i>
                            Админ-панель: Голосование за MVP финала LFK
                        </h3>
                    </div>
                    <div class="card-body">';

// Получаем текущие голоса из LocalStorage (имитация)
$players = [
    'Орлов Сергей' => 42,
    'Шереметьев Роман' => 35,
    'Перетолчин Леонид' => 28
];

$totalVotes = array_sum($players);
arsort($players);

echo '<h4 class="mb-4">Текущие результаты голосования</h4>';

echo '<div class="row mb-4">';
foreach ($players as $player => $votes) {
    $percentage = $totalVotes > 0 ? round(($votes / $totalVotes) * 100) : 0;
    
    echo '<div class="col-md-4 mb-3">
            <div class="card player-card">
                <div class="card-body text-center">
                    <h5 class="card-title">' . htmlspecialchars($player) . '</h5>
                    <div class="display-4 fw-bold text-primary mb-2">' . $votes . '</div>
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-primary" style="width: ' . $percentage . '%">
                            ' . $percentage . '%
                        </div>
                    </div>
                    <p class="text-muted small">' . $votes . ' из ' . $totalVotes . ' голосов</p>
                </div>
            </div>
        </div>';
}
echo '</div>';

// Форма для ручного управления
echo '<h4 class="mb-4">Ручное управление голосами</h4>
<form method="POST" class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Игрок</label>
        <select name="player" class="form-select">
            <option value="Орлов Сергей">Орлов Сергей</option>
            <option value="Шереметьев Роман">Шереметьев Роман</option>
            <option value="Перетолчин Леонид">Перетолчин Леонид</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Добавить голосов</label>
        <input type="number" name="add_votes" class="form-control" value="1" min="1" max="100">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <button type="submit" name="add" class="btn btn-success w-100">
            <i class="bi bi-plus-circle me-1"></i> Добавить голоса
        </button>
    </div>
</form>

<hr class="my-4">

<div class="alert alert-info">
    <h5><i class="bi bi-info-circle me-2"></i>Информация о голосовании</h5>
    <ul class="mb-0">
        <li>Всего проголосовало: <strong>' . $totalVotes . '</strong> пользователей</li>
        <li>Лидирует: <strong>' . array_key_first($players) . '</strong></li>
        <li>Голосование активно до: 31.05.2024</li>
    </ul>
</div>

<div class="text-center mt-4">
    <a href="?page=home" class="btn btn-primary">
        <i class="bi bi-eye me-1"></i> Посмотреть на сайте
    </a>
    <button onclick="location.reload()" class="btn btn-secondary ms-2">
        <i class="bi bi-arrow-clockwise me-1"></i> Обновить статистику
    </button>
</div>';

echo '</div></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
?>