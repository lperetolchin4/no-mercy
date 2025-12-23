<?php
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    
    // Валидация
    $errors = [];
    if (empty($name)) $errors[] = 'Имя обязательно';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email';
    if (empty($subject)) $errors[] = 'Тема обязательна';
    if (empty($message_text)) $errors[] = 'Сообщение обязательно';
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message_text]);
            
            $message = 'Сообщение успешно отправлено!';
            $message_type = 'success';
            
            // Очистка полей
            $_POST = [];
        } catch (PDOException $e) {
            $message = 'Ошибка при отправке: ' . $e->getMessage();
            $message_type = 'danger';
        }
    } else {
        $message = implode('<br>', $errors);
        $message_type = 'danger';
    }
}
?>

<section class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-5">Контакты</h1>
        
        <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h3 class="fw-bold mb-4">Наш стадион</h3>
                <div class="ratio ratio-16x9 mb-4">
                   <iframe 
                        src="https://www.openstreetmap.org/export/embed.html?bbox=38.980%2C45.065%2C39.020%2C45.085&layer=mapnik&marker=45.075%2C39.000" 
                        style="border: 1px solid #333" 
                        allowfullscreen
                        title="Расположение офиса клуба в Краснодаре">
                    </iframe>
                </div>
                <p class="text-gray-400">г. Краснодар, ул. Железнодорожная, 49</p>
            </div>
            
            <div class="col-lg-6">
                <h3 class="fw-bold mb-4">Связаться с клубом</h3>
                
                <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя *</label>
                        <input type="text" class="form-control bg-black border-gray-800 text-white" id="name" name="name" 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control bg-black border-gray-800 text-white" id="email" name="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Тема *</label>
                        <input type="text" class="form-control bg-black border-gray-800 text-white" id="subject" name="subject"
                               value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="form-label">Сообщение *</label>
                        <textarea class="form-control bg-black border-gray-800 text-white" id="message" name="message" 
                                  rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary px-5">Отправить</button>
                </form>
            </div>
        </div>
    </div>
</section>