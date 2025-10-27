<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = $_POST['password'];
    
    // Cek apakah email sudah ada
    $cek = mysqli_query($connect, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($cek) > 0) {
        header("location:../public/register.php?pesan=gagal");
        exit();
    }
    
    
    // Insert user baru dengan role 'user'
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')";
    
    if (mysqli_query($connect, $sql)) {
        header("location:../public/register.php?pesan=sukses");
    } else {
        header("location:../public/register.php?pesan=gagal");
    }
    exit();
}
?>