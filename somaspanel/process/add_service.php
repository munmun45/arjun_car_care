<?php
header('Content-Type: application/json');
require_once '../config/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if services table exists, if not create it
$tableCheck = $conn->query("SHOW TABLES LIKE 'services'");
if ($tableCheck->num_rows == 0) {
    $createTable = "
    CREATE TABLE `services` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `icon` varchar(100) DEFAULT NULL,
      `image` varchar(255) DEFAULT NULL,
      `status` enum('active','inactive') DEFAULT 'active',
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    if (!$conn->query($createTable)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create services table: ' . $conn->error]);
        exit;
    }
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$icon = trim($_POST['icon'] ?? '');
$status = $_POST['status'] ?? 'active';

// Validate required fields
if (empty($title) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Title and description are required']);
    exit;
}

$imageName = null;

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/services/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowedTypes)) {
        header('Location: ../services.php?error=' . urlencode('Invalid image format. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.'));
        exit;
    }
    
    // Check file size (max 5MB)
    if ($_FILES['image']['size'] > 5000000) {
        header('Location: ../services.php?error=' . urlencode('Image file is too large. Maximum size is 5MB.'));
        exit;
    }
    
    // Generate unique filename
    $imageName = uniqid() . '_' . time() . '.' . $imageFileType;
    $targetFile = $uploadDir . $imageName;
    
    // Simple file upload without image processing
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        // File uploaded successfully
    } else {
        header('Location: ../services.php?error=' . urlencode('Failed to upload image file.'));
        exit;
    }
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO services (title, description, icon, image, status) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $title, $description, $icon, $imageName, $status);

if ($stmt->execute()) {
    // Redirect back to services page with success message
    header('Location: ../services.php?success=1');
    exit;
} else {
    // Delete uploaded image if database insert fails
    if ($imageName && file_exists($uploadDir . $imageName)) {
        unlink($uploadDir . $imageName);
    }
    // Redirect back with error message
    header('Location: ../services.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}

$stmt->close();
$conn->close();
?>
