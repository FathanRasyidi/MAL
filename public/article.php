<?php
session_start();
include '../app/db.php';

// Cek login
if (empty($_COOKIE['email']) && !isset($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

$user_type = $_SESSION['usertype'];
$user_id = $_SESSION['id_user'];
$anime_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edit_mode = isset($_GET['edit']);

// === AMBIL DATA ANIME ===
$anime = null;
$user_list = null;
$comments = [];

if ($anime_id > 0) {
    // Ambil data anime
    $query = mysqli_query($connect, "SELECT a.*, u.name as added_by_name FROM anime a LEFT JOIN users u ON a.added_by = u.id WHERE a.id = $anime_id");
    $anime = mysqli_fetch_assoc($query);
    
    if (!$anime) {
        header("location:home.php");
        exit();
    }
    
    // Ambil status di list user
    $list_query = mysqli_query($connect, "SELECT * FROM user_anime_list WHERE user_id=$user_id AND anime_id=$anime_id");
    $user_list = mysqli_fetch_assoc($list_query);
    
    // Ambil komentar (table comment, bukan comments)
    $comment_query = mysqli_query($connect, "SELECT c.*, u.name FROM comment c JOIN users u ON c.user_id = u.id WHERE c.anime_id = $anime_id ORDER BY c.created DESC");
    while ($row = mysqli_fetch_assoc($comment_query)) {
        $comments[] = $row;
    }
}

// === PROSES SIMPAN ANIME ===
if (isset($_POST['save_anime']) && ($user_type == 'admin' || $user_type == 'editor')) {
    $title = $_POST['title'];
    $synopsis = $_POST['synopsis'];
    $genre = $_POST['genre'];
    
    // Upload gambar
    $image = null;
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] == 0) {
        $image = file_get_contents($_FILES['image_upload']['tmp_name']);
    } else if ($anime_id > 0) {
        // Ambil gambar lama jika edit
        $old = mysqli_query($connect, "SELECT image FROM anime WHERE id = $anime_id");
        $old_data = mysqli_fetch_assoc($old);
        $image = $old_data['image'];
    }
    
    if ($anime_id > 0) {
        // UPDATE
        $stmt = $connect->prepare("UPDATE anime SET title=?, synopsis=?, genre=?, image=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $synopsis, $genre, $image, $anime_id);
        $stmt->execute();
        header("location:article.php?id=$anime_id&success=1");
    } else {
        // INSERT
        $stmt = $connect->prepare("INSERT INTO anime (title, synopsis, genre, image, added_by) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssssi", $title, $synopsis, $genre, $image, $user_id);
        $stmt->execute();
        $new_id = $stmt->insert_id;
        header("location:article.php?id=$new_id&success=1");
    }
    exit();
}

// === PROSES DELETE ANIME ===
if (isset($_GET['delete']) && $anime_id > 0 && ($user_type == 'admin' || $user_type == 'editor')) {
    mysqli_query($connect, "DELETE FROM anime WHERE id = $anime_id");
    header("location:home.php?deleted=1");
    exit();
}

// === PROSES ADD TO LIST ===
if (isset($_POST['add_to_list'])) {
    $status = $_POST['status'];
    
    // Cek sudah ada belum
    $check = mysqli_query($connect, "SELECT * FROM user_anime_list WHERE user_id=$user_id AND anime_id=$anime_id");
    if (mysqli_num_rows($check) > 0) {
        // UPDATE
        mysqli_query($connect, "UPDATE user_anime_list SET status='$status' WHERE user_id=$user_id AND anime_id=$anime_id");
    } else {
        // INSERT
        mysqli_query($connect, "INSERT INTO user_anime_list (user_id, anime_id, status) VALUES ($user_id, $anime_id, '$status')");
    }
    header("location:article.php?id=$anime_id");
    exit();
}

// === PROSES REMOVE FROM LIST ===
if (isset($_GET['remove_from_list'])) {
    mysqli_query($connect, "DELETE FROM user_anime_list WHERE user_id=$user_id AND anime_id=$anime_id");
    header("location:article.php?id=$anime_id");
    exit();
}

// === PROSES ADD COMMENT ===
if (isset($_POST['add_comment'])) {
    $comment_text = mysqli_real_escape_string($connect, $_POST['comment_text']);
    mysqli_query($connect, "INSERT INTO comment (user_id, anime_id, comment_text, created) VALUES ($user_id, $anime_id, '$comment_text', CURRENT_TIMESTAMP)");
    header("location:article.php?id=$anime_id");
    exit();
}

// === PROSES DELETE COMMENT ===
if (isset($_GET['delete_comment'])) {
    $comment_id = (int)$_GET['delete_comment'];
    mysqli_query($connect, "DELETE FROM comment WHERE id=$comment_id AND user_id=$user_id");
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
            background-color: #f5f5f5;
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
    
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <div class="p-6 flex justify-between items-center bg-white border-b sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <a href="home.php" class="text-blue-600 hover:text-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <?php 
                        if ($anime_id == 0) echo "Add New Anime";
                        elseif ($edit_mode) echo "Edit Anime";
                        else echo htmlspecialchars($anime['title']);
                        ?>
                    </h1>
                </div>
                
                <a class="navbar-brand flex items-center my-2 bg-white px-4 py-2 rounded-lg shadow">
                    <img src="../assets/images/suisei.png" alt="Profile" width="45" height="45" class="rounded-full border-2" style="margin-right: 10px; border-color: #2563eb;">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800"><?php echo $_SESSION['login']; ?></span>
                        <span class="text-xs text-gray-500"><?php echo ucfirst($user_type); ?></span>
                    </div>
                </a>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Alerts -->
                <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Anime successfully saved!
                </div>
                <?php endif; ?>

                <?php if ($anime_id == 0 || $edit_mode): ?>
                    <!-- Form Add/Edit Anime -->
                    <?php if ($user_type == 'admin' || $user_type == 'editor'): ?>
                    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                <input type="text" name="title" required 
                                       value="<?php echo $anime ? htmlspecialchars($anime['title']) : ''; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Synopsis *</label>
                                <textarea name="synopsis" rows="6" required 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo $anime ? htmlspecialchars($anime['synopsis']) : ''; ?></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Genre (comma separated)</label>
                                <input type="text" name="genre" 
                                       value="<?php echo $anime ? htmlspecialchars($anime['genre']) : ''; ?>"
                                       placeholder="Action, Adventure, Fantasy"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cover Image <?php echo $anime_id > 0 ? '' : '*'; ?>
                                </label>
                                <?php if ($anime && !empty($anime['image'])): ?>
                                <div class="mb-3">
                                    <img src="image.php?id=<?php echo $anime['id']; ?>" 
                                         alt="Current cover" 
                                         class="w-48 h-64 object-cover rounded border">
                                    <p class="text-sm text-gray-500 mt-2">Current image saved in database</p>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="image_upload" accept="image/*" 
                                       <?php echo $anime_id == 0 ? 'required' : ''; ?>
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <p class="text-sm text-gray-500 mt-1">Allowed: JPG, JPEG, PNG, GIF, WEBP (Max 16MB)</p>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex gap-3">
                            <button type="submit" name="save_anime" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium">
                                <?php echo $anime_id > 0 ? 'Update Anime' : 'Add Anime'; ?>
                            </button>
                            <?php if ($anime_id > 0): ?>
                            <a href="article.php?id=<?php echo $anime_id; ?>" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-3 rounded-lg font-medium inline-block">
                                Cancel
                            </a>
                            <?php else: ?>
                            <a href="home.php" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-3 rounded-lg font-medium inline-block">
                                Cancel
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                
            <?php elseif ($anime): ?>
                <!-- View Anime Detail -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Cover & Actions -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-6">
                            <?php if (!empty($anime['image'])): ?>
                                <img src="image.php?id=<?php echo $anime['id']; ?>" 
                                     alt="<?php echo htmlspecialchars($anime['title']); ?>" 
                                     class="w-full h-96 object-cover">
                            <?php else: ?>
                                <div class="w-full h-96 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-2xl font-bold text-center px-6">
                                        <?php echo htmlspecialchars($anime['title']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-6">
                                <h3 class="font-bold text-lg mb-4">Add to My List</h3>
                                <form method="POST">
                                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3">
                                        <option value="Plan to Watch" <?php echo ($user_list && $user_list['status'] == 'Plan to Watch') ? 'selected' : ''; ?>>Plan to Watch</option>
                                        <option value="Watching" <?php echo ($user_list && $user_list['status'] == 'Watching') ? 'selected' : ''; ?>>Watching</option>
                                        <option value="Completed" <?php echo ($user_list && $user_list['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Dropped" <?php echo ($user_list && $user_list['status'] == 'Dropped') ? 'selected' : ''; ?>>Dropped</option>
                                    </select>
                                    
                                    <button type="submit" name="add_to_list" 
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium mt-3">
                                        <?php echo $user_list ? 'Update List' : 'Add to List'; ?>
                                    </button>
                                </form>
                                
                                <?php if ($user_list): ?>
                                <a href="?id=<?php echo $anime_id; ?>&remove_from_list=1" 
                                   class="block w-full text-center bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg font-medium mt-2">
                                    Remove from List
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($user_type == 'admin' || $user_type == 'editor'): ?>
                                <div class="mt-4 pt-4 border-t">
                                    <h4 class="font-bold mb-3">Admin Actions</h4>
                                    <a href="?id=<?php echo $anime_id; ?>&edit=true" 
                                       class="block w-full text-center bg-yellow-50 hover:bg-yellow-100 text-yellow-700 py-2 rounded-lg font-medium mb-2">
                                        Edit Anime
                                    </a>
                                    <a href="?id=<?php echo $anime_id; ?>&delete=1" 
                                       onclick="return confirm('Are you sure you want to delete this anime?')"
                                       class="block w-full text-center bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg font-medium">
                                        Delete Anime
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Info & Comments -->
                    <div class="lg:col-span-2">
                        <!-- Info -->
                        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                            <h2 class="text-3xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($anime['title']); ?></h2>
                            
                            <!-- Genres -->
                            <?php if (!empty($anime['genre'])): ?>
                            <div class="mb-4">
                                <?php 
                                $genres = explode(',', $anime['genre']);
                                foreach ($genres as $genre): 
                                    $genre = trim($genre);
                                    if (!empty($genre)):
                                ?>
                                    <span class="genre-badge"><?php echo htmlspecialchars($genre); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            <?php endif; ?>
                            
                            <h3 class="font-bold text-lg mb-2">Synopsis</h3>
                            <p class="text-gray-700 leading-relaxed mb-4"><?php echo nl2br(htmlspecialchars($anime['synopsis'])); ?></p>
                            
                            <div class="text-sm text-gray-500">
                                <p>Added by: <?php echo $anime['added_by_name'] ?? 'Unknown'; ?></p>
                            </div>
                        </div>
                        
                        <!-- Comments Section -->
                        <div class="bg-white rounded-xl shadow-lg p-8">
                            <h3 class="text-2xl font-bold mb-6">Comments</h3>
                            
                            <!-- Add Comment Form -->
                            <form method="POST" class="mb-8">
                                <textarea name="comment_text" rows="3" required 
                                          placeholder="Write your comment..." 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3"></textarea>
                                <button type="submit" name="add_comment" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                                    Post Comment
                                </button>
                            </form>
                            
                            <!-- Comments List -->
                            <?php if (count($comments) > 0): ?>
                                <?php foreach ($comments as $comment): ?>
                                <div class="border-b border-gray-200 pb-4 mb-4 last:border-0">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                            <?php echo strtoupper(substr($comment['name'], 0, 1)); ?>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($comment['name']); ?></span>
                                                    <span class="text-xs text-gray-500"><?php echo date('M j, Y - g:i A', strtotime($comment['created'])); ?></span>
                                                </div>
                                                <?php if ($comment['user_id'] == $user_id): ?>
                                                <a href="?id=<?php echo $anime_id; ?>&delete_comment=<?php echo $comment['id']; ?>" 
                                                   onclick="return confirm('Delete this comment?')"
                                                   class="text-red-500 hover:text-red-700 text-sm">Delete</a>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-8">No comments yet. Be the first to comment!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
