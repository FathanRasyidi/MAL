<?php
function get_comments(mysqli $db, int $anime_id): array {
    $items = [];
    $sql = "SELECT c.*, u.name FROM comment c JOIN users u ON c.user_id = u.id WHERE c.anime_id = $anime_id ORDER BY c.created DESC";
    $query = mysqli_query($db, $sql);
    if (!$query) {
        return $items;
    }
    while ($row = mysqli_fetch_assoc($query)) {
        $items[] = $row;
    }
    return $items;
}

function add_comment(mysqli $db, int $user_id, int $anime_id, string $text): void {
    $message = trim($text);
    if ($message === '') {
        return;
    }
    $safe = mysqli_real_escape_string($db, $message);
    mysqli_query($db, "INSERT INTO comment (user_id, anime_id, comment_text, created) VALUES ($user_id, $anime_id, '$safe', CURRENT_TIMESTAMP)");
}

function delete_comment(mysqli $db, int $comment_id, int $user_id, bool $is_admin): void {
    $comment_id = max($comment_id, 0);
    if ($comment_id === 0) {
        return;
    }
    if ($is_admin) {
        mysqli_query($db, "DELETE FROM comment WHERE id = $comment_id");
        return;
    }
    mysqli_query($db, "DELETE FROM comment WHERE id = $comment_id AND user_id = $user_id");
}
