<?php
require_once '../config/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gallery.php?error=' . urlencode('Invalid request method'));
    exit;
}

$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = $_POST['category'] ?? 'general';
$status = $_POST['status'] ?? 'active';

// Validate required fields
if ($id <= 0 || empty($title)) {
    header('Location: ../gallery.php?error=' . urlencode('ID and title are required'));
    exit;
}

// Get current gallery data
$stmt = $conn->prepare("SELECT image FROM gallery WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$currentGallery = $result->fetch_assoc();
$stmt->close();

if (!$currentGallery) {
    header('Location: ../gallery.php?error=' . urlencode('Gallery item not found'));
    exit;
}

$imageName = $currentGallery['image'];

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/gallery/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowedTypes)) {
        header('Location: ../gallery.php?error=' . urlencode('Invalid image format. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.'));
        exit;
    }
    
    // Check file size (max 5MB)
    if ($_FILES['image']['size'] > 5000000) {
        header('Location: ../gallery.php?error=' . urlencode('Image file is too large. Maximum size is 5MB.'));
        exit;
    }
    
    // Delete old image if exists
    if ($imageName && file_exists($uploadDir . $imageName)) {
        unlink($uploadDir . $imageName);
    }
    
    // Generate unique filename
    $imageName = uniqid() . '_' . time() . '.' . $imageFileType;
    $targetFile = $uploadDir . $imageName;
    
    // Simple file upload without image processing
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        header('Location: ../gallery.php?error=' . urlencode('Failed to upload image file.'));
        exit;
    }
}

// Update database
$stmt = $conn->prepare("UPDATE gallery SET title = ?, description = ?, image = ?, category = ?, status = ? WHERE id = ?");
$stmt->bind_param("sssssi", $title, $description, $imageName, $category, $status, $id);

if ($stmt->execute()) {
    // Redirect back to gallery page with success message
    header('Location: ../gallery.php?updated=1');
    exit;
} else {
    // Redirect back with error message
    header('Location: ../gallery.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}

$stmt->close();
$conn->close();
?>
