<?php
requireRole([ROLE_ADMIN, ROLE_MODERATOR]);

$db = getDB();

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Добавление новости
    if ($action === 'add_news') {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', transliterate($_POST['title'])));
        
        $stmt = $db->prepare("
            INSERT INTO news (title, slug, excerpt, content, image_url, author_id, published_at, is_published)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['title'],
            $slug,
            $_POST['excerpt'],
            $_POST['content'],
            $_POST['image_url'] ?: null,
            $_SESSION['user_id'],
            $_POST['published_at'],
            isset($_POST['is_published']) ? 1 : 0
        ]);
        
        logActivity('create', 'news', $db->lastInsertId(), "Добавлена новость: {$_POST['title']}");
        setFlash('success', 'Новость успешно добавлена');
        redirect('?page=moderator');
    }
    
    // Удаление новости
    if ($action === 'delete_news' && isset($_POST['news_id'])) {
        $stmt = $db->prepare("SELECT title FROM news WHERE id = ?");
        $stmt->execute([$_POST['news_id']]);
        $news = $stmt->fetch();
        
        $stmt = $db->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$_POST['news_id']]);
        
        logActivity('delete', 'news', $_POST['news_id'], "Удалена новость: {$news['title']}");
        setFlash('success', 'Новость удалена');
        redirect('?page=moderator');
    }
    
    // Обновление новости
    if ($action === 'update_news' && isset($_POST['news_id'])) {
        $stmt = $db->prepare("
            UPDATE news SET 
                title = ?, excerpt = ?, content = ?, 
                image_url = ?, published_at = ?, is_published = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['title'],
            $_POST['excerpt'],
            $_POST['content'],
            $_POST['image_url'] ?: null,
            $_POST['published_at'],
            isset($_POST['is_published']) ? 1 : 0,
            $_POST['news_id']
        ]);
        
        logActivity('update', 'news', $_POST['news_id'], "Обновлена новость: {$_POST['title']}");
        setFlash('success', 'Новость обновлена');
        redirect('?page=moderator');
    }
}

// Получаем новости
$news = $db->query("
    SELECT n.*, u.full_name as author_name 
    FROM news n 
    LEFT JOIN users u ON n.author_id = u.id 
    ORDER BY n.published_at DESC
")->fetchAll();

// Редактирование новости
$editNews = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editNews = $stmt->fetch();
}

// Функция транслитерации для slug
function transliterate($text) {
    $rus = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'];
    $lat = ['a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','y','','e','yu','ya'];
    return str_replace($rus, $lat, mb_strtolower($text));
}
?>

<section class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-newspaper me-2"></i>Панель модератора
            </h1>
            <span class="badge bg-warning text-dark fs-6">
                <i class="bi bi-person-badge me-1"></i>
                <?= e(getCurrentUser()['full_name']) ?>
            </span>
        </div>
        
        <?php showFlash(); ?>
        
        <div class="row">
            <!-- Форма добавления/редактирования новости -->
            <div class="col-lg-5 mb-4">
                <div class="card bg-dark border-gray-800">
                    <div class="card-header border-gray-800">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-plus-circle me-2"></i>
                            <?= $editNews ? 'Редактировать новость' : 'Добавить новость' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="<?= $editNews ? 'update_news' : 'add_news' ?>">
                            <?php if ($editNews): ?>
                            <input type="hidden" name="news_id" value="<?= $editNews['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Заголовок *</label>
                                <input type="text" name="title" class="form-control bg-black border-gray-800 text-white" 
                                       value="<?= e($editNews['title'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Краткое описание *</label>
                                <textarea name="excerpt" class="form-control bg-black border-gray-800 text-white" 
                                          rows="2" required><?= e($editNews['excerpt'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Полный текст *</label>
                                <textarea name="content" class="form-control bg-black border-gray-800 text-white" 
                                          rows="8" required><?= e($editNews['content'] ?? '') ?></textarea>
                                <div class="form-text">Поддерживается HTML-разметка</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Изображение (URL)</label>
                                    <input type="text" name="image_url" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= e($editNews['image_url'] ?? '') ?>"
                                           placeholder="assets/img/news/...">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Дата публикации *</label>
                                    <input type="date" name="published_at" class="form-control bg-black border-gray-800 text-white" 
                                           value="<?= e($editNews['published_at'] ?? date('Y-m-d')) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_published" class="form-check-input" id="is_published"
                                           <?= ($editNews['is_published'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_published">Опубликовать</label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>
                                    <?= $editNews ? 'Сохранить изменения' : 'Добавить новость' ?>
                                </button>
                                <?php if ($editNews): ?>
                                <a href="?page=moderator" class="btn btn-outline-secondary">Отмена</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Список новостей -->
            <div class="col-lg-7">
                <div class="card bg-dark border-gray-800">
                    <div class="card-header border-gray-800 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-list-ul me-2"></i>Все новости
                        </h5>
                        <span class="badge bg-primary"><?= count($news) ?> новостей</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Заголовок</th>
                                        <th>Автор</th>
                                        <th>Дата</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($news as $item): ?>
                                    <tr>
                                        <td><?= $item['id'] ?></td>
                                        <td>
                                            <strong><?= e(mb_substr($item['title'], 0, 40)) ?><?= mb_strlen($item['title']) > 40 ? '...' : '' ?></strong>
                                            <?php if ($item['image_url']): ?>
                                            <br><small class="text-gray-400"><i class="bi bi-image"></i> Есть фото</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= e($item['author_name'] ?? 'Неизвестно') ?></small>
                                        </td>
                                        <td>
                                            <small><?= date('d.m.Y', strtotime($item['published_at'])) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($item['is_published']): ?>
                                            <span class="badge bg-success">Опубликовано</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Черновик</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?page=news&id=<?= $item['id'] ?>" 
                                                   class="btn btn-outline-info" title="Просмотр" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="?page=moderator&edit=<?= $item['id'] ?>" 
                                                   class="btn btn-outline-primary" title="Редактировать">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Удалить эту новость?')">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="delete_news">
                                                    <input type="hidden" name="news_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger" title="Удалить">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>