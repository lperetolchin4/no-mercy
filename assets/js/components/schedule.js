export function initSchedule() {
    console.log('Инициализация календаря...');
    
    const tableBody = document.querySelector('#schedule-table tbody');
    if (!tableBody) {
        console.error('Не найден элемент #schedule-table tbody');
        return;
    }
    
    // Жестко закодированные данные матчей
    const matches = [
        {
            id: 1,
            date: "2024-12-20",
            opponent: "Спарта",
            venue: "Дома",
            competition: "Финал кубка LFK",
            result: "3:0",
            ticket_price: "0",
            ticket_status: "Победа!",
            description: "Историческая победа в финале кубка LFK!"
        },
        {
            id: 2,
            date: "2024-12-27",
            opponent: "Система",
            venue: "Дома",
            competition: "Премьер-Лига",
            result: "",
            ticket_price: "150",
            ticket_status: "Купить билет",
            description: "Зимний матч против лидера лиги"
        },
        {
            id: 3,
            date: "2025-01-15",
            opponent: "Lucky",
            venue: "В гостях",
            competition: "Премьер-Лига",
            result: "",
            ticket_price: "",
            ticket_status: "Скоро в продаже",
            description: "Первая игра после зимнего перерыва"
        },
        {
            id: 4,
            date: "2025-01-25",
            opponent: "Торпедо",
            venue: "Дома",
            competition: "Кубок страны",
            result: "",
            ticket_price: "200",
            ticket_status: "Скоро в продаже",
            description: "1/4 финала кубка страны"
        },
        {
            id: 5,
            date: "2025-02-05",
            opponent: "Динамо",
            venue: "В гостях",
            competition: "Премьер-Лига",
            result: "",
            ticket_price: "300",
            ticket_status: "Скоро в продаже",
            description: "Дерби с принципиальным соперником"
        }
    ];
    
    // Функция форматирования даты
    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (e) {
            return dateString;
        }
    }
    
    // Функция определения статуса матча
    function getMatchStatus(match) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const matchDate = new Date(match.date);
        matchDate.setHours(0, 0, 0, 0);
        
        if (match.result) return 'completed';
        if (matchDate.getTime() === today.getTime()) return 'today';
        if (matchDate < today) return 'past';
        return 'upcoming';
    }
    
    // Функция отрисовки таблицы
    function renderTable() {
        console.log('Отрисовка таблицы...');
        
        // Очищаем таблицу
        tableBody.innerHTML = '';
        
        // Добавляем заголовок текущего дня
        const todayRow = document.createElement('tr');
        todayRow.className = 'table-warning';
        todayRow.innerHTML = `
            <td colspan="7" class="text-center fw-bold py-2">
                <i class="bi bi-calendar-day"></i> Текущий день: ${formatDate(new Date().toISOString().split('T')[0])}
            </td>
        `;
        tableBody.appendChild(todayRow);
        
        // Добавляем матчи
        matches.forEach(match => {
            const status = getMatchStatus(match);
            const isWatched = localStorage.getItem(`match_${match.id}`) === 'true';
            
            let statusBadge = '';
            let statusText = '';
            
            switch(status) {
                case 'completed':
                    statusBadge = '<span class="badge bg-success me-2">✓</span>';
                    statusText = 'Завершен';
                    break;
                case 'today':
                    statusBadge = '<span class="badge bg-warning me-2">!</span>';
                    statusText = 'Сегодня';
                    break;
                case 'past':
                    statusBadge = '<span class="badge bg-secondary me-2">⌛</span>';
                    statusText = 'Прошедший';
                    break;
                case 'upcoming':
                    statusBadge = '<span class="badge bg-primary me-2">→</span>';
                    statusText = 'Предстоящий';
                    break;
            }
            
            const row = document.createElement('tr');
            if (isWatched) {
                row.classList.add('match-watched');
            }
            
            // Определяем кнопку для билетов
            let ticketButton = '';
            if (match.result) {
                ticketButton = `<button class="btn btn-sm btn-outline-secondary" onclick="alert('Статистика матча: ${match.result}')">
                    Статистика
                </button>`;
            } else if (match.ticket_status === 'Купить билет') {
                ticketButton = `<button class="btn btn-sm btn-success" onclick="buyTicket(${match.id})">
                    ${match.ticket_price} ₽
                </button>`;
            } else {
                ticketButton = `<span class="badge bg-secondary">${match.ticket_status}</span>`;
            }
            
            row.innerHTML = `
                <td>
                    ${statusBadge}
                    <strong>${formatDate(match.date)}</strong>
                    <div class="small text-gray-400">${statusText}</div>
                </td>
                <td>
                    <span class="badge ${match.venue === 'Дома' ? 'bg-primary' : 'bg-secondary'} me-2">
                        ${match.venue === 'Дома' ? 'Д' : 'Г'}
                    </span>
                    <strong>${match.opponent}</strong>
                    ${match.description ? `<div class="small text-gray-400 mt-1">${match.description}</div>` : ''}
                </td>
                <td>
                    <span class="badge bg-dark">${match.competition}</span>
                </td>
                <td>
                    ${match.result ? `<span class="fw-bold text-success">${match.result}</span>` : '<span class="text-gray-400">− : −</span>'}
                </td>
                <td>
                    ${status === 'completed' || status === 'past' ? 
                        `<button class="btn btn-sm ${isWatched ? 'btn-success' : 'btn-outline-light'} toggle-watch" data-id="${match.id}">
                            ${isWatched ? '✓ Просмотрено' : 'Отметить'}
                        </button>` : 
                        `<span class="text-gray-400">${statusText}</span>`
                    }
                </td>
                <td>
                    ${ticketButton}
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-info" onclick="showMatchInfo(${match.id})">
                        <i class="bi bi-info-circle"></i>
                    </button>
                </td>
            `;
            
            tableBody.appendChild(row);
        });
        
        // Добавляем обработчики для кнопок "просмотрено"
        document.querySelectorAll('.toggle-watch').forEach(button => {
            button.addEventListener('click', function() {
                const matchId = this.dataset.id;
                const currentState = localStorage.getItem(`match_${matchId}`) === 'true';
                localStorage.setItem(`match_${matchId}`, !currentState);
                showNotification(`Матч отмечен как ${!currentState ? 'просмотренный' : 'непросмотренный'}`);
                renderTable(); // Перерисовываем таблицу
            });
        });
    }
    
    // Функция показа уведомления
    function showNotification(message) {
        // Создаем простое уведомление
        const notification = document.createElement('div');
        notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
    
    // Глобальные функции
    window.buyTicket = function(matchId) {
        const match = matches.find(m => m.id == matchId);
        if (match) {
            const confirmBuy = confirm(`Купить билет на матч против ${match.opponent}?\n\nДата: ${formatDate(match.date)}\nЦена: ${match.ticket_price} ₽\n\nНажмите OK для покупки (демо-режим)`);
            if (confirmBuy) {
                showNotification(`✅ Билет на матч с ${match.opponent} успешно приобретен!`);
            }
        }
    };
    
    window.showMatchInfo = function(matchId) {
        const match = matches.find(m => m.id == matchId);
        if (match) {
            alert(`Информация о матче:\n\n` +
                  `Соперник: ${match.opponent}\n` +
                  `Дата: ${formatDate(match.date)}\n` +
                  `Турнир: ${match.competition}\n` +
                  `Место: ${match.venue}\n` +
                  `Результат: ${match.result || 'Ещё не сыграно'}\n` +
                  `Билеты: ${match.ticket_price ? match.ticket_price + ' ₽' : match.ticket_status}\n` +
                  `Описание: ${match.description}`);
        }
    };
    
    // Инициализация фильтра
    const filterSelect = document.getElementById('competition-filter');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            // В упрощенной версии просто перерисовываем таблицу
            renderTable();
        });
    }
    
    // Инициализация кнопки сброса
    const resetBtn = document.getElementById('reset-watched');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (confirm('Сбросить все отметки о просмотренных матчах?')) {
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key.startsWith('match_')) {
                        localStorage.removeItem(key);
                    }
                }
                showNotification('Все отметки сброшены');
                renderTable();
            }
        });
    }
    
    // Начальная отрисовка
    renderTable();
    console.log('Календарь инициализирован успешно');
}