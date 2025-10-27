# MyAnimeList - Simple CRUD Application

Aplikasi CRUD sederhana untuk mengelola daftar anime dengan sistem login dan komentar.

## 📋 Fitur

### User Features:
- ✅ Login & Register
- ✅ Lihat daftar semua anime
- ✅ Lihat detail anime
- ✅ Tambah anime ke list pribadi (Watching, Completed, Plan to Watch, Dropped)
- ✅ Beri rating anime (1-10)
- ✅ Komentar di setiap anime

### Admin/Editor Features:
- ✅ Tambah anime baru
- ✅ Edit anime
- ✅ Hapus anime
- ✅ Upload gambar cover (disimpan di database sebagai LONGBLOB)

## 🗄️ Database Structure

### Table: `users`
```sql
id (INT, Primary Key)
name (VARCHAR)
email (VARCHAR, Unique)
password (VARCHAR) -- menggunakan password_hash()
role (ENUM: 'admin', 'editor', 'user')
created_at (TIMESTAMP)
```

### Table: `anime`
```sql
id (INT, Primary Key)
title (VARCHAR)
synopsis (TEXT)
genre (VARCHAR) -- comma separated
image (LONGBLOB) -- gambar disimpan di database
added_by (INT, Foreign Key ke users.id)
created_at (TIMESTAMP)
updated_at (TIMESTAMP)
```

### Table: `user_anime_list`
```sql
id (INT, Primary Key)
user_id (INT, Foreign Key ke users.id)
anime_id (INT, Foreign Key ke anime.id)
status (ENUM: 'Watching', 'Completed', 'Plan to Watch', 'Dropped')
rating (INT, 1-10)
added_at (TIMESTAMP)
```

### Table: `comment`
```sql
id (INT, Primary Key)
user_id (INT, Foreign Key ke users.id)
anime_id (INT, Foreign Key ke anime.id)
comment_text (TEXT)
created_at (TIMESTAMP)
```

## 📁 File Structure

```
MAL/
├── app/
│   ├── db.php              # Koneksi database
│   ├── cek_login.php       # Proses login
│   ├── register.php        # Proses registrasi
│   └── logout.php          # Proses logout
├── public/
│   ├── home.php            # Halaman utama (daftar anime)
│   ├── article.php         # Detail/Add/Edit anime
│   ├── image.php           # Menampilkan gambar dari database
│   ├── navbar.php          # Sidebar navigasi
│   ├── login.php           # Halaman login
│   └── register.php        # Halaman registrasi
├── assets/
│   └── images/             # Folder untuk gambar static
└── database/
    └── anime_database.sql  # SQL untuk membuat database
```

## 🚀 Cara Install

1. **Import Database**
   ```sql
   CREATE DATABASE anime_list;
   ```
   Import file `database/anime_database.sql`

2. **Update Konfigurasi Database**
   Edit `app/db.php`:
   ```php
   $hostname = "localhost";
   $username = "root";
   $password = "";
   $database = "anime_list";
   ```

3. **Akses Aplikasi**
   Buka browser: `http://localhost/MAL/public/login.php`

4. **Default Login**
   - **Admin**: admin@anime.com / password
   - **Editor**: editor@anime.com / password  
   - **User**: user@anime.com / password

## 💡 Penjelasan Kode Sederhana

### `home.php` - Halaman Utama
```php
// 1. Cek login
if (empty($_COOKIE['email']) && !isset($_SESSION['login'])) {
    header("location:login.php");
    exit();
}

// 2. Ambil data statistik
$total_anime = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) as total FROM anime"))['total'];

// 3. Ambil semua anime
$result_anime = mysqli_query($connect, "SELECT * FROM anime ORDER BY id DESC");

// 4. Loop dan tampilkan
while ($anime = mysqli_fetch_assoc($result_anime)) {
    // tampilkan card anime
}
```

### `article.php` - CRUD Anime

**1. Tambah Anime:**
```php
if (isset($_POST['save_anime'])) {
    $title = $_POST['title'];
    $synopsis = $_POST['synopsis'];
    $image = file_get_contents($_FILES['image_upload']['tmp_name']); // Baca gambar
    
    // Insert ke database
    $stmt = $connect->prepare("INSERT INTO anime (title, synopsis, image) VALUES (?,?,?)");
    $stmt->bind_param("sss", $title, $synopsis, $image);
    $stmt->execute();
}
```

**2. Edit Anime:**
```php
if ($anime_id > 0 && isset($_POST['save_anime'])) {
    // Update data
    $stmt = $connect->prepare("UPDATE anime SET title=?, synopsis=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $synopsis, $anime_id);
    $stmt->execute();
}
```

**3. Hapus Anime:**
```php
if (isset($_GET['delete'])) {
    mysqli_query($connect, "DELETE FROM anime WHERE id = $anime_id");
}
```

**4. Add to List:**
```php
if (isset($_POST['add_to_list'])) {
    $status = $_POST['status'];
    $rating = $_POST['rating'];
    
    // Insert atau Update
    if (sudah ada di list) {
        UPDATE user_anime_list SET status, rating
    } else {
        INSERT INTO user_anime_list
    }
}
```

**5. Tambah Komentar:**
```php
if (isset($_POST['add_comment'])) {
    $comment_text = $_POST['comment_text'];
    mysqli_query($connect, "INSERT INTO comment (user_id, anime_id, comment_text) VALUES (...)");
}
```

### `image.php` - Tampilkan Gambar dari Database
```php
$id = $_GET['id'];
$stmt = $connect->prepare("SELECT image FROM anime WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($image);
$stmt->fetch();

header("Content-Type: image/jpeg");
echo $image; // Output gambar
```

## 🎯 Flow Aplikasi

1. **User Login** → `login.php` → `cek_login.php` → `home.php`
2. **Lihat Anime** → `home.php` (grid view semua anime)
3. **Detail Anime** → Click anime → `article.php?id=X`
4. **Tambah Anime** → `article.php` (tanpa ID) → Form → Save
5. **Edit Anime** → `article.php?id=X&edit=true` → Form → Update
6. **Add to List** → Di halaman detail → Form status & rating → Save
7. **Komentar** → Di halaman detail → Form komentar → Post

## 🔒 Role & Permission

| Fitur | User | Editor | Admin |
|-------|------|--------|-------|
| Lihat anime | ✅ | ✅ | ✅ |
| Add to list | ✅ | ✅ | ✅ |
| Komentar | ✅ | ✅ | ✅ |
| Tambah anime | ❌ | ✅ | ✅ |
| Edit anime | ❌ | ✅ | ✅ |
| Hapus anime | ❌ | ✅ | ✅ |

## 📝 Notes

- Gambar disimpan sebagai **LONGBLOB** di database (bukan di folder)
- Table komentar bernama **`comment`** (bukan `comments`)
- Password menggunakan **`password_hash()`** untuk keamanan
- Session digunakan untuk menyimpan data login
- Cookie digunakan untuk "Remember Me"

## 🐛 Troubleshooting

**Error: Table 'comments' doesn't exist**
- Gunakan table `comment` (singular)

**Error: Gambar tidak muncul**
- Pastikan field `image` bertipe LONGBLOB
- Cek file `image.php` sudah ada

**Error: Upload gambar failed**
- Cek `max_upload_size` di php.ini
- Pastikan form punya `enctype="multipart/form-data"`

## 📚 Technologies Used

- PHP 7.4+
- MySQL/MariaDB
- TailwindCSS (CDN)
- Alpine.js (untuk interaksi kecil)

---

Dibuat dengan ❤️ untuk belajar CRUD sederhana
