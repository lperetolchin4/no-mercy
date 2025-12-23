<section class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-4">Календарь матчей</h1>
        
        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="card bg-dark border-gray-800 text-center">
                    <div class="card-body">
                        <h3 class="fw-bold display-6">5</h3>
                        <p class="text-gray-400 mb-0">Матчей в сезоне</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card bg-dark border-gray-800 text-center">
                    <div class="card-body">
                        <h3 class="fw-bold display-6 text-success">1</h3>
                        <p class="text-gray-400 mb-0">Побед</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card bg-dark border-gray-800 text-center">
                    <div class="card-body">
                        <h3 class="fw-bold display-6 text-primary">1</h3>
                        <p class="text-gray-400 mb-0">Предстоящих</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card bg-dark border-gray-800 text-center">
                    <div class="card-body">
                        <h3 class="fw-bold display-6 text-warning">3</h3>
                        <p class="text-gray-400 mb-0">Будущих</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Фильтры и управление -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="fw-bold">Фильтр:</span>
                    <select id="competition-filter" class="form-select bg-dark border-gray-800 text-white w-auto">
                        <option value="all">Все матчи</option>
                        <option value="Премьер-Лига">Премьер-Лига</option>
                        <option value="Финал кубка LFK">Финал кубка LFK</option>
                        <option value="Кубок страны">Кубок страны</option>
                        <option value="completed">Завершенные</option>
                        <option value="upcoming">Ближайшие</option>
                    </select>
                    <button id="reset-watched" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-arrow-clockwise"></i> Сбросить отметки
                    </button>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="text-gray-400">
                    <span class="badge bg-primary me-2">Д</span> = Дома
                    <span class="badge bg-secondary ms-3 me-2">Г</span> = В гостях
                </div>
            </div>
        </div>
        
        <!-- Легенда статусов -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-dark border-gray-800 p-3">
                    <h6 class="fw-bold mb-2">Легенда статусов:</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <span class="d-flex align-items-center">
                            <span class="badge bg-success me-2">&nbsp;</span>
                            <small>Завершенный матч</small>
                        </span>
                        <span class="d-flex align-items-center">
                            <span class="badge bg-warning me-2">&nbsp;</span>
                            <small>Матч сегодня</small>
                        </span>
                        <span class="d-flex align-items-center">
                            <span class="badge bg-secondary me-2">&nbsp;</span>
                            <small>Пропущенный матч</small>
                        </span>
                        <span class="d-flex align-items-center">
                            <span class="badge bg-primary me-2">&nbsp;</span>
                            <small>Предстоящий матч</small>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
       <!-- Таблица матчей -->
<div class="table-responsive">
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th>Дата</th>
                <th>Соперник</th>
                <th>Турнир</th>
                <th>Результат</th>
                <th>Просмотр</th>
                <th>Билеты</th>
                <th>Инфо</th>
            </tr>
        </thead>
        <tbody id="schedule-table">
            <!-- Данные загружаются через JavaScript -->
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-2">Загрузка календаря...</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
// Простой inline JavaScript для календаря
document.addEventListener('DOMContentLoaded', function() {
    const matches = [
        {
            id: 1,
            date: '20.12.2024',
            opponent: 'Спарта',
            competition: 'Финал кубка LFK',
            result: '3:0',
            venue: 'Дома',
            ticket: 'Победа!',
            desc: 'Историческая победа в финале!'
        },
        {
            id: 2,
            date: '27.12.2024',
            opponent: 'Система',
            competition: 'Премьер-Лига',
            result: '',
            venue: 'Дома',
            ticket: '150 ₽',
            desc: 'Зимний матч'
        },
        {
            id: 3,
            date: '15.01.2025',
            opponent: 'Lucky',
            competition: 'Премьер-Лига',
            result: '',
            venue: 'В гостях',
            ticket: 'Скоро',
            desc: 'После зимнего перерыва'
        }
    ];
    
    const tableBody = document.getElementById('schedule-table');
    if (!tableBody) return;
    
    let html = '';
    
    // Заголовок текущего дня
    const today = new Date().toLocaleDateString('ru-RU');
    html += `<tr class="table-warning">
                <td colspan="7" class="text-center fw-bold py-2">
                    <i class="bi bi-calendar-day"></i> Текущий день: ${today}
                </td>
            </tr>`;
    
    // Матчи
    matches.forEach(match => {
        const isWatched = localStorage.getItem('match_' + match.id) === 'true';
        
        html += `<tr ${isWatched ? 'class="match-watched"' : ''}>
            <td>
                <strong>${match.date}</strong>
                <div class="small text-gray-400">
                    ${match.result ? 'Завершен' : 'Предстоящий'}
                </div>
            </td>
            <td>
                <span class="badge ${match.venue === 'Дома' ? 'bg-primary' : 'bg-secondary'} me-2">
                    ${match.venue === 'Дома' ? 'Д' : 'Г'}
                </span>
                <strong>${match.opponent}</strong>
                <div class="small text-gray-400">${match.desc}</div>
            </td>
            <td><span class="badge bg-dark">${match.competition}</span></td>
            <td>
                ${match.result ? 
                    `<span class="fw-bold text-success">${match.result}</span>` : 
                    '<span class="text-gray-400">− : −</span>'
                }
            </td>
            <td>
                ${match.result ? 
                    `<button class="btn btn-sm ${isWatched ? 'btn-success' : 'btn-outline-light'}" 
                            onclick="toggleMatch(${match.id})">
                        ${isWatched ? '✓ Просмотрено' : 'Отметить'}
                    </button>` :
                    '<span class="text-gray-400">Ещё не сыграно</span>'
                }
            </td>
            <td>
                ${match.ticket === '150 ₽' ?
                    `<button class="btn btn-sm btn-success" onclick="alert('Билет куплен! (демо)')">
                        ${match.ticket}
                    </button>` :
                    `<span class="badge bg-secondary">${match.ticket}</span>`
                }
            </td>
            <td>
                <button class="btn btn-sm btn-outline-info" 
                        onclick="alert('${match.opponent} - ${match.date}\\n${match.desc}')">
                    <i class="bi bi-info-circle"></i>
                </button>
            </td>
        </tr>`;
    });
    
    tableBody.innerHTML = html;
});

function toggleMatch(matchId) {
    const current = localStorage.getItem('match_' + matchId) === 'true';
    localStorage.setItem('match_' + matchId, !current);
    location.reload(); // Перезагружаем для обновления
}
</script>
        
        <!-- Уведомление о демо-режиме -->
        <div class="alert alert-info mt-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div>
                    <h5 class="fw-bold mb-1">Интерактивный календарь матчей</h5>
                    <p class="mb-0">
                        • Нажмите "Отметить как просмотренный" для сохранения в браузере<br>
                        • Кнопка "Купить билет" работает в демо-режиме<br>
                        • Данные обновляются в реальном времени
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Стили для календаря -->
<style>
.match-watched {
    opacity: 0.9;
    background: rgba(40, 167, 69, 0.1) !important;
}

.table-warning {
    background: rgba(255, 193, 7, 0.15) !important;
}

.table-success {
    background: rgba(40, 167, 69, 0.15) !important;
}

.table-secondary {
    background: rgba(108, 117, 125, 0.1) !important;
}

.badge {
    font-weight: 500;
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838, #1ea085);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>