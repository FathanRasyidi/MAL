<?php
session_start();
include '../app/db.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];
$user_id = empty($_SESSION['id_user']) ? '' : $_SESSION['id_user'];

if (empty($_COOKIE['email']) && !isset($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

// Query untuk mengambil anime di list user
$sql = "SELECT a.*, l.status FROM anime a 
        JOIN user_anime_list l ON a.id = l.anime_id 
        WHERE l.user_id = '$user_id' 
        ORDER BY l.id DESC";
$result = mysqli_query($connect, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My List - MyAnimeList</title>
    <link rel="icon" href="../assets/images/sr.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f5f5f5;
        }
        .anime-cover {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-watching { background: #3b82f6; color: white; }
        .status-completed { background: #10b981; color: white; }
        .status-plan { background: #f59e0b; color: white; }
        .status-dropped { background: #ef4444; color: white; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="ml-64 min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="p-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">My Anime List</h1>
                <a class="navbar-brand flex items-center my-2 bg-white px-4 py-2 rounded-lg shadow">
                    <img src="../assets/images/suisei.png" alt="Profile" width="45" height="45" class="rounded-full border-2"
                        id="logo" style="margin-right: 10px; border-color: #2563eb;">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800"><?php echo isset($_SESSION['login']) ? $_SESSION['login'] : 'Guest'; ?></span>
                        <span class="text-xs text-gray-500"><?php echo ucfirst($user_type); ?></span>
                    </div>
                </a>
            </div>

            <!-- Filter Tabs -->
            <div class="p-6 pt-4">
                <div class="mb-6 flex gap-2" x-data="{ filter: 'all' }">
                <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'" 
                        class="px-4 py-2 rounded-lg font-medium shadow">
                    All
                </button>
                <button @click="filter = 'Watching'" :class="filter === 'Watching' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'" 
                        class="px-4 py-2 rounded-lg font-medium shadow">
                    Watching
                </button>
                <button @click="filter = 'Completed'" :class="filter === 'Completed' ? 'bg-green-600 text-white' : 'bg-white text-gray-700'" 
                        class="px-4 py-2 rounded-lg font-medium shadow">
                    Completed
                </button>
                <button @click="filter = 'Plan to Watch'" :class="filter === 'Plan to Watch' ? 'bg-orange-600 text-white' : 'bg-white text-gray-700'" 
                        class="px-4 py-2 rounded-lg font-medium shadow">
                    Plan to Watch
                </button>
                <button @click="filter = 'Dropped'" :class="filter === 'Dropped' ? 'bg-red-600 text-white' : 'bg-white text-gray-700'" 
                        class="px-4 py-2 rounded-lg font-medium shadow">
                    Dropped
                </button>
            </div>

                <!-- Anime Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-8">
                <?php while ($anime = mysqli_fetch_array($result)): ?>
                    <?php
                    $status_class = 'status-watching';
                    if ($anime['status'] == 'Completed') $status_class = 'status-completed';
                    if ($anime['status'] == 'Plan to Watch') $status_class = 'status-plan';
                    if ($anime['status'] == 'Dropped') $status_class = 'status-dropped';
                    ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                         x-show="filter === 'all' || filter === '<?php echo $anime['status']; ?>'">
                        <a href="article.php?id=<?php echo $anime['id']; ?>">
                            <img src="image.php?id=<?php echo $anime['id']; ?>" 
                                 alt="<?php echo htmlspecialchars($anime['title']); ?>" 
                                 class="anime-cover">
                        </a>
                        <div class="p-4">
                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $anime['status']; ?></span>
                            <a href="article.php?id=<?php echo $anime['id']; ?>" 
                               class="block mt-2 font-semibold text-gray-800 hover:text-blue-600 line-clamp-2">
                                <?php echo htmlspecialchars($anime['title']); ?>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>

                <?php if (mysqli_num_rows($result) == 0): ?>
                    <div class="text-center py-16">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        <h3 class="text-2xl font-bold text-gray-600 mb-2">Your list is empty</h3>
                        <p class="text-gray-500 mb-6">Start adding anime to your list!</p>
                        <a href="home.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                            Browse Anime
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
