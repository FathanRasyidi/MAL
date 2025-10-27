<?php
function get_all_anime(mysqli $db)
{
    return mysqli_query($db, "SELECT * FROM anime ORDER BY id DESC");
}

function count_anime(mysqli $db): int
{
    $result = mysqli_query($db, "SELECT COUNT(*) AS total FROM anime");
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return isset($row['total']) ? (int)$row['total'] : 0;
}

function count_users(mysqli $db): int
{
    $result = mysqli_query($db, "SELECT COUNT(*) AS total FROM users");
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return isset($row['total']) ? (int)$row['total'] : 0;
}

function count_comments(mysqli $db): int
{
    $result = mysqli_query($db, "SELECT COUNT(*) AS total FROM comment");
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return isset($row['total']) ? (int)$row['total'] : 0;
}

function count_user_list(mysqli $db, int $user_id): int
{
    $result = mysqli_query($db, "SELECT COUNT(*) AS total FROM user_anime_list WHERE user_id = $user_id");
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return isset($row['total']) ? (int)$row['total'] : 0;
}

function count_completed(mysqli $db, int $user_id): int
{
    $result = mysqli_query($db, "SELECT COUNT(*) AS total FROM user_anime_list WHERE user_id = $user_id AND status = 'Completed'");
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return isset($row['total']) ? (int)$row['total'] : 0;
}

function get_anime(mysqli $db, int $anime_id): ?array
{
    $result = mysqli_query($db, "SELECT a.*, u.name AS added_by_name FROM anime a LEFT JOIN users u ON a.added_by = u.id WHERE a.id = $anime_id");
    if (!$result || mysqli_num_rows($result) === 0) {
        return null;
    }
    return mysqli_fetch_assoc($result);
}

function get_user_list_entry(mysqli $db, int $user_id, int $anime_id): ?array
{
    $result = mysqli_query($db, "SELECT * FROM user_anime_list WHERE user_id = $user_id AND anime_id = $anime_id");
    if (!$result || mysqli_num_rows($result) === 0) {
        return null;
    }
    return mysqli_fetch_assoc($result);
}

function save_anime(mysqli $db, int $anime_id, int $user_id, array $input, array $files): int
{
    $title = mysqli_real_escape_string($db, trim($input['title'] ?? ''));
    $synopsis = mysqli_real_escape_string($db, trim($input['synopsis'] ?? ''));
    $genre = mysqli_real_escape_string($db, trim($input['genre'] ?? ''));
    $image_sql = null;

    if (!empty($files['image_upload']['tmp_name'])) {
        $image_data = mysqli_real_escape_string($db, file_get_contents($files['image_upload']['tmp_name']));
        $image_sql = "'$image_data'";
    }

    if ($anime_id > 0) {
        $query = "UPDATE anime SET title='$title', synopsis='$synopsis', genre='$genre'";
        if ($image_sql !== null) {
            $query .= ", image=$image_sql";
        }
        $query .= " WHERE id=$anime_id";
        mysqli_query($db, $query);
        return $anime_id;
    }

    if ($image_sql === null) {
        return 0;
    }

    $query = "INSERT INTO anime (title, synopsis, genre, image, added_by) VALUES ('$title', '$synopsis', '$genre', $image_sql, $user_id)";
    mysqli_query($db, $query);
    return (int)mysqli_insert_id($db);
}

function delete_anime(mysqli $db, int $anime_id): void
{
    mysqli_query($db, "DELETE FROM anime WHERE id = $anime_id");
}

function update_list_status(mysqli $db, int $user_id, int $anime_id, string $status): void
{
    $safe_status = mysqli_real_escape_string($db, $status);
    $check = mysqli_query($db, "SELECT id FROM user_anime_list WHERE user_id = $user_id AND anime_id = $anime_id");
    if ($check && mysqli_num_rows($check) > 0) {
        mysqli_query($db, "UPDATE user_anime_list SET status = '$safe_status' WHERE user_id = $user_id AND anime_id = $anime_id");
        return;
    }
    mysqli_query($db, "INSERT INTO user_anime_list (user_id, anime_id, status) VALUES ($user_id, $anime_id, '$safe_status')");
}

function delete_from_list(mysqli $db, int $user_id, int $anime_id): void
{
    mysqli_query($db, "DELETE FROM user_anime_list WHERE user_id = $user_id AND anime_id = $anime_id");
}

function get_mylist(mysqli $db, int $user_id)
{
    $query = "SELECT a.*, l.status FROM anime a JOIN user_anime_list l ON a.id = l.anime_id WHERE l.user_id = $user_id ORDER BY l.id DESC";
    return mysqli_query($db, $query);
}

function get_latest_users(mysqli $db): array
{
    $rows = [];
    $result = mysqli_query($db, "SELECT id, name, email, role AS usertype FROM users ORDER BY id DESC LIMIT 6");
    if (!$result) {
        return $rows;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function get_users(mysqli $db): array
{
    $rows = [];
    $result = mysqli_query($db, "SELECT id, name, email, role AS usertype FROM users ORDER BY name ASC");
    if (!$result) {
        return $rows;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function update_user_role(mysqli $db, int $user_id, string $role): bool
{
    $role = mysqli_real_escape_string($db, $role);
    return (bool)mysqli_query($db, "UPDATE users SET role = '$role' WHERE id = $user_id");
}

function delete_user(mysqli $db, int $user_id): bool
{
    $id = (int)$user_id;
    if ($id <= 0) {
        return false;
    }
    mysqli_query($db, "DELETE FROM comment WHERE user_id = $id");
    mysqli_query($db, "DELETE FROM user_anime_list WHERE user_id = $id");
    return (bool)mysqli_query($db, "DELETE FROM users WHERE id = $id");
}
