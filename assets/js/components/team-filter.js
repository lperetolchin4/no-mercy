export function initTeamFilter() {
    const players = [
        { id: 1, name: 'Перетолчин Леонид', position: 'полузащитник', number: 8, image: 'assets/img/players/player1.jpg' },
        { id: 2, name: 'Ахмедов Рустам', position: 'защитник', number: 73, image: 'assets/img/players/player2.jpg' },
        { id: 3, name: 'Тарабрин Александр', position: 'защитник', number: 5, image: 'assets/img/players/player3.jpg' },
        { id: 4, name: 'Шереметьев Роман', position: 'нападающий', number: 11, image: 'assets/img/players/player5.jpg' },
        { id: 5, name: 'Маранов Евгений', position: 'защитник', number: 4, image: 'assets/img/players/player4.jpg' },
        { id: 6, name: 'Чесноков Сергей', position: 'полузащитник', number: 6, image: 'assets/img/players/player6.jpg' },
        { id: 7, name: 'Абидов Надир', position: 'вратарь', number: 1, image: 'assets/img/players/player7.jpg' },
        { id: 8, name: 'Цехомский Артем', position: 'полузащитник', number: 11, image: 'assets/img/players/player8.jpg' },
        { id: 9, name: 'Перетокин Эд', position: 'нападающий', number: 14, image: 'assets/img/players/player9.jpg' },
        { id: 10, name: 'Аббасов Решад', position: 'полузащитник', number: 15, image: 'assets/img/players/player10.jpg' },
        { id: 11, name: 'Орлов Сергей', position: 'полузащитник', number: 7, image: 'assets/img/players/player11.jpg' },
        { id: 12, name: 'Умар Ибрагим', position: 'защитник', number: 2, image: 'assets/img/players/player12.jpg' },
        // Добавьте больше игроков
    ];

    const filterButtons = document.querySelectorAll('.filter-btn');
    const playersContainer = document.getElementById('players-container');

    function renderPlayers(filter = 'all') {
        playersContainer.innerHTML = '';
        
        const filtered = filter === 'all' 
            ? players 
            : players.filter(p => p.position === filter);
        
        filtered.forEach(player => {
            const col = document.createElement('div');
            col.className = 'col-md-3 col-sm-6 mb-4';
            col.innerHTML = `
                <div class="player-card h-100" data-bs-toggle="modal" data-bs-target="#playerModal" data-id="${player.id}">
                    <img src="${player.image}" alt="${player.name}" class="player-img">
                    <div class="p-3">
                        <h5 class="fw-bold">${player.name}</h5>
                        <p class="text-gray-400 mb-1">№ ${player.number}</p>
                        <span class="badge bg-dark">${player.position}</span>
                    </div>
                </div>
            `;
            playersContainer.appendChild(col);
        });
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            renderPlayers(btn.dataset.filter);
        });
    });

    // Модальное окно с деталями
    const modal = document.getElementById('playerModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const playerId = parseInt(button.dataset.id);
            const player = players.find(p => p.id === playerId);
            
            if (player) {
                this.querySelector('.modal-title').textContent = player.name;
                this.querySelector('.modal-body').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <img src="${player.image}" class="img-fluid rounded" alt="${player.name}">
                        </div>
                        <div class="col-md-6">
                            <h5>Статистика сезона</h5>
                            <ul class="list-unstyled">
                                <li>Матчи: 24</li>
                                <li>Голы: ${player.position === 'нападающий' ? '15' : player.position === 'полузащитник' ? '5' : '0'}</li>
                                <li>Голевые передачи: 7</li>
                                <li>Желтые карточки: 2</li>
                            </ul>
                        </div>
                    </div>
                `;
            }
        });
    }

    renderPlayers();
}