<?php
require_once '../config/config.php';

// Accept both GET and POST methods
$id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);
}

if ($id <= 0) {
    header('Location: ../gallery.php?error=' . urlencode('Invalid gallery ID'));
    exit;
}

// Get gallery data to delete associated image
$stmt = $conn->prepare("SELECT image FROM gallery WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$gallery = $result->fetch_assoc();
$stmt->close();

if (!$gallery) {
    header('Location: ../gallery.php?error=' . urlencode('Gallery item not found'));
    exit;
}

// Delete the gallery item from database
$stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Delete associated image file if exists
    if ($gallery['image']) {
        $imagePath = '../uploads/gallery/' . $gallery['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    header('Location: ../gallery.php?deleted=1');
    exit;
} else {
    header('Location: ../gallery.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}

$stmt->close();
$conn->close();
?>
