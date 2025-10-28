<?php
session_start();
include '../../app/db.php';
include '../../app/auth_manager.php';
include '../../app/anime_manager.php';

ensure_admin('../login.php', '../home.php');

$total_users = count_users($connect);
$total_anime = count_anime($connect);
$total_comments = count_comments($connect);
$latest_users = get_latest_users($connect);

$nav_items = [
	['label' => 'Dashboard', 'href' => 'dashboard.php'],
	['label' => 'Role Edit', 'href' => 'role_edit.php'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard - MyAnimeList</title>
	<link rel="icon" href="../../assets/images/sr.png" type="image/x-icon">
	<script src="https://cdn.tailwindcss.com"></script>
	<style>
		body {
			background-color: #f3f4f6;
		}
	</style>
</head>

<body>
	<?php include '../navbar.php'; ?>

	<main class="ml-64 min-h-screen bg-gray-50">
		<header class="p-6 flex justify-between items-center">
			<div>
				<h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
				<p class="text-gray-500">Kelola ekosistem MyAnimeList dengan mudah.</p>
			</div>
			<div class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg shadow">
				<img src="../../assets/images/user.jpeg" alt="Profile" width="45" height="45" class="rounded-full border-2" style="border-color:#2563eb;">
				<div>
					<p class="font-semibold text-gray-800 mb-0"><?php echo htmlspecialchars($_SESSION['login']); ?></p>
					<p class="text-xs text-gray-500 mb-0">Administrator</p>
				</div>
			</div>
		</header>

		<nav class="px-6">
			<div class="bg-white rounded-xl shadow overflow-hidden flex">
				<?php foreach ($nav_items as $item): ?>
					<?php $active = basename($_SERVER['PHP_SELF']) === basename($item['href']); ?>
					<a href="<?php echo $item['href']; ?>" class="flex-1 text-center py-3 text-sm font-medium <?php echo $active ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
						<?php echo $item['label']; ?>
					</a>
				<?php endforeach; ?>
			</div>
		</nav>

		<section class="p-6 space-y-6">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<div class="bg-white border rounded-xl shadow-xl p-6 border-t-4 border-blue-500">
					<p class="text-sm text-gray-500">Total Users</p>
					<p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_users; ?></p>
				</div>
				<div class="bg-white border rounded-xl shadow-xl p-6 border-t-4 border-purple-500">
					<p class="text-sm text-gray-500">Total Anime</p>
					<p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_anime; ?></p>
				</div>
				<div class="bg-white border rounded-xl shadow-xl p-6 border-t-4 border-green-500">
					<p class="text-sm text-gray-500">Total Comments</p>
					<p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_comments; ?></p>
				</div>
			</div>

			<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-6">
				<div class="flex items-center justify-between mb-4">
					<h2 class="text-2xl font-semibold text-gray-800">Pengguna Terbaru</h2>
				</div>

				<?php if (count($latest_users) > 0): ?>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
								<th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
								<th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-700">
							<?php foreach ($latest_users as $user): ?>
							<tr>
								<td class="px-4 py-2 font-medium text-gray-800"><?php echo htmlspecialchars($user['name']); ?></td>
								<td class="px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
								<td class="px-4 py-2 capitalize"><?php echo htmlspecialchars($user['usertype']); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php else: ?>
				<p class="text-gray-500">Belum ada data pengguna ditampilkan.</p>
				<?php endif; ?>
			</div>

			<div class="bg-white border border-gray-200 rounded-xl shadow-xl p-6">
				<h2 class="text-2xl font-semibold text-gray-800 mb-3">Catatan Admin</h2>
				<p class="text-gray-700 leading-relaxed">
					Gunakan halaman ini sebagai pusat kontrol untuk memantau aktivitas komunitas.
				</p>
			</div>
		</section>
	</main>
</body>

</html>
