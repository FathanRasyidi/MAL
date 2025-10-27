<?php 
session_start();

if (empty($_COOKIE['email']) && !isset($_SESSION['login'])) {
    header("location:/mal/public/login.php");
    exit();
} else {
    header("location:/mal/public/home.php");
    exit();
}
?>