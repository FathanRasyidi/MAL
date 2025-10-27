<?php
session_start();
$op = "";

if (isset($_GET['pesan'])) {
    $op = $_GET['pesan'];
    if ($op == 'logout') {
        echo "<script>alert('Anda telah berhasil logout');</script>";
        header("refresh:0;url=login.php");
    } elseif ($op == 'gagal') {
        echo "<script>alert('Login gagal! Email dan Password salah!');</script>";
        header("refresh:0;url=login.php");
    } elseif ($op == 'belum_login') {
        echo "<script>alert('Anda harus login terlebih dahulu');</script>";
        header("refresh:0;url=login.php");
    } elseif ($op == 'kosong') {
        echo "<script>alert('Email dan Password harus diisi');</script>";
        header("refresh:0;url=login.php");
    }
}

if (isset($_COOKIE['user'])) {
    $_SESSION['login'] = $_COOKIE['user'];
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - MyAnimeList</title>
    <link rel="icon" href="../assets/images/sr.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="antialiased bg-gradient-to-br from-blue-100 to-white">
    <div class="container px-8 mx-auto">
        <div class="flex flex-col text-center md:text-left md:flex-row h-screen justify-evenly md:items-center">
            <div class="flex flex-col m-8 sm:mr-32">
                <a class="navbar-brand text-gray-600 flex items-center">
                    <img src="../assets/images/sr.png" alt="" width="90" class="d-inline-block mr-3" id="logo">
                    <span class="ml-4">
                        <h1 class="text-5xl text-gray-900 font-bold">MyAnimeList</h1>
                        <p class="w-9/12 mx-auto md:mx-0 text-gray-500">
                        Your Anime Collection
                        </p>
                    </span>
                </a>
            </div>
            <div class="w-full md:w-full lg:w-4/12 mx-auto md:mx-0">
                <div class="bg-white p-8 pb-4 flex flex-col w-full shadow-xl rounded-xl">
                    <h2 class="text-2xl font-bold text-gray-800 text-left mb-5">
                        LOGIN
                    </h2>
                    <form action="/mal/app/cek_login.php" method="POST" class="w-full">
                        <div id="input" class="flex flex-col w-full my-5 mt-0">
                            <label for="email" class="text-gray-500 mb-2">Email</label>
                            <input type="text" id="email" name="email"
                                class="appearance-none border-2 border-gray-100 rounded-lg px-4 py-3 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-green-600 focus:shadow-lg" />
                        </div>
                        <div id="input" class="flex flex-col w-full my-5">
                            <label for="password" class="text-gray-500 mb-2">Password</label>
                            <input type="password" id="password" name="password"
                                class="appearance-none border-2 border-gray-100 rounded-lg px-4 py-3 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-green-600 focus:shadow-lg" />
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="kuki" name="kuki">
                            <label class="form-check-label" for="kuki" style="color: #2563eb">Remember me</label>
                        </div>
                        <div id="button" class="flex flex-col w-full my-5">
                            <button type="submit" class="w-full py-4 bg-blue-600 rounded-lg text-white hover:bg-blue-700">
                                <div class="flex flex-row items-center justify-center">
                                    <div class="mr-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="font-bold">Login</div>
                                </div>
                            </button>
                        </div>
                        <div class="text-center mt-4">
                            <p class="text-gray-600">Don't have an account? 
                                <a href="register.php" class="text-blue-600 hover:text-blue-700 font-semibold">Register here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>