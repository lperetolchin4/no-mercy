-- Создание базы данных
CREATE DATABASE IF NOT EXISTS nomercity_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nomercity_db;

-- Таблица для контактной формы
CREATE TABLE contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица новостей
CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    excerpt TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    image_url VARCHAR(300),
    author VARCHAR(100),
    published_at DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Вставка тестовых новостей
INSERT INTO news (title, excerpt, content, image_url, author, published_at) VALUES
('«No MERCY» одержал крупную победу в дерби', 'Со счетом 3:0 наша команда разгромила принципиального соперника.', '<p>Подробный отчет о матче...</p>', 'assets/img/news/match1.jpg', 'Алексей Иванов', '2024-05-15'),
('Новый стадион получил сертификат UEFA Category 4', 'Наша арена готова принимать финалы еврокубков.', '<p>Детали реконструкции...</p>', 'assets/img/news/stadium.jpg', 'Мария Петрова', '2024-05-10'),
('Подписан контракт с бразильским вингером', 'Клуб объявил о трансфере 22-летнего таланта.', '<p>Интервью с новичком...</p>', 'assets/img/news/transfer.jpg', 'Иван Сидоров', '2024-05-05');