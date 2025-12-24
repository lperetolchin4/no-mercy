<?php
require_once __DIR__ . '/../includes/auth.php';
logout();
setFlash('success', 'Вы успешно вышли из системы');
redirect('?page=home');
?>