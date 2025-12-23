<?php
/**
 * Конфигурация базы данных SQLite для футбольного клуба "No MERCY"
 * Не требует MySQL - идеально для демонстрации
 */

// Основная функция для подключения к БД
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            // Путь к файлу базы данных SQLite
            $dbPath = __DIR__ . '/../data/football_club.sqlite';
            
            // Подключаемся к базе
            $db = new PDO('sqlite:' . $dbPath);
            
            // Настройки для отображения ошибок
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Включаем поддержку внешних ключей
            $db->exec('PRAGMA foreign_keys = ON');
            
            // Создаем таблицы при первом подключении
            createDatabaseTables($db);
            
        } catch (PDOException $e) {
            // В режиме разработки показываем ошибку
            die('<div style="background: #f00; color: white; padding: 20px; font-family: monospace;">
                <h2>Ошибка базы данных</h2>
                <p><strong>Сообщение:</strong> ' . $e->getMessage() . '</p>
                <p><strong>Путь к БД:</strong> ' . $dbPath . '</p>
                <p>Проверьте права на запись в папку data/</p>
            </div>');
        }
    }
    
    return $db;
}

// Функция создания таблиц
function createDatabaseTables($db) {
    // Таблица контактов (для формы обратной связи)
    $db->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        subject TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Таблица новостей
    $db->exec("CREATE TABLE IF NOT EXISTS news (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        excerpt TEXT NOT NULL,
        content TEXT NOT NULL,
        image_url TEXT,
        author TEXT,
        published_at DATE NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Таблица голосов (для React-компонента) - ОБНОВЛЕНА
    $db->exec("CREATE TABLE IF NOT EXISTS player_votes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        player_name TEXT UNIQUE NOT NULL,
        votes INTEGER DEFAULT 0,
        last_updated DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Добавляем тестовые данные, если таблицы пустые
    addInitialData($db);
}

// Функция добавления начальных данных
function addInitialData($db) {
    // Проверяем, есть ли новости
    $stmt = $db->query("SELECT COUNT(*) as count FROM news");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        // Добавляем тестовые новости
        $newsData = [
            [
                'title' => '«No MERCY» одержал крупную победу в дерби',
                'excerpt' => 'Со счетом 3:0 наша команда разгромила принципиального соперника.',
                'content' => '<p>В матче 15-го тура Премьер-Лиги наш клуб показал блестящую игру. Уже на 10-й минуте Шереметьев Роман открыл счет, а к перерыву Орлов Сергей удвоил преимущество. Во втором тайме команда контролировала ход игры и на 78-й минуте Перетолчин Леонид поставил победную точку.</p>
                <h3>Ключевые моменты матча</h3>
                <ul>
                    <li>10-я минута: Гол Шереметьева Романа после передачи Цехомского Артема</li>
                    <li>38-я минута: Орлов Сергей удваивает преимущество</li>
                    <li>78-я минута: Перетолчин Леонид забивает третий гол</li>
                    <li>87-я минута: Абидов Надир отражает пенальти</li>
                </ul>
                <p>Тренер Александр Петров: "Мы готовились к этому матчу особым образом. Видно, что игроки понимали важность момента. Благодарю парней за самоотдачу."</p>',
                'image_url' => 'assets/img/news/match1.jpg',
                'author' => 'Меркулов Евгений',
                'published_at' => '2024-05-15'
            ],
            [
                'title' => 'Наш стадион получил разрешение на проведение домашних матчей.',
                'excerpt' => 'Наша арена готова принимать матчи.',
                'content' => '<p>Стадион "Мерси Арена" успешно прошел инспекцию РФС и получил разрешение на проведение матчей. Это означает, что наша команда наконец-то официально обрела домашнюю арену.</p>
                <h3>Основные улучшения стадиона:</h3>
                <ul>
                    <li><strong>Появились сидячие места</strong> количество 150 мест</li>
                    <li><strong>Обновление искуственного покрытия</strong> с компьютерным управлением</li>
                    <li><strong>Улучшенная система безопасности</strong> с камерами наблюдения</li>
                    <li><strong>Реконструкция раздевалок</strong> появились душевые кабинки</li>
                    <li><strong>Улучшенное освещение</strong> по стандартам РФС</li>
                </ul>
                <p>Реконструкция продолжалась 2 года и обошлась в 500 тыс. рублей. Президент клуба отметил, что это инвестиция в будущее команды.</p>',
                'image_url' => 'assets/img/news/stadium.jpg',
                'author' => 'Мария Петрова',
                'published_at' => '2024-05-10'
            ],
            [
                'title' => 'Подписан контракт с молодым вингером',
                'excerpt' => 'Клуб объявил о трансфере 19-летнего таланта.',
                'content' => '<p>Футбольный клуб "No MERCY" объявляет о подписании 5-летнего контракта с полузащитником Бедикяном Ашотом.</p>
                
                <h3>Карьера игрока:</h3>
                <table class="table">
                    <tr>
                        <th>Клуб</th>
                        <th>Период</th>
                        <th>Матчи</th>
                        <th>Голы</th>
                    </tr>
                    <tr>
                        <td>Колос (мол.)</td>
                        <td>2018-2020</td>
                        <td>45</td>
                        <td>18</td>
                    </tr>
                    <tr>
                        <td>КубГУ-2</td>
                        <td>2020-2024</td>
                        <td>120</td>
                        <td>32</td>
                    </tr>
                    <tr>
                        <td>Студенческая сборная до 21 года</td>
                        <td>2022-2024</td>
                        <td>15</td>
                        <td>5</td>
                    </tr>
                </table>
                
                <p>Бедикян Ашот: "Я очень рад присоединиться к такому амбициозному проекту. "No MERCY" - клуб с большой историей и блестящим будущим. Не могу дождаться первого матча!"</p>
                
                <p>Спортивный директор клуба отметил, что Ашот - игрок, который идеально впишется в тактическую схему команды. "Он быстрый, техничный и обладает отличным видением поля" - добавил он.</p>',
                'image_url' => 'assets/img/news/transfer.jpg',
                'author' => 'Иван Сидоров',
                'published_at' => '2024-05-05'
            ],
            [
                'title' => 'No MERCY - чемпион кубка LFK!',
                'excerpt' => 'Наша команда впервые становится чемпионом!',
                'content' => '<p>Футбольный клуб "No MERCY" выигрывает 2-1 "Маяк" и забирает золотые медали кубка LFK.</p>
                
                <p>В очень напряженном финале первый тайм завершился боевой ничьей 1-1, в котором голом отличился наш полузащитник Чесноков Сергей. Во втором тайме победу принес Орлов Сергей красивым ударом из-за штрафной, 2-1!</p>
                
                <p>Капитан команды Перетолчин Леонид после игры отметил, что команда была настроена на максимум: "Парни готовы были отдать все силы и эмоции на поле, поэтому нам удалось выиграть.</p>',
                'image_url' => 'assets/img/champ.jpg',
                'author' => 'Меркулов Евгений',
                'published_at' => '2024-05-05'
            ]
        ];
        
        $stmt = $db->prepare("
            INSERT INTO news (title, excerpt, content, image_url, author, published_at) 
            VALUES (:title, :excerpt, :content, :image_url, :author, :published_at)
        ");
        
        foreach ($newsData as $news) {
            $stmt->execute($news);
        }
    }
    
    // Добавляем начальные голоса для игроков финала LFK - ОБНОВЛЕНО
    $stmt = $db->query("SELECT COUNT(*) as count FROM player_votes");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        // Вставка данных о голосовании за лучшего игрока финала LFK
        $players = [
            ['player_name' => 'Орлов Сергей', 'votes' => 42],
            ['player_name' => 'Шереметьев Роман', 'votes' => 35],
            ['player_name' => 'Перетолчин Леонид', 'votes' => 28]
        ];
        
        $stmt = $db->prepare("
            INSERT INTO player_votes (player_name, votes) 
            VALUES (:player_name, :votes)
        ");
        
        foreach ($players as $player) {
            $stmt->execute($player);
        }
        
        // Альтернативный вариант с одним запросом:
        // $db->exec("
        //     INSERT INTO player_votes (player_name, votes) VALUES 
        //     ('Орлов Сергей', 42),
        //     ('Шереметьев Роман', 35),
        //     ('Перетолчин Леонид', 28)
        // ");
    }
}

// Функция для добавления голоса (для API)
function addVoteForPlayer($playerName) {
    $db = getDB();
    
    $stmt = $db->prepare("
        INSERT INTO player_votes (player_name, votes) 
        VALUES (:name, 1)
        ON CONFLICT(player_name) DO UPDATE SET 
        votes = votes + 1, 
        last_updated = CURRENT_TIMESTAMP
    ");
    
    return $stmt->execute([':name' => $playerName]);
}

// Функция для получения голосов игрока
function getPlayerVotes($playerName) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT votes FROM player_votes WHERE player_name = :name");
    $stmt->execute([':name' => $playerName]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['votes'] : 0;
}

// Функция для получения всех голосов
function getAllVotes() {
    $db = getDB();
    
    $stmt = $db->query("SELECT player_name, votes FROM player_votes ORDER BY votes DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для получения статистики голосования
function getVotingStats() {
    $db = getDB();
    
    $stats = [];
    
    // Общее количество голосов
    $stmt = $db->query("SELECT SUM(votes) as total_votes FROM player_votes");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_votes'] = $result['total_votes'] ?? 0;
    
    // Лидер голосования
    $stmt = $db->query("SELECT player_name, votes FROM player_votes ORDER BY votes DESC LIMIT 1");
    $leader = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['leader'] = $leader['player_name'] ?? 'Нет данных';
    $stats['leader_votes'] = $leader['votes'] ?? 0;
    
    // Все игроки с процентами
    $players = getAllVotes();
    $stats['players'] = [];
    
    foreach ($players as $player) {
        $percentage = $stats['total_votes'] > 0 
            ? round(($player['votes'] / $stats['total_votes']) * 100, 1)
            : 0;
        
        $stats['players'][] = [
            'name' => $player['player_name'],
            'votes' => $player['votes'],
            'percentage' => $percentage
        ];
    }
    
    return $stats;
}

// Функция сброса голосования (для админки)
function resetVoting() {
    $db = getDB();
    
    try {
        $db->exec("DELETE FROM player_votes");
        
        // Вставляем начальные значения
        $players = [
            ['player_name' => 'Орлов Сергей', 'votes' => 0],
            ['player_name' => 'Шереметьев Роман', 'votes' => 0],
            ['player_name' => 'Перетолчин Леонид', 'votes' => 0]
        ];
        
        $stmt = $db->prepare("
            INSERT INTO player_votes (player_name, votes) 
            VALUES (:player_name, :votes)
        ");
        
        foreach ($players as $player) {
            $stmt->execute($player);
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Ошибка сброса голосования: " . $e->getMessage());
        return false;
    }
}

// Функция для тестирования базы данных
function testDatabaseConnection() {
    try {
        $db = getDB();
        
        // Проверяем таблицу новостей
        $stmt = $db->query("SELECT COUNT(*) as news_count FROM news");
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Проверяем таблицу голосов
        $stmt = $db->query("SELECT COUNT(*) as votes_count FROM player_votes");
        $votes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Проверяем таблицу контактов
        $stmt = $db->query("SELECT COUNT(*) as contacts_count FROM contacts");
        $contacts = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'tables' => [
                'news' => $news['news_count'],
                'player_votes' => $votes['votes_count'],
                'contacts' => $contacts['contacts_count']
            ],
            'voting_players' => getAllVotes()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Функция для экспорта данных голосования в JSON (для API)
function exportVotingData() {
    $stats = getVotingStats();
    
    return json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'total_votes' => $stats['total_votes'],
        'leader' => [
            'name' => $stats['leader'],
            'votes' => $stats['leader_votes']
        ],
        'players' => $stats['players']
    ], JSON_PRETTY_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// Функция для импорта данных голосования из JSON
function importVotingData($jsonData) {
    try {
        $data = json_decode($jsonData, true);
        
        if (!$data || !isset($data['players'])) {
            throw new Exception("Неверный формат JSON данных");
        }
        
        $db = getDB();
        
        // Очищаем таблицу
        $db->exec("DELETE FROM player_votes");
        
        // Вставляем новые данные
        $stmt = $db->prepare("
            INSERT INTO player_votes (player_name, votes) 
            VALUES (:name, :votes)
        ");
        
        foreach ($data['players'] as $player) {
            $stmt->execute([
                ':name' => $player['name'],
                ':votes' => $player['votes']
            ]);
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Ошибка импорта голосования: " . $e->getMessage());
        return false;
    }
}

// Автоматическое создание базы при первом запуске
register_shutdown_function(function() {
    // Эта функция гарантирует, что база будет создана при любом сценарии
    try {
        getDB();
    } catch (Exception $e) {
        // Молча игнорируем ошибки при завершении работы
    }
});
?>