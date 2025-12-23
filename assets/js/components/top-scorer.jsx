// React компонент "Лучший игрок финала кубка LFK"
'use strict';

const { useState, useEffect } = React;

function FinalMVP() {
    // Данные игроков с фотографиями
    const players = [
        {
            id: 1,
            name: 'Орлов Сергей',
            position: 'Полузащитник',
            number: 7,
            photo: 'assets/img/players/player11.jpg',
            stats: '3 гола + 7 передач',
            initialVotes: 42
        },
        {
            id: 2,
            name: 'Шереметьев Роман',
            position: 'Нападающий',
            number: 11,
            photo: 'assets/img/players/player5.jpg',
            stats: '12 голов + 1 передача',
            initialVotes: 35
        },
        {
            id: 3,
            name: 'Перетолчин Леонид',
            position: 'Полузащитник',
            number: 8,
            photo: 'assets/img/players/player1.jpg',
            stats: '4 гола + 5 передач',
            initialVotes: 28
        }
    ];
    
    const [votes, setVotes] = useState({});
    const [totalVotes, setTotalVotes] = useState(0);
    const [hasVoted, setHasVoted] = useState(false);
    const [selectedPlayer, setSelectedPlayer] = useState(null);
    const [isLoading, setIsLoading] = useState(true);
    
    // Загружаем голоса при монтировании
    useEffect(() => {
        loadVotes();
    }, []);
    
    // Функция загрузки голосов
    const loadVotes = () => {
        try {
            const savedVotes = localStorage.getItem('lfk_final_mvp_votes');
            const savedTotal = localStorage.getItem('lfk_final_mvp_total');
            const voted = localStorage.getItem('lfk_final_has_voted');
            
            if (savedVotes) {
                const parsedVotes = JSON.parse(savedVotes);
                setVotes(parsedVotes);
                
                const total = Object.values(parsedVotes).reduce((a, b) => a + b, 0);
                setTotalVotes(total);
            } else {
                // Начальные значения из данных игроков
                const initialVotes = {};
                let total = 0;
                
                players.forEach(player => {
                    initialVotes[player.name] = player.initialVotes;
                    total += player.initialVotes;
                });
                
                setVotes(initialVotes);
                setTotalVotes(total);
                localStorage.setItem('lfk_final_mvp_votes', JSON.stringify(initialVotes));
                localStorage.setItem('lfk_final_mvp_total', total.toString());
            }
            
            if (voted === 'true') {
                setHasVoted(true);
            }
        } catch (error) {
            console.error('Ошибка загрузки голосов:', error);
        } finally {
            setIsLoading(false);
        }
    };
    
    // Функция голосования
    const handleVote = (playerName) => {
        if (hasVoted) {
            showNotification('Вы уже голосовали в этом опросе!', 'warning');
            return;
        }
        
        if (!playerName) {
            showNotification('Выберите игрока для голосования!', 'warning');
            return;
        }
        
        setSelectedPlayer(playerName);
        
        const newVotes = {
            ...votes,
            [playerName]: votes[playerName] + 1
        };
        
        setVotes(newVotes);
        setTotalVotes(totalVotes + 1);
        setHasVoted(true);
        
        localStorage.setItem('lfk_final_mvp_votes', JSON.stringify(newVotes));
        localStorage.setItem('lfk_final_mvp_total', (totalVotes + 1).toString());
        localStorage.setItem('lfk_final_has_voted', 'true');
        
        showNotification(`✅ Ваш голос за ${playerName} учтен!`, 'success');
    };
    
    // Функция сброса голосования
    const resetVotes = () => {
        if (confirm('Сбросить все голоса? Это действие нельзя отменить.')) {
            const initialVotes = {};
            let total = 0;
            
            players.forEach(player => {
                initialVotes[player.name] = 0;
            });
            
            setVotes(initialVotes);
            setTotalVotes(0);
            setHasVoted(false);
            setSelectedPlayer(null);
            
            localStorage.removeItem('lfk_final_mvp_votes');
            localStorage.removeItem('lfk_final_mvp_total');
            localStorage.removeItem('lfk_final_has_voted');
            
            showNotification('Голосование сброшено', 'info');
        }
    };
    
    // Функция показа уведомления
    const showNotification = (message, type) => {
        const oldNotifications = document.querySelectorAll('.custom-notification');
        oldNotifications.forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `custom-notification alert alert-${type} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease;
            background: #000 !important;
            border: 1px solid #333 !important;
            color: white !important;
        `;
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    };
    
    // Рассчитываем проценты
    const calculatePercentage = (playerVotes) => {
        if (totalVotes === 0) return 0;
        return Math.round((playerVotes / totalVotes) * 100);
    };
    
    // Находим лидера
    const findLeader = () => {
        let leader = '';
        let maxVotes = 0;
        
        Object.entries(votes).forEach(([player, voteCount]) => {
            if (voteCount > maxVotes) {
                maxVotes = voteCount;
                leader = player;
            }
        });
        
        return { leader, votes: maxVotes };
    };
    
    const leader = findLeader();
    
    // Стили для анимации
    useEffect(() => {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            
            .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
            
            .player-vote-card {
                transition: all 0.3s ease;
                cursor: pointer;
                border: 2px solid transparent;
                height: 100%;
            }
            
            .player-vote-card:hover {
                transform: translateY(-5px);
                border-color: #666;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            }
            
            .player-vote-card.selected {
                border-color: #007bff;
                background: rgba(0, 123, 255, 0.1);
            }
            
            .player-vote-card.voted {
                border-color: #28a745;
                background: rgba(40, 167, 69, 0.1);
            }
            
            .player-photo {
                width: 100%;
                height: 180px;
                object-fit: cover;
                border-bottom: 1px solid #333;
            }
            
            .player-number {
                position: absolute;
                top: 10px;
                left: 10px;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 1.2rem;
                border: 2px solid white;
            }
            
            .leader-badge {
                position: absolute;
                top: 10px;
                right: 10px;
                background: #ffc107;
                color: #000;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                z-index: 10;
                border: 2px solid #000;
            }
            
            .progress-bar-animated {
                animation: progress-animation 1s ease-in-out;
            }
            
            @keyframes progress-animation {
                from { width: 0%; }
                to { width: var(--target-width); }
            }
            
            .player-position {
                font-size: 0.85rem;
                color: #aaa;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .player-stats {
                font-size: 0.8rem;
                color: #6c757d;
                margin-top: 5px;
            }
        `;
        document.head.appendChild(style);
        
        return () => {
            document.head.removeChild(style);
        };
    }, []);
    
    if (isLoading) {
        return (
            <div className="card bg-dark border-gray-800 p-4">
                <div className="d-flex justify-content-center py-4">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Загрузка...</span>
                    </div>
                </div>
            </div>
        );
    }
    
    return (
        <div className="card bg-dark border-gray-800 p-4 shadow-lg">
            <div className="card-header border-gray-800 pb-3 mb-3">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 className="card-title fw-bold mb-0 d-flex align-items-center">
                            <i className="bi bi-trophy-fill text-warning me-2"></i>
                            Лучший игрок финала кубка LFK
                        </h3>
                        <p className="text-gray-400 mb-0 mt-1 small">
                            Голосуйте за самого ценного игрока решающего матча
                        </p>
                    </div>
                    <div className="text-end">
                        <div className="badge bg-primary rounded-pill fs-6">{totalVotes} голосов</div>
                        <div className="small text-gray-400 mt-1">Всего игроков: {players.length}</div>
                    </div>
                </div>
            </div>
            
            <div className="card-body">
                {/* Информация о лидере */}
                {leader.votes > 0 && (
                    <div className="alert alert-dark border-gray-800 mb-4">
                        <div className="d-flex align-items-center">
                            <i className="bi bi-star-fill text-warning fs-4 me-3"></i>
                            <div>
                                <h5 className="fw-bold mb-1">Текущий лидер голосования</h5>
                                <p className="mb-0">
                                    <span className="fw-bold">{leader.leader}</span> 
                                    <span className="text-gray-400 ms-2">
                                        ({leader.votes} голосов • {calculatePercentage(leader.votes)}%)
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                )}
                
                {/* Карточки игроков с фото */}
                <div className="row mb-4">
                    {players.map((player) => {
                        const playerVotes = votes[player.name] || 0;
                        const percentage = calculatePercentage(playerVotes);
                        const isSelected = selectedPlayer === player.name;
                        const isLeader = leader.leader === player.name && leader.votes > 0;
                        
                        return (
                            <div className="col-md-4 mb-3" key={player.id}>
                                <div 
                                    className={`player-vote-card card bg-dark border-gray-800 position-relative ${
                                        isSelected ? 'selected' : ''
                                    } ${hasVoted ? 'voted' : ''}`}
                                    onClick={() => !hasVoted && setSelectedPlayer(player.name)}
                                    style={{ cursor: hasVoted ? 'default' : 'pointer' }}
                                >
                                    {/* Фото игрока */}
                                    <div className="position-relative">
                                        <img 
                                            src={player.photo} 
                                            alt={player.name}
                                            className="player-photo"
                                            onError={(e) => {
                                                e.target.src = 'assets/img/players/default.jpg';
                                                e.target.alt = 'Фото не найдено';
                                            }}
                                        />
                                        <div className="player-number">#{player.number}</div>
                                        {isLeader && (
                                            <div className="leader-badge" title="Лидер голосования">
                                                <i className="bi bi-trophy-fill"></i>
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="card-body">
                                        {/* Имя и позиция */}
                                        <h5 className="card-title fw-bold mb-1">{player.name}</h5>
                                        <div className="player-position">{player.position}</div>
                                        <div className="player-stats">{player.stats}</div>
                                        
                                        <div className="mt-3">
                                            {/* Прогресс бар голосов */}
                                            <div className="d-flex justify-content-between align-items-center mb-1">
                                                <span className="text-gray-400 small">Голоса</span>
                                                <span className="fw-bold">{playerVotes}</span>
                                            </div>
                                            <div className="progress bg-gray-900" style={{height: '8px'}}>
                                                <div 
                                                    className="progress-bar bg-primary progress-bar-animated" 
                                                    style={{ 
                                                        width: `${percentage}%`,
                                                        '--target-width': `${percentage}%`
                                                    }}
                                                ></div>
                                            </div>
                                            <div className="text-end small text-gray-400 mt-1">
                                                {percentage}% от всех голосов
                                            </div>
                                        </div>
                                        
                                        {/* Кнопка голосования */}
                                        {!hasVoted && (
                                            <button
                                                className={`btn w-100 mt-3 ${isSelected ? 'btn-primary' : 'btn-outline-light'}`}
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    handleVote(player.name);
                                                }}
                                                disabled={!isSelected}
                                            >
                                                {isSelected ? (
                                                    <>
                                                        <i className="bi bi-check-circle me-2"></i>
                                                        Голосовать за {player.name.split(' ')[0]}
                                                    </>
                                                ) : (
                                                    'Выбрать для голосования'
                                                )}
                                            </button>
                                        )}
                                        
                                        {/* Статус голосования */}
                                        {hasVoted && selectedPlayer === player.name && (
                                            <div className="text-center text-success mt-3">
                                                <i className="bi bi-check-circle-fill me-1"></i>
                                                Вы проголосовали за этого игрока
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
                
                {/* Кнопка подтверждения */}
                {!hasVoted && selectedPlayer && (
                    <div className="text-center mb-4">
                        <button
                            className="btn btn-primary btn-lg px-5 py-3"
                            onClick={() => handleVote(selectedPlayer)}
                        >
                            <i className="bi bi-check-circle-fill me-2"></i>
                            Подтвердить голос за {selectedPlayer}
                        </button>
                    </div>
                )}
                
                {/* Статистика */}
                <div className="card bg-dark border-gray-800 mb-3">
                    <div className="card-body">
                        <h5 className="fw-bold mb-3">
                            <i className="bi bi-bar-chart-line me-2"></i>
                            Статистика голосования
                        </h5>
                        <div className="row">
                            <div className="col-md-6">
                                <ul className="list-unstyled">
                                    <li className="mb-2 d-flex justify-content-between">
                                        <span>Всего голосов:</span>
                                        <span className="fw-bold">{totalVotes}</span>
                                    </li>
                                    <li className="mb-2 d-flex justify-content-between">
                                        <span>Лидирует:</span>
                                        <span className="fw-bold text-warning">{leader.leader}</span>
                                    </li>
                                    <li className="mb-2 d-flex justify-content-between">
                                        <span>Активных игроков:</span>
                                        <span className="fw-bold">{players.length}</span>
                                    </li>
                                </ul>
                            </div>
                            <div className="col-md-6">
                                <ul className="list-unstyled">
                                    <li className="mb-2 d-flex justify-content-between">
                                        <span>Ваш статус:</span>
                                        <span className={hasVoted ? 'text-success fw-bold' : 'text-warning'}>
                                            {hasVoted ? 'Проголосовал' : 'Не голосовал'}
                                        </span>
                                    </li>
                                    <li className="mb-2 d-flex justify-content-between">
                                        <span>Средний рейтинг:</span>
                                        <span className="fw-bold">{Math.round(totalVotes / players.length)}</span>
                                    </li>
                                    <li className="mb-2 d-flex justify-content-between">
                                        <span>Голосование до:</span>
                                        <span className="text-gray-400">31.12.2024</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                {/* Информация */}
                <div className="text-center small text-gray-500 mb-3">
                    <div className="d-flex justify-content-center align-items-center">
                        <i className="bi bi-info-circle me-2"></i>
                        {hasVoted 
                            ? 'Спасибо за участие в голосовании! Результаты обновляются в реальном времени.'
                            : 'Выберите игрока и нажмите "Голосовать". Каждый пользователь может голосовать только один раз.'
                        }
                    </div>
                </div>
                
                {/* Кнопка сброса (для демонстрации) */}
                <div className="text-center">
                    <button
                        onClick={resetVotes}
                        className="btn btn-outline-danger btn-sm"
                        title="Только для тестирования"
                    >
                        <i className="bi bi-arrow-clockwise me-1"></i>
                        Сбросить голосование (тест)
                    </button>
                </div>
            </div>
            
            <div className="card-footer border-gray-800 text-center">
                <div className="row">
                    <div className="col-md-4">
                        <small className="text-gray-400">Финал кубка LFK</small>
                    </div>
                    <div className="col-md-4">
                        <small className="text-gray-400">24.05.2024 • Стадион "Кубань"</small>
                    </div>
                    <div className="col-md-4">
                        <small className="text-gray-400">No MERCY 3:1 Соперник</small>
                    </div>
                </div>
            </div>
        </div>
    );
}

// Рендеринг компонента
const domContainer = document.getElementById('top-scorer-container');
if (domContainer) {
    const root = ReactDOM.createRoot(domContainer);
    root.render(<FinalMVP />);
}