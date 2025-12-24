-- Установка кодировки
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- Создание пользователя
CREATE USER IF NOT EXISTS 'nomercity_user'@'%' IDENTIFIED BY 'nomercity_pass';
GRANT ALL PRIVILEGES ON nomercity_db.* TO 'nomercity_user'@'%';
FLUSH PRIVILEGES;

USE nomercity_db;

-- Остальной код...

-- Создание пользователя
CREATE USER IF NOT EXISTS 'nomercity_user'@'%' IDENTIFIED BY 'nomercity_pass';
GRANT ALL PRIVILEGES ON nomercity_db.* TO 'nomercity_user'@'%';
FLUSH PRIVILEGES;

USE nomercity_db;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    full_name VARCHAR(100),
    avatar VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица игроков (VARCHAR вместо ENUM)
CREATE TABLE IF NOT EXISTS players (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50) NOT NULL,
    number INT NOT NULL,
    image VARCHAR(255) DEFAULT 'assets/img/players/default.jpg',
    birth_date DATE,
    nationality VARCHAR(50) DEFAULT 'Россия',
    height INT,
    weight INT,
    bio TEXT,
    matches_played INT DEFAULT 0,
    goals INT DEFAULT 0,
    assists INT DEFAULT 0,
    yellow_cards INT DEFAULT 0,
    red_cards INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица новостей
CREATE TABLE IF NOT EXISTS news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE,
    excerpt TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    image_url VARCHAR(300),
    author_id INT,
    is_published BOOLEAN DEFAULT TRUE,
    views INT DEFAULT 0,
    published_at DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица контактов
CREATE TABLE IF NOT EXISTS contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица голосов за MVP
CREATE TABLE IF NOT EXISTS player_votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    player_name VARCHAR(100) NOT NULL,
    votes INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица логов действий
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- НАЧАЛЬНЫЕ ДАННЫЕ
-- =====================================================

-- Пользователи (пароль: password)
INSERT INTO users (username, email, password, role, full_name) VALUES
('admin', 'admin@nomercity.fc.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Администратор'),
('moderator', 'moder@nomercity.fc.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'moderator', 'Модератор Новостей'),
('user', 'user@nomercity.fc.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Обычный Пользователь');

-- Игроки
INSERT INTO players (name, position, number, image, matches_played, goals, assists, yellow_cards) VALUES
('Абидов Надир', 'вратарь', 1, 'assets/img/players/player7.jpg', 24, 0, 0, 1),
('Умар Ибрагим', 'защитник', 2, 'assets/img/players/player12.jpg', 22, 0, 2, 3),
('Маранов Евгений', 'защитник', 4, 'assets/img/players/player4.jpg', 23, 1, 1, 4),
('Тарабрин Александр', 'защитник', 5, 'assets/img/players/player3.jpg', 20, 0, 3, 2),
('Чесноков Сергей', 'полузащитник', 6, 'assets/img/players/player6.jpg', 24, 3, 5, 2),
('Орлов Сергей', 'полузащитник', 7, 'assets/img/players/player11.jpg', 24, 3, 7, 1),
('Перетолчин Леонид', 'полузащитник', 8, 'assets/img/players/player1.jpg', 24, 4, 5, 2),
('Шереметьев Роман', 'нападающий', 11, 'assets/img/players/player5.jpg', 24, 12, 1, 3),
('Цехомский Артем', 'полузащитник', 10, 'assets/img/players/player8.jpg', 18, 2, 4, 1),
('Перетокин Эд', 'нападающий', 14, 'assets/img/players/player9.jpg', 15, 5, 2, 0),
('Аббасов Решад', 'полузащитник', 15, 'assets/img/players/player10.jpg', 20, 2, 3, 2),
('Ахмедов Рустам', 'защитник', 73, 'assets/img/players/player2.jpg', 22, 1, 2, 5);

-- Новости
INSERT INTO news (title, slug, excerpt, content, image_url, author_id, published_at) VALUES
('«No MERCY» одержал крупную победу в дерби', 'derby-victory', 
 'Со счетом 3:0 наша команда разгромила принципиального соперника.',
 '<p>В матче 15-го тура Премьер-Лиги наш клуб показал блестящую игру.</p>',
 'assets/img/news/match1.jpg', 2, '2024-05-15'),

('Наш стадион получил сертификацию', 'stadium-certification',
 'Наша арена готова принимать матчи.',
 '<p>Стадион "Мерси Арена" успешно прошел инспекцию РФС.</p>',
 'assets/img/news/stadium.jpg', 2, '2024-05-10'),

('Подписан контракт с молодым вингером', 'new-transfer',
 'Клуб объявил о трансфере 19-летнего таланта.',
 '<p>Футбольный клуб "No MERCY" объявляет о подписании контракта.</p>',
 'assets/img/news/transfer.jpg', 2, '2024-05-05'),

('No MERCY - чемпион кубка LFK!', 'lfk-champions',
 'Наша команда впервые становится чемпионом!',
 '<p>Футбольный клуб "No MERCY" выигрывает 2-1 у "Маяка"!</p>',
 'assets/img/champ.jpg', 2, '2024-05-20');

-- Голоса за MVP
INSERT INTO player_votes (player_name, votes) VALUES
('Орлов Сергей', 42),
('Шереметьев Роман', 35),
('Перетолчин Леонид', 28);