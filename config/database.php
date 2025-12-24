<?php
/**
 * Конфигурация базы данных MySQL для Docker
 */

define('DB_HOST', 'mysql');
define('DB_NAME', 'nomercity_db');
define('DB_USER', 'nomercity_user');
define('DB_PASS', 'nomercity_pass');
define('DB_CHARSET', 'utf8mb4');

/**
 * Получение подключения к базе данных
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Устанавливаем кодировку UTF-8
            $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("SET CHARACTER SET utf8mb4");
            
        } catch (PDOException $e) {
            http_response_code(500);
            die('
                <div style="font-family: Arial; max-width: 600px; margin: 50px auto; padding: 30px; background: #1a1a1a; color: #fff; border-radius: 10px; border: 1px solid #333;">
                    <h2 style="color: #ff6b6b;">Ошибка подключения к базе данных</h2>
                    <p><strong>Сообщение:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                </div>
            ');
        }
    }
    
    return $pdo;
}

/**
 * Функции для работы с голосованием
 */
function addVoteForPlayer($playerName) {
    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO player_votes (player_name, votes) 
        VALUES (:name, 1)
        ON DUPLICATE KEY UPDATE votes = votes + 1
    ");
    return $stmt->execute([':name' => $playerName]);
}

function getAllVotes() {
    $db = getDB();
    $stmt = $db->query("SELECT player_name, votes FROM player_votes ORDER BY votes DESC");
    return $stmt->fetchAll();
}

function getVotingStats() {
    $db = getDB();
    
    $stmt = $db->query("SELECT SUM(votes) as total FROM player_votes");
    $total = $stmt->fetch()['total'] ?? 0;
    
    $stmt = $db->query("SELECT player_name, votes FROM player_votes ORDER BY votes DESC LIMIT 1");
    $leader = $stmt->fetch();
    
    return [
        'total_votes' => $total,
        'leader' => $leader['player_name'] ?? 'Нет данных',
        'leader_votes' => $leader['votes'] ?? 0,
        'players' => getAllVotes()
    ];
}