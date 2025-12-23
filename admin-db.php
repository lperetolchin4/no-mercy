<?php
// Админка базы данных для проекта No MERCY FC
require_once 'includes/db_connect.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Админка БД - No MERCY FC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 20px; font-family: Arial; }
        .db-card { border-radius: 10px; margin-bottom: 20px; }
        .table-sm th { background: #343a40; color: white; }
        .sql-query { font-family: "Courier New", monospace; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow db-card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">📊 Администратор базы данных - ФК "No MERCY"</h3>
                <small>SQLite · PDO · Полная безопасность</small>
            </div>
            <div class="card-body">';

// ==================== ИНФОРМАЦИЯ О БАЗЕ ====================
try {
    $dbFile = __DIR__ . '/data/football_club.sqlite';
    $dbSize = file_exists($dbFile) ? round(filesize($dbFile) / 1024, 2) : 0;
    
    echo '<div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5>📁 Файл БД</h5>
                        <p class="mb-1"><strong>' . $dbSize . ' KB</strong></p>
                        <small class="text-muted">football_club.sqlite</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5>🗃️ Таблицы</h5>
                        <p class="mb-1"><strong>2</strong></p>
                        <small class="text-muted">contacts, news</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5>🔒 Безопасность</h5>
                        <p class="mb-1"><strong>PDO</strong></p>
                        <small class="text-muted">Prepared Statements</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5>⚡ Движок</h5>
                        <p class="mb-1"><strong>SQLite 3</strong></p>
                        <small class="text-muted">Версия ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . '</small>
                    </div>
                </div>
            </div>
        </div>';

    // ==================== ТАБЛИЦА CONTACTS ====================
    echo '<h4 class="mb-3">📨 Таблица <code>contacts</code> (сообщения от пользователей)</h4>';
    
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();
    
    if (empty($messages)) {
        echo '<div class="alert alert-info">Нет сообщений. <a href="?page=contact">Отправить тестовое</a></div>';
    } else {
        echo '<div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Тема</th>
                            <th>Сообщение</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($messages as $msg) {
            $date = date('d.m H:i', strtotime($msg['created_at']));
            echo '<tr>
                    <td><span class="badge bg-secondary">' . $msg['id'] . '</span></td>
                    <td><small>' . $date . '</small></td>
                    <td>' . htmlspecialchars($msg['name']) . '</td>
                    <td><code>' . htmlspecialchars($msg['email']) . '</code></td>
                    <td>' . htmlspecialchars($msg['subject']) . '</td>
                    <td><div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'
                        . htmlspecialchars($msg['message']) . '</div></td>
                  </tr>';
        }
        
        echo '</tbody></table></div>';
    }
    
    // ==================== ТАБЛИЦА NEWS ====================
    echo '<hr class="my-4">
          <h4 class="mb-3">📰 Таблица <code>news</code> (новости клуба)</h4>';
    
    $stmt = $pdo->query("SELECT id, title, author, published_at FROM news ORDER BY published_at DESC");
    $news = $stmt->fetchAll();
    
    echo '<div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Заголовок</th><th>Автор</th><th>Дата</th><th>На сайте</th></tr>
                </thead>
                <tbody>';
    
    foreach ($news as $item) {
        echo '<tr>
                <td>' . $item['id'] . '</td>
                <td><strong>' . htmlspecialchars($item['title']) . '</strong></td>
                <td>' . htmlspecialchars($item['author']) . '</td>
                <td>' . $item['published_at'] . '</td>
                <td>
                    <a href="?page=news&id=' . $item['id'] . '" class="btn btn-sm btn-outline-primary" target="_blank">
                        👁️ Просмотр
                    </a>
                </td>
              </tr>';
    }
    
    echo '</tbody></table></div>';
    
    // ==================== СТРУКТУРА БАЗЫ ====================
    echo '<hr class="my-4">
          <h4 class="mb-3">🏗️ Структура базы данных</h4>
          <div class="card">
            <div class="card-body sql-query">';
    
    $tables = $pdo->query("SELECT name, sql FROM sqlite_master WHERE type='table'")->fetchAll();
    foreach ($tables as $table) {
        echo '<h5>Таблица: <code>' . $table['name'] . '</code></h5>
              <pre class="bg-dark text-white p-3 rounded">' . htmlspecialchars($table['sql']) . '</pre>';
    }
    
    echo '</div></div>';
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">
            <h5>❌ Ошибка подключения к БД</h5>
            <p>' . $e->getMessage() . '</p>
            <p>Проверьте файл: <code>data/football_club.sqlite</code></p>
          </div>';
}

// ==================== КНОПКИ ДЕЙСТВИЙ ====================
echo '<hr class="my-4">
      <div class="text-center">
          <a href="?page=contact" class="btn btn-success btn-lg">
              ✉️ Отправить тестовое сообщение
          </a>
          <a href="?page=home" class="btn btn-primary btn-lg">
              🏠 На главную
          </a>
          <button onclick="showDbPath()" class="btn btn-info btn-lg">
              📍 Показать путь к БД
          </button>
      </div>
      
      <div class="alert alert-secondary mt-4">
          <h5>💡 Для защиты проекта:</h5>
          <ol class="mb-0">
              <li>Откройте <a href="?page=contact">форму контактов</a></li>
              <li>Отправьте сообщение "Тест БД"</li>
              <li>Обновите эту страницу - сообщение появится в таблице</li>
              <li>Покажите структуру БД и файл football_club.sqlite</li>
          </ol>
      </div>
            </div>
        </div>
    </div>
    
    <script>
    function showDbPath() {
        alert("Файл базы данных:\\n\\n" +
              "C:\\\\xampp\\\\htdocs\\\\nomercity-fc\\\\data\\\\football_club.sqlite\\n\\n" +
              "Можно открыть в DB Browser for SQLite");
    }
    </script>
</body>
</html>';
?>