<?php
session_start();

// Jika sudah register, redirect ke login
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'sukses') {
        echo "<script>alert('Registrasi berhasil! Silakan login.');</script>";
        header("refresh:0;url=login.php");
        exit();
    } elseif ($_GET['pesan'] == 'gagal') {
        echo "<script>alert('Registrasi gagal! Email sudah digunakan.');</script>";
        header("refresh:0;url=register.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MyAnimeList</title>
    <link rel="icon" href="../assets/images/sr.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
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
                        REGISTER
                    </h2>
                    <form action="../app/register.php" method="POST" class="w-full">
                        <div class="flex flex-col w-full my-5 mt-0">
                            <label for="name" class="text-gray-500 mb-2">Name</label>
                            <input type="text" id="name" name="name" required
                                class="appearance-none border-2 border-gray-100 rounded-lg px-4 py-3 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:shadow-lg" />
                        </div>
                        <div class="flex flex-col w-full my-5">
                            <label for="email" class="text-gray-500 mb-2">Email</label>
                            <input type="email" id="email" name="email" required
                                class="appearance-none border-2 border-gray-100 rounded-lg px-4 py-3 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:shadow-lg" />
                        </div>
                        <div class="flex flex-col w-full my-5">
                            <label for="password" class="text-gray-500 mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                class="appearance-none border-2 border-gray-100 rounded-lg px-4 py-3 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:shadow-lg" />
                        </div>
                        <div class="flex flex-col w-full my-5">
                            <button type="submit" class="w-full py-4 bg-blue-600 rounded-lg text-white hover:bg-blue-700">
                                <div class="flex flex-row items-center justify-center">
                                    <div class="mr-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="font-bold">Register</div>
                                </div>
                            </button>
                        </div>
                        <div class="text-center mt-4">
                            <p class="text-gray-600">Already have an account? 
                                <a href="login.php" class="text-blue-600 hover:text-blue-700 font-semibold">Login here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>