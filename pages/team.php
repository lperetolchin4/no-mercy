<?php
$db = getDB();
$players = $db->query("SELECT * FROM players WHERE is_active = 1 ORDER BY number")->fetchAll();

// Группируем по позициям для статистики
$positions = ['вратарь' => 0, 'защитник' => 0, 'полузащитник' => 0, 'нападающий' => 0];
foreach ($players as $p) {
    if (isset($positions[$p['position']])) {
        $positions[$p['position']]++;
    }
}
?>

<section class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-4">Команда</h1>
        
        <!-- Фильтры -->
        <div class="mb-5">
            <h3 class="fw-bold mb-3">Состав команды</h3>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary filter-btn active" data-filter="all">
                    Все игроки <span class="badge bg-light text-dark ms-1"><?= count($players) ?></span>
                </button>
                <button class="btn btn-outline-light filter-btn" data-filter="вратарь">
                    Вратари <span class="badge bg-secondary ms-1"><?= $positions['вратарь'] ?></span>
                </button>
                <button class="btn btn-outline-light filter-btn" data-filter="защитник">
                    Защитники <span class="badge bg-secondary ms-1"><?= $positions['защитник'] ?></span>
                </button>
                <button class="btn btn-outline-light filter-btn" data-filter="полузащитник">
                    Полузащитники <span class="badge bg-secondary ms-1"><?= $positions['полузащитник'] ?></span>
                </button>
                <button class="btn btn-outline-light filter-btn" data-filter="нападающий">
                    Нападающие <span class="badge bg-secondary ms-1"><?= $positions['нападающий'] ?></span>
                </button>
            </div>
        </div>
        
        <!-- Сетка игроков -->
        <div class="row" id="players-container">
            <?php if (empty($players)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        В команде пока нет игроков. 
                        <?php if (isAdmin()): ?>
                            <a href="?page=admin">Добавить игрока</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($players as $player): ?>
                <div class="col-md-3 col-sm-6 mb-4 player-item" data-position="<?= e($player['position']) ?>">
                    <div class="player-card h-100" data-bs-toggle="modal" data-bs-target="#playerModal" 
                         data-player='<?= htmlspecialchars(json_encode($player, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>'>
                        <img src="<?= e($player['image'] ?: 'assets/img/players/default.jpg') ?>" 
                             alt="<?= e($player['name']) ?>" 
                             class="player-img"
                             onerror="this.src='assets/img/players/default.jpg'">
                        <div class="p-3">
                            <h5 class="fw-bold mb-1"><?= e($player['name']) ?></h5>
                            <p class="text-gray-400 mb-1">№ <?= $player['number'] ?></p>
                            <span class="badge bg-dark"><?= e($player['position']) ?></span>
                            <div class="mt-2 small text-gray-400">
                                <span title="Матчи"><?= $player['matches_played'] ?? 0 ?> М</span> |
                                <span title="Голы" class="text-success"><?= $player['goals'] ?? 0 ?> Г</span> |
                                <span title="Передачи" class="text-info"><?= $player['assists'] ?? 0 ?> П</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Статистика команды -->
        <div class="row mt-5">
            <div class="col-md-3 col-6 mb-4 text-center">
                <div class="bg-dark p-4 rounded">
                    <h3 class="fw-bold display-4"><?= count($players) ?></h3>
                    <p class="text-gray-400 mb-0">Игроков в составе</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 text-center">
                <div class="bg-dark p-4 rounded">
                    <?php 
                    $foreigners = 0;
                    foreach ($players as $p) {
                        if (($p['nationality'] ?? 'Россия') !== 'Россия') {
                            $foreigners++;
                        }
                    }
                    ?>
                    <h3 class="fw-bold display-4"><?= $foreigners ?></h3>
                    <p class="text-gray-400 mb-0">Легионеров</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 text-center">
                <div class="bg-dark p-4 rounded">
                    <h3 class="fw-bold display-4">25.4</h3>
                    <p class="text-gray-400 mb-0">Средний возраст</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 text-center">
                <div class="bg-dark p-4 rounded">
                    <?php 
                    $heights = array_filter(array_column($players, 'height'));
                    $avgHeight = !empty($heights) ? round(array_sum($heights) / count($heights)) : 186;
                    ?>
                    <h3 class="fw-bold display-4"><?= $avgHeight ?></h3>
                    <p class="text-gray-400 mb-0">Средний рост (см)</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Модальное окно игрока -->
<div class="modal fade" id="playerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-gray-800">
                <h5 class="modal-title">Информация об игроке</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="playerModalBody">
                <!-- Контент загружается через JavaScript -->
            </div>
            <div class="modal-footer border-gray-800">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Фильтрация игроков
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Убираем активный класс у всех кнопок
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-light');
            });
            
            // Добавляем активный класс текущей кнопке
            this.classList.remove('btn-outline-light');
            this.classList.add('active', 'btn-primary');
            
            const filter = this.dataset.filter;
            
            // Показываем/скрываем игроков
            document.querySelectorAll('.player-item').forEach(item => {
                if (filter === 'all' || item.dataset.position === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Модальное окно с деталями игрока
    const playerModal = document.getElementById('playerModal');
    if (playerModal) {
        playerModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const playerData = button.dataset.player;
            
            try {
                const player = JSON.parse(playerData);
                
                document.getElementById('playerModalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-5">
                            <img src="${player.image || 'assets/img/players/default.jpg'}" 
                                 class="img-fluid rounded" 
                                 alt="${player.name}"
                                 onerror="this.src='assets/img/players/default.jpg'">
                        </div>
                        <div class="col-md-7">
                            <h3 class="fw-bold">${player.name}</h3>
                            <p class="text-gray-400 mb-3">
                                <span class="badge bg-dark me-2">${player.position}</span>
                                № ${player.number}
                            </p>
                            
                            <h5>Статистика сезона</h5>
                            <div class="row mb-3">
                                <div class="col-4 text-center">
                                    <div class="bg-black p-3 rounded">
                                        <div class="fs-3 fw-bold">${player.matches_played || 0}</div>
                                        <small class="text-gray-400">Матчей</small>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="bg-black p-3 rounded">
                                        <div class="fs-3 fw-bold text-success">${player.goals || 0}</div>
                                        <small class="text-gray-400">Голов</small>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="bg-black p-3 rounded">
                                        <div class="fs-3 fw-bold text-info">${player.assists || 0}</div>
                                        <small class="text-gray-400">Передач</small>
                                    </div>
                                </div>
                            </div>
                            
                            <ul class="list-unstyled">
                                ${player.nationality ? `<li><strong>Национальность:</strong> ${player.nationality}</li>` : ''}
                                ${player.height ? `<li><strong>Рост:</strong> ${player.height} см</li>` : ''}
                                ${player.weight ? `<li><strong>Вес:</strong> ${player.weight} кг</li>` : ''}
                                <li><strong>Жёлтые карточки:</strong> ${player.yellow_cards || 0}</li>
                                <li><strong>Красные карточки:</strong> ${player.red_cards || 0}</li>
                            </ul>
                            
                            ${player.bio ? `<p class="text-gray-400 mt-3">${player.bio}</p>` : ''}
                        </div>
                    </div>
                `;
                
                this.querySelector('.modal-title').textContent = player.name;
            } catch (e) {
                console.error('Ошибка парсинга данных игрока:', e);
                document.getElementById('playerModalBody').innerHTML = '<p class="text-danger">Ошибка загрузки данных</p>';
            }
        });
    }
});
</script>