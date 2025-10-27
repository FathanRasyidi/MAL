<?php
function ensure_logged_in(string $redirectPath = 'login.php'): void
{
    if (empty($_COOKIE['email']) && !isset($_SESSION['login'])) {
        $target = $redirectPath !== '' ? $redirectPath : 'login.php';
        $separator = strpos($target, '?') !== false ? '&' : '?';
        header('location:' . $target . $separator . 'pesan=belum_login');
        exit();
    }
}

function ensure_admin(string $loginRedirect = 'login.php', string $homeRedirect = 'home.php'): void
{
    ensure_logged_in($loginRedirect);
    if (empty($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
        header('location:' . $homeRedirect);
        exit();
    }
}

function logout_user(): void
{
    setcookie('email', '', time() - 3600, '/');
    setcookie('user', '', time() - 3600, '/');
    session_unset();
    session_destroy();
}
