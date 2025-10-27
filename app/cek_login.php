<?php
include 'db.php';
session_start();


$email = $_POST['email'];
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    header("location:/mal/public/login.php?pesan=kosong");
    exit();
}

$data = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email' AND password = '$password'");
$cek = mysqli_num_rows($data);

if ($cek > 0) {
    $row = mysqli_fetch_assoc($data);
    $login = $row['name'];
    $type = $row['role'];
    $_SESSION['id_user'] = $row['id'];
    $_SESSION['login'] = $login;
    $_SESSION['usertype'] = $type;
    $_SESSION['email'] = $email;
    if (isset($_POST['kuki'])) {
        setcookie('email', $email, time() + 60 * 60 * 24);
    }
    header("location:/mal/public/home.php");
    exit();
} else {
    header("location:/mal/public/login.php?pesan=gagal");
}
?>