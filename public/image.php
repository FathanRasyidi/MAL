<?php
include '../app/db.php';

if (!isset($_GET['id'])) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

$id = (int)$_GET['id'];
$result = mysqli_query($connect, "SELECT image FROM anime WHERE id = $id");
$row = $result ? mysqli_fetch_assoc($result) : null;

if ($row && !empty($row['image'])) {
    header("Content-Type: image/jpeg");
    echo $row['image'];
    exit();
}

header("Content-Type: image/svg+xml");
echo '<svg width="225" height="318" xmlns="http://www.w3.org/2000/svg"><rect width="225" height="318" fill="#667eea"/><text x="50%" y="50%" text-anchor="middle" fill="white" font-size="16" font-family="Arial">No Image</text></svg>';
?>
