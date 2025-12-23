<?php
require_once 'includes/db_connect.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Просмотр сообщений</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .message-card { transition: transform 0.2s; }
        .message-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-envelope"></i> Сообщения от пользователей</h3>
            </div>
            <div class="card-body">';

try {
    // Получаем все сообщения
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($messages)) {
        echo '<div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Сообщений пока нет
              </div>';
    } else {
        echo '<p class="text-muted">Всего сообщений: <strong>' . count($messages) . '</strong></p>';
        
        foreach ($messages as $message) {
            $date = date('d.m.Y H:i', strtotime($message['created_at']));
            
            echo '<div class="card message-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="card-title mb-1">' . htmlspecialchars($message['subject']) . '</h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    От: <strong>' . htmlspecialchars($message['name']) . '</strong> 
                                    (' . htmlspecialchars($message['email']) . ')
                                </h6>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-secondary">' . $date . '</span><br>
                                <small class="text-muted">ID: ' . $message['id'] . '</small>
                            </div>
                        </div>
                        <p class="card-text">' . nl2br(htmlspecialchars($message['message'])) . '</p>
                    </div>
                </div>';
        }
    }
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">
            <h5>Ошибка загрузки сообщений</h5>
            <p>' . $e->getMessage() . '</p>
            <p>Проверьте подключение к базе данных</p>
          </div>';
}

echo '</div>
        <div class="card-footer text-center">
            <a href="?page=contact" class="btn btn-primary">Назад к форме</a>
            <a href="?page=home" class="btn btn-secondary">На главную</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
?>