<header class="sticky-top bg-black border-bottom border-gray-800">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="/">
                <img src="assets/img/logo.png" alt="Логотип No MERCY" height="40" class="me-2"> No MERCY
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>" href="?page=home">Главная</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'about') ? 'active' : ''; ?>" href="?page=about">О клубе</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'team') ? 'active' : ''; ?>" href="?page=team">Команда</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'schedule') ? 'active' : ''; ?>" href="?page=schedule">Календарь</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'gallery') ? 'active' : ''; ?>" href="?page=gallery">Галерея</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'news') ? 'active' : ''; ?>" href="?page=news">Новости</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($page == 'contact') ? 'active' : ''; ?>" href="?page=contact">Контакты</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>