<?php
// Подключаем конфигурацию (включает сессию и БД)
require_once __DIR__ . '/config/config.php';

// Определяем страницу
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Разрешенные страницы
$allowed_pages = [
    'home', 'about', 'team', 'schedule', 'gallery', 'news', 'contact',
    'login', 'logout', 'register', 'profile',
    'admin', 'moderator'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Для страницы отдельной новости
if (isset($_GET['id']) && $page == 'news') {
    $page = 'news_single';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Футбольный клуб «No MERCY»</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/custom-bootstrap.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="assets/img/logo.png">
</head>
<body class="bg-black text-white">
    <?php include 'includes/header.php'; ?>

    <main class="container-fluid px-0">
        <?php
        $page_file = 'pages/' . $page . '.php';
        if (file_exists($page_file)) {
            include $page_file;
        } else {
            include 'pages/home.php';
        }
        ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script type="module" src="assets/js/main.js"></script>
</body>
</html>