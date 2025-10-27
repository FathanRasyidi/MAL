<?php
session_start();
include '../app/db.php';
include '../app/anime_manager.php';
include '../app/comment_manager.php';
include '../app/auth_manager.php';

ensure_logged_in();

$user_type = $_SESSION['usertype'] ?? 'user';
$profile_target = $user_type === 'admin' ? 'admin/dashboard.php' : '';
$user_id = (int)($_SESSION['id_user'] ?? 0);
$anime_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edit_mode = isset($_GET['edit']);

$anime = null;
$user_list = null;
$comments = [];

if ($anime_id > 0) {
    $anime = get_anime($connect, $anime_id);
    if (!$anime) {
        header("location:home.php");
        exit();
    }
    $user_list = get_user_list_entry($connect, $user_id, $anime_id);
    $comments = get_comments($connect, $anime_id);
}

if (isset($_POST['save_anime']) && ($user_type == 'admin' || $user_type == 'editor')) {
    $new_id = save_anime($connect, $anime_id, $user_id, $_POST, $_FILES);
    if ($new_id > 0) {
        header("location:article.php?id=$new_id&success=1");
    } else {
        header("location:article.php?id=$anime_id&success=1");
    }
    exit();
}

if (isset($_GET['delete']) && $anime_id > 0 && ($user_type == 'admin' || $user_type == 'editor')) {
    delete_anime($connect, $anime_id);
    header("location:home.php?deleted=1");
    exit();
}

if ($anime_id > 0 && isset($_POST['add_to_list'])) {
    update_list_status($connect, $user_id, $anime_id, $_POST['status'] ?? 'Plan to Watch');
    header("location:article.php?id=$anime_id");
    exit();
}

if ($anime_id > 0 && isset($_GET['remove_from_list'])) {
    delete_from_list($connect, $user_id, $anime_id);
    header("location:article.php?id=$anime_id");
    exit();
}

if ($anime_id > 0 && isset($_POST['add_comment'])) {
    add_comment($connect, $user_id, $anime_id, $_POST['comment_text'] ?? '');
    header("location:article.php?id=$anime_id");
    exit();
}

if ($anime_id > 0 && isset($_GET['delete_comment'])) {
    $comment_id = (int)$_GET['delete_comment'];
    $is_admin = ($user_type === 'admin');
    delete_comment($connect, $comment_id, $user_id, $is_admin);
    header("location:article.php?id=$anime_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $anime ? htmlspecialchars($anime['title']) : 'Add Anime'; ?> - MyAnimeList</title>
    <link rel="icon" href="../assets/images/sr.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
        }
        .genre-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            margin: 4px;
            background-color: #e0e7ff;
            color: #4338ca;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="ml-64 min-h-screen bg-gray-50">
        <header class="p-6 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="home.php" class="text-blue-600 hover:text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">
                    <?php
                    if ($anime_id === 0) {
                        echo 'Add New Anime';
                    } elseif ($edit_mode) {
                        echo 'Edit Anime';
                    } elseif ($anime) {
                        echo htmlspecialchars($anime['title']);
                    } else {
                        echo 'Anime Detail';
                    }
                    ?>
                </h1>
            </div>

            <div class="flex items-center">
                <?php if ($profile_target): ?>
                <a href="<?php echo $profile_target; ?>" class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg shadow hover:shadow-md transition">
                    <img src="../assets/images/user.jpeg" alt="Profile" width="45" height="45" class="rounded-full border-2" style="border-color: #2563eb;">
                    <div>
                        <p class="font-semibold text-gray-800 mb-0"><?php echo htmlspecialchars($_SESSION['login'] ?? 'User'); ?></p>
                        <p class="text-xs text-gray-500 mb-0"><?php echo ucfirst($user_type); ?></p>
                    </div>
                </a>
                <?php else: ?>
                <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg shadow">
                    <img src="../assets/images/suisei.png" alt="Profile" width="45" height="45" class="rounded-full border-2" style="border-color: #2563eb;">
                    <div>
                        <p class="font-semibold text-gray-800 mb-0"><?php echo htmlspecialchars($_SESSION['login'] ?? 'User'); ?></p>
                        <p class="text-xs text-gray-500 mb-0"><?php echo ucfirst($user_type); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <div class="p-6">
            <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                Anime successfully saved!
            </div>
            <?php endif; ?>

            <?php if ($anime_id === 0 || $edit_mode): ?>
                <?php if ($user_type == 'admin' || $user_type == 'editor'): ?>
                <section class="bg-white border border-gray-200 rounded-xl shadow-xl p-8 mb-8">
                    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                            <input type="text" name="title" required value="<?php echo $anime ? htmlspecialchars($anime['title']) : ''; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Synopsis *</label>
                            <textarea name="synopsis" rows="6" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo $anime ? htmlspecialchars($anime['synopsis']) : ''; ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Genre (comma separated)</label>
                            <input type="text" name="genre" value="<?php echo $anime ? htmlspecialchars($anime['genre']) : ''; ?>" placeholder="Action, Adventure, Fantasy" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image <?php echo $anime_id > 0 ? '' : '*'; ?></label>
                            <?php if ($anime && !empty($anime['image'])): ?>
                            <div class="mb-3">
                                <img src="image.php?id=<?php echo $anime['id']; ?>" alt="Current cover" class="w-48 h-64 object-cover rounded border">
                                <p class="text-sm text-gray-500 mt-2">Current image saved in database</p>
                            </div>
                            <?php endif; ?>
                            <input type="file" name="image_upload" accept="image/*" <?php echo $anime_id === 0 ? 'required' : ''; ?> class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-sm text-gray-500 mt-1">Allowed: JPG, JPEG, PNG, GIF, WEBP (Max 16MB)</p>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" name="save_anime" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                                <?php echo $anime_id > 0 ? 'Update Anime' : 'Add Anime'; ?>
                            </button>
                            <a href="<?php echo $anime_id > 0 ? 'article.php?id=' . $anime_id : 'home.php'; ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium">Cancel</a>
                        </div>
                    </form>
                </section>
                <?php else: ?>
                <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded">Only admin or editor can manage anime.</div>
                <?php endif; ?>

            <?php elseif ($anime): ?>
                <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1">
                        <div class="bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                            <?php if (!empty($anime['image'])): ?>
                                <img src="image.php?id=<?php echo $anime['id']; ?>" alt="<?php echo htmlspecialchars($anime['title']); ?>" class="w-full h-96 object-cover">
                            <?php else: ?>
                                <div class="w-full h-96 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-2xl font-bold text-center px-6"><?php echo htmlspecialchars($anime['title']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="p-6">
                                <h3 class="font-bold text-lg mb-3">Add to My List</h3>
                                <form method="POST" class="mb-3">
                                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="Plan to Watch" <?php echo ($user_list && $user_list['status'] == 'Plan to Watch') ? 'selected' : ''; ?>>Plan to Watch</option>
                                        <option value="Watching" <?php echo ($user_list && $user_list['status'] == 'Watching') ? 'selected' : ''; ?>>Watching</option>
                                        <option value="Completed" <?php echo ($user_list && $user_list['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Dropped" <?php echo ($user_list && $user_list['status'] == 'Dropped') ? 'selected' : ''; ?>>Dropped</option>
                                    </select>
                                    <button type="submit" name="add_to_list" class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">
                                        <?php echo $user_list ? 'Update List' : 'Add to List'; ?>
                                    </button>
                                </form>

                                <?php if ($user_list): ?>
                                <a href="?id=<?php echo $anime_id; ?>&remove_from_list=1" class="block w-full text-center bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg font-medium">
                                    Remove from List
                                </a>
                                <?php endif; ?>

                                <?php if ($user_type == 'admin' || $user_type == 'editor'): ?>
                                <div class="mt-4 pt-4 border-t">
                                    <h4 class="font-bold mb-3">Admin Actions</h4>
                                    <a href="?id=<?php echo $anime_id; ?>&edit=true" class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg font-medium mb-2">Edit Anime</a>
                                    <a href="?id=<?php echo $anime_id; ?>&delete=1" onclick="return confirm('Delete this anime?')" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium">Delete Anime</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <section class="bg-white border border-gray-200 rounded-xl shadow-xl p-8">
                            <h2 class="text-3xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($anime['title']); ?></h2>

                            <?php if (!empty($anime['genre'])): ?>
                            <div class="mb-4">
                                <?php foreach (explode(',', $anime['genre']) as $genre): $genre = trim($genre); if ($genre === '') continue; ?>
                                    <span class="genre-badge"><?php echo htmlspecialchars($genre); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <p class="text-gray-700 leading-relaxed mb-4"><?php echo nl2br(htmlspecialchars($anime['synopsis'])); ?></p>

                            <p class="text-sm text-gray-500">Added by: <?php echo htmlspecialchars($anime['added_by_name'] ?? 'Unknown'); ?></p>
                        </section>

                        <section class="bg-white border border-gray-200 rounded-xl shadow-xl p-8">
                            <h3 class="text-2xl font-bold mb-6">Comments</h3>

                            <form method="POST" class="mb-8">
                                <textarea name="comment_text" rows="3" required placeholder="Write your comment..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3"></textarea>
                                <button type="submit" name="add_comment" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">Post Comment</button>
                            </form>

                            <?php if (count($comments) > 0): ?>
                                <?php foreach ($comments as $comment): ?>
                                <div class="border-b border-gray-200 pb-4 mb-4 last:border-0">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold"><?php echo strtoupper(substr($comment['name'], 0, 1)); ?></div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($comment['name']); ?></span>
                                                    <span class="text-xs text-gray-500"><?php echo date('M j, Y - g:i A', strtotime($comment['created'])); ?></span>
                                                </div>
                                                <?php if ($user_type === 'admin' || (int)$comment['user_id'] === $user_id): ?>
                                                <a href="?id=<?php echo $anime_id; ?>&delete_comment=<?php echo $comment['id']; ?>" onclick="return confirm('Delete this comment?')" class="text-red-500 hover:text-red-600" title="Hapus komentar" aria-label="Hapus komentar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                        <path fill-rule="evenodd" d="M9.75 3a1.5 1.5 0 00-1.415 1.013L8.036 5.25H5.25a.75.75 0 000 1.5h.56l.63 11.102A2.25 2.25 0 008.686 20h6.628a2.25 2.25 0 002.247-2.148L18.19 6.75h.56a.75.75 0 000-1.5h-2.786l-.3-1.237A1.5 1.5 0 0013.75 3h-4zm4.677 3.75l-.49 10.853a.75.75 0 01-.748.697H9.561a.75.75 0 01-.748-.697L8.323 6.75h6.104z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-6">No comments yet. Be the first to comment!</p>
                            <?php endif; ?>
                        </section>
                    </div>
                </section>
            <?php else: ?>
                <div class="bg-white border border-gray-200 rounded-xl shadow-xl p-8 text-center text-gray-600">Anime not found.</div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>
