<?php
session_start();
include '../app/db.php';
$user_type = empty($_SESSION['usertype']) ? '' : $_SESSION['usertype'];
$user_id = empty($_SESSION['id_user']) ? '' : $_SESSION['id_user'];

if (empty($_COOKIE['email']) && !isset($_SESSION['login'])) {
    header("location:login.php?pesan=belum_login");
    exit();
}

// Query untuk mendapatkan semua anime
$sql_anime = "SELECT * FROM anime ORDER BY id DESC";
$result_anime = mysqli_query($connect, $sql_anime);

// Query untuk statistik
$sql_total_anime = "SELECT COUNT(*) as total FROM anime";
$result_total = mysqli_query($connect, $sql_total_anime);
$total_anime = mysqli_fetch_array($result_total)['total'];

// Query untuk anime di list user
$sql_my_list = "SELECT COUNT(*) as total FROM user_anime_list WHERE user_id = '$user_id'";
$result_my_list = mysqli_query($connect, $sql_my_list);
$total_my_list = mysqli_fetch_array($result_my_list)['total'];

// Query untuk completed anime
$sql_completed = "SELECT COUNT(*) as total FROM user_anime_list WHERE user_id = '$user_id' AND status = 'Completed'";
$result_completed = mysqli_query($connect, $sql_completed);
$total_completed = mysqli_fetch_array($result_completed)['total'];

// Query untuk total article count
$sql_articles = "SELECT COUNT(*) as total FROM anime";
$result_articles = mysqli_query($connect, $sql_articles);
$total_articles = mysqli_fetch_array($result_articles)['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MyAnimeList - Home</title>
    <link rel="icon" href="../assets/images/sr.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #f5f5f5;
        }

        .fixed-sidebar {
            position: fixed;
            z-index: 1;
        }

        .anime-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .anime-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .anime-cover {
            width: 100%;
            height: 280px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: scale(1.05);
        }

        .genre-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin: 2px;
            background-color: #e0e7ff;
            color: #4338ca;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="ml-64 min-h-screen">
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

            <!-- Statistics Cards -->
            <div class="px-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Total Anime -->
                <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Anime</p>
                            <p class="text-3xl font-bold mt-1"><?php echo $total_anime; ?></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                </div>

                <!-- My List -->
                <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">My List</p>
                            <p class="text-3xl font-bold mt-1"><?php echo $total_my_list; ?></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </div>
                </div>

                <!-- Completed -->
                <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Completed</p>
                            <p class="text-3xl font-bold mt-1"><?php echo $total_completed; ?></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Total Articles -->
                <div class="stat-card bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Articles</p>
                            <p class="text-3xl font-bold mt-1"><?php echo $total_articles; ?></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                </div>
            </div>

                <!-- Anime List Section -->
                <div class="bg-white rounded-xl shadow-lg p-6 mx-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">All Anime</h2>
                    <?php if ($user_type == 'admin' || $user_type == 'editor'): ?>
                    <a href="article.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        + Add New Anime
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Anime Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    <?php 
                    if (mysqli_num_rows($result_anime) > 0) {
                        while ($anime = mysqli_fetch_assoc($result_anime)) {
                            $genres = explode(',', $anime['genre']);
                    ?>
                        <div class="anime-card bg-white rounded-lg overflow-hidden shadow-md">
                            <div class="relative">
                                <?php if (!empty($anime['image'])): ?>
                                    <img src="image.php?id=<?php echo $anime['id']; ?>" 
                                         alt="<?php echo htmlspecialchars($anime['title']); ?>" 
                                         class="anime-cover">
                                <?php else: ?>
                                    <div class="anime-cover flex items-center justify-center">
                                        <span class="text-white text-xl font-bold text-center px-4">
                                            <?php echo htmlspecialchars($anime['title']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Quick Actions -->
                                <div class="absolute top-2 right-2 opacity-0 hover:opacity-100 transition-opacity">
                                    <button class="bg-white text-blue-600 rounded-full p-2 shadow-lg hover:bg-blue-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="p-4">
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 hover:text-blue-600 transition" title="<?php echo htmlspecialchars($anime['title']); ?>">
                                    <?php echo htmlspecialchars($anime['title']); ?>
                                </h3>
                                
                                <!-- Genres -->
                                <div class="mb-2 flex flex-wrap">
                                    <?php 
                                    $display_genres = array_slice($genres, 0, 2);
                                    foreach ($display_genres as $genre): 
                                        $genre = trim($genre);
                                        if (!empty($genre)):
                                    ?>
                                        <span class="genre-badge"><?php echo htmlspecialchars($genre); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>

                                <!-- Synopsis Preview -->
                                <?php if (!empty($anime['synopsis'])): ?>
                                <p class="text-sm text-gray-600 line-clamp-3 mb-3">
                                    <?php echo htmlspecialchars(substr($anime['synopsis'], 0, 80)) . '...'; ?>
                                </p>
                                <?php endif; ?>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="article.php?id=<?php echo $anime['id']; ?>" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 text-center py-2 rounded text-sm font-medium transition">
                                        View Details
                                    </a>
                                    <?php if ($user_type == 'admin' || $user_type == 'editor'): ?>
                                    <a href="article.php?id=<?php echo $anime['id']; ?>&edit=true" class="bg-yellow-50 hover:bg-yellow-100 text-yellow-600 px-3 py-2 rounded text-sm transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                        }
                    } else {
                    ?>
                        <div class="col-span-full text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500 text-lg">No anime found. Start adding some!</p>
                            <?php if ($user_type == 'admin' || $user_type == 'editor'): ?>
                            <a href="article.php" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
                                + Add Your First Anime
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php } ?>
                </div>
                </div>
            </div>
        </div>

</body>

</html>