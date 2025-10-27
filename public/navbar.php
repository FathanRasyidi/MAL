<aside class="fixed top-0 left-0 w-64 h-screen pb-6 px-5 py-8 overflow-y-auto bg-white border-r">
    <a class="navbar-brand text-gray-600 flex items-center pl-2 my-2">
        <img src="../assets/images/sr.png" alt="" width="50" height="50" class="d-inline-block" id="logo" style="margin-right: 10px">
        <span class="ml-2">MyAnimeList</span>
    </a>

    <div class="flex flex-col h-full mt-6">
        <nav class="-mx-3 space-y-6 flex-1">
            <div class="space-y-3 ">
                <label class="px-3 text-xs text-gray-500 uppercase ">navigasi</label>

                <a class="flex items-center px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'bg-gray-300' : ''; ?> text-gray-600 transition-colors duration-300 transform rounded-lg hover:bg-gray-200 hover:text-gray-700"
                    href="home.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="mx-2 text-sm font-medium">Home</span>
                </a>

                <a class="flex items-center px-3 py-2 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['article.php']) ? 'bg-gray-300' : ''; ?> text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                    href="article.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="mx-2 text-sm font-medium">Add Anime</span>
                </a>

                <a class="flex items-center px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'mylist.php' ? 'bg-gray-300' : ''; ?> text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                    href="mylist.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <span class="mx-2 text-sm font-medium">My List</span>
                </a>
                
                <a class="flex items-center px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'bg-gray-300' : ''; ?> text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
                    href="about.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <span class="mx-2 text-sm font-medium">About</span>
                </a>
            </div>
        </nav>
        
        <!-- Logout di paling bawah -->
        <a class="flex items-center px-3 py-2 text-red-600 transition-colors duration-300 transform rounded-lg hover:bg-gray-200 hover:text-red-600"
            href="../app/logout.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"
                class="h-5 w-5">
                <path fill-rule="evenodd"
                    d="M12 2.25a.75.75 0 01.75.75v9a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM6.166 5.106a.75.75 0 010 1.06 8.25 8.25 0 1011.668 0 .75.75 0 111.06-1.06c3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788a.75.75 0 011.06 0z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="mx-2 text-sm font-medium">Logout</span>
        </a>
    </div>
</aside>