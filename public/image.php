<?php
include '../app/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $connect->prepare("SELECT image FROM anime WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    
    if ($image) {
        header("Content-Type: image/jpeg");
        echo $image;
    } else {
        // Return placeholder jika tidak ada gambar
        header("Content-Type: image/svg+xml");
        echo '<svg width="225" height="318" xmlns="http://www.w3.org/2000/svg"><rect width="225" height="318" fill="#667eea"/><text x="50%" y="50%" text-anchor="middle" fill="white" font-size="16" font-family="Arial">No Image</text></svg>';
    }
} else {
    header("HTTP/1.0 404 Not Found");
}
?>
