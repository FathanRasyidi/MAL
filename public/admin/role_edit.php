<?php
session_start();
include '../../app/db.php';
include '../../app/auth_manager.php';
include '../../app/anime_manager.php';

ensure_admin('../login.php', '../home.php');

$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['delete_user_id'])) {
		$target_id = (int)$_POST['delete_user_id'];
		$current_id = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
		if ($target_id === $current_id) {
			$feedback = 'Anda tidak dapat menghapus akun sendiri.';
		} elseif (delete_user($connect, $target_id)) {
			$feedback = 'Pengguna berhasil dihapus.';
		} else {
			$feedback = 'Terjadi kesalahan saat menghapus pengguna.';
		}
	} elseif (isset($_POST['user_id'], $_POST['usertype'])) {
		$user_id = (int)$_POST['user_id'];
		$new_role = $_POST['usertype'];

		if (in_array($new_role, ['admin', 'editor', 'user'], true)) {
			if (update_user_role($connect, $user_id, $new_role)) {
				$feedback = 'Role pengguna berhasil diperbarui.';
			} else {
				$feedback = 'Terjadi kesalahan saat memperbarui role.';
			}
		} else {
			$feedback = 'Role tidak valid.';
		}
	}
}

$users = get_users($connect);

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
	<title>Role Management - MyAnimeList</title>
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
				<h1 class="text-3xl font-bold text-gray-800">Role Management</h1>
				<p class="text-gray-500">Kelola tingkat akses pengguna.</p>
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

		<section class="p-6">
			<?php if ($feedback): ?>
			<div class="mb-4 px-4 py-3 rounded-lg <?php echo strpos($feedback, 'berhasil') !== false ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
				<?php echo $feedback; ?>
			</div>
			<?php endif; ?>

			<div class="bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
				<div class="px-6 py-4 border-b">
					<h2 class="text-xl font-semibold text-gray-800">Daftar Pengguna</h2>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200">
						<thead class="bg-gray-50">
							<tr>
								<th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
								<th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
								<th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role Saat Ini</th>
								<th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Atur Role</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-700">
							<?php foreach ($users as $user): ?>
							<tr>
								<td class="px-4 py-3 font-medium text-gray-800"><?php echo htmlspecialchars($user['name']); ?></td>
								<td class="px-4 py-3"><?php echo htmlspecialchars($user['email']); ?></td>
								<td class="px-4 py-3 capitalize"><?php echo htmlspecialchars($user['usertype']); ?></td>
								<td class="px-4 py-3">
									<div class="flex items-center gap-2">
										<form method="POST" class="flex items-center gap-2">
											<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
											<select name="usertype" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
												<option value="user" <?php echo $user['usertype'] === 'user' ? 'selected' : ''; ?>>User</option>
												<option value="editor" <?php echo $user['usertype'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
												<option value="admin" <?php echo $user['usertype'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
											</select>
											<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
										</form>
										<form method="POST" onsubmit="return confirm('Hapus pengguna ini? Tindakan ini tidak dapat dibatalkan.');">
											<input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
											<button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">Hapus</button>
										</form>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
							<?php if (count($users) === 0): ?>
							<tr>
								<td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada data pengguna.</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	</main>
</body>

</html>
