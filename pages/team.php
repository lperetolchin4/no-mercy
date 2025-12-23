<section class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-4">Команда</h1>
        
        <!-- Фильтры -->
        <div class="mb-5">
            <h3 class="fw-bold mb-3">Состав команды</h3>
            <div class="d-flex flex-wrap gap-2 player-filters">
                <button class="btn btn-primary filter-btn active" data-filter="all">Все игроки</button>
                <button class="btn btn-outline-light filter-btn" data-filter="вратарь">Вратари</button>
                <button class="btn btn-outline-light filter-btn" data-filter="защитник">Защитники</button>
                <button class="btn btn-outline-light filter-btn" data-filter="полузащитник">Полузащитники</button>
                <button class="btn btn-outline-light filter-btn" data-filter="нападающий">Нападающие</button>
            </div>
        </div>
        
        <!-- Сетка игроков -->
        <div class="row" id="players-container">
            <!-- Игроки будут загружены через JavaScript -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <p class="mt-3">Загрузка состава команды...</p>
            </div>
        </div>
        
        <!-- Статистика команды -->
        <div class="row mt-5">
            <div class="col-md-3 col-6 mb-4 text-center">
                <div class="bg-dark p-4 rounded">
                    <h3 class="fw-bold display-4">24</h3>
                    <p class="text-gray-400 mb-0">Игрока в составе</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 text-center">
                <div class="bg-dark p-4 rounded">
                    <h3 class="fw-bold display-4">7</h3>
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
                    <h3 class="fw-bold display-4">186</h3>
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
            <div class="modal-body">
                <!-- Контент будет загружен через JavaScript -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-gray-800">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>