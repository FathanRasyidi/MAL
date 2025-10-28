<?php
session_start();
include '../app/db.php';
include '../app/auth_manager.php';
ensure_logged_in();

$user_type = $_SESSION['usertype'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>About - MyAnimeList</title>
	<link rel="icon" href="../assets/images/sr.png" type="image/x-icon">
	<script src="https://cdn.tailwindcss.com"></script>
	<style>
		body {
			background-color: #f3f4f6;
		}
	</style>
</head>

<body>
	<?php include 'navbar.php'; ?>

	<main class="ml-64 min-h-screen bg-gray-50">
		<header class="p-6 border-b">
			<h1 class="text-3xl font-bold text-gray-800">About Me</h1>
			<p class="text-gray-500 mt-1">Sekilas cerita mengenai sosok di balik dashboard MyAnimeList ini.</p>
		</header>

		<section class="p-6 space-y-6">
			<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-8 flex flex-col lg:flex-row gap-6">
				<img src="../assets/images/user.jpeg" alt="Profile" class="w-32 h-32 rounded-full border-4 border-blue-500 shadow self-start">
				<div>
					<h2 class="text-2xl font-semibold text-gray-800 mb-2">Halo, saya <?php echo htmlspecialchars($_SESSION['login'] ?? 'Pengguna'); ?></h2>
					<p class="text-gray-700 leading-relaxed mb-3">
						Seorang penggemar anime dan pengembang web yang senang meramu ide kreatif menjadi produk digital yang rapih. Dashboard ini saya bangun sebagai wadah pribadi untuk mendokumentasikan judul favorit, berbagi opini, dan menjaga daftar tontonan tetap teratur.
					</p>
				</div>
			</div>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-6">
					<h3 class="text-xl font-semibold text-gray-800 mb-2">Apa yang Saya Lakukan</h3>
					<p class="text-gray-600">Mendesain dan membangun aplikasi web ringan menggunakan PHP &amp; Tailwind, sekaligus merawat basis data agar performa tetap stabil.</p>
				</div>
				<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-6">
					<h3 class="text-xl font-semibold text-gray-800 mb-2">Nilai yang Saya Pegang</h3>
					<p class="text-gray-600">Kerapihan kode, konsistensi UI, dan pengalaman pengguna yang intuitif agar setiap fitur terasa natural digunakan.</p>
				</div>
				<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-6">
					<h3 class="text-xl font-semibold text-gray-800 mb-2">Kesukaan di Waktu Luang</h3>
					<p class="text-gray-600">Mengulas episode terbaru, eksperimen dengan bahasa pemrograman, dan mengeksplorasi anime.</p>
				</div>
			</div>

			<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-8">
				<h2 class="text-2xl font-semibold text-gray-800 mb-3">Kemampuan Utama</h2>
				<ul class="space-y-3 text-gray-700">
					<li class="flex items-start gap-3">
						<span class="mt-1 w-2 h-2 bg-blue-600 rounded-full"></span>
						Mengorganisir daftar anime dengan status dinamis serta penyimpanan cover berbasis database.
					</li>
					<li class="flex items-start gap-3">
						<span class="mt-1 w-2 h-2 bg-blue-600 rounded-full"></span>
						Membuat antarmuka cepat responsif menggunakan Tailwind CSS dan Alpine.js.
					</li>
					<li class="flex items-start gap-3">
						<span class="mt-1 w-2 h-2 bg-blue-600 rounded-full"></span>
						Merancang fitur komunitas seperti komentar dan manajemen peran untuk menjaga interaksi tetap sehat.
					</li>
				</ul>
			</div>

			<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-8">
				<h2 class="text-2xl font-semibold text-gray-800 mb-4">Teknologi Favorit</h2>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-gray-700">
					<div class="border border-blue-100 rounded-lg px-4 py-3 bg-blue-50">PHP</div>
					<div class="border border-blue-100 rounded-lg px-4 py-3 bg-blue-50">Tailwind CSS</div>
					<div class="border border-blue-100 rounded-lg px-4 py-3 bg-blue-50">MySQL</div>
				</div>
			</div>

			<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-8">
				<h2 class="text-2xl font-semibold text-gray-800 mb-3">Mari Terhubung</h2>
				<p class="text-gray-700 mb-4">Punya saran fitur atau ingin berkolaborasi? Jangan ragu untuk mengirim pesan. Saya senang berdiskusi seputar anime maupun pengembangan web.</p>
				<div class="flex flex-wrap gap-4">
					<a href="https://www.linkedin.com/in/fathanras/" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
						Connect on LinkedIn
					</a>
				</div>
			</div>
		</section>
	</main>
</body>

</html>
