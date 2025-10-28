<?php
// config/add_user.php
// Jalankan sekali untuk membuat akun admin dan user
// Hapus file ini setelah sukses untuk keamanan.
include __DIR__ . '/../app/db.php';

$users = [
    ['name' => 'Admin', 'email' => 'admin@example.com', 'password' => '123', 'role' => 'admin'],
    ['name' => 'Editor', 'email' => 'editor@example.com', 'password' => '123', 'role' => 'editor'],
    ['name' => 'User', 'email' => 'user@example.com', 'password' => '123', 'role' => 'user'],
];

foreach ($users as $user) {
    $name = $connect->real_escape_string($user['name']);
    $email = $connect->real_escape_string($user['email']);
    $password = $connect->real_escape_string($user['password']);
    $role = $connect->real_escape_string($user['role']);

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if ($connect->query($sql) === TRUE) {
        echo "User " . $email . " created successfully.<br>";
    } else {
        echo "Error creating user " . $email . ": " . $connect->error . "<br>";
    }
}

?> 