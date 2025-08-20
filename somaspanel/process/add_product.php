<?php
require_once '../config/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../product.php?error=' . urlencode('Invalid request method'));
    exit;
}

// Check if products table exists, if not create it
$tableCheck = $conn->query("SHOW TABLES LIKE 'products'");
if ($tableCheck->num_rows == 0) {
    $createTable = "
    CREATE TABLE `products` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `image` varchar(255) DEFAULT NULL,
      `star_rating` decimal(2,1) DEFAULT 0.0,
      `off_price` decimal(10,2) DEFAULT NULL,
      `main_price` decimal(10,2) NOT NULL,
      `quantity` int(11) DEFAULT 0,
      `status` enum('active','inactive') DEFAULT 'active',
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    if (!$conn->query($createTable)) {
        header('Location: ../product.php?error=' . urlencode('Failed to create products table: ' . $conn->error));
        exit;
    }
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$star_rating = floatval($_POST['star_rating'] ?? 0);
$off_price = !empty($_POST['off_price']) ? floatval($_POST['off_price']) : null;
$main_price = floatval($_POST['main_price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);
$status = $_POST['status'] ?? 'active';

// Validate required fields
if (empty($title) || empty($description) || $main_price <= 0 || $quantity < 0) {
    header('Location: ../product.php?error=' . urlencode('Title, description, main price, and quantity are required'));
    exit;
}

$imageName = null;

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/products/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowedTypes)) {
        header('Location: ../product.php?error=' . urlencode('Invalid image format. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.'));
        exit;
    }
    
    // Check file size (max 5MB)
    if ($_FILES['image']['size'] > 5000000) {
        header('Location: ../product.php?error=' . urlencode('Image file is too large. Maximum size is 5MB.'));
        exit;
    }
    
    // Generate unique filename
    $imageName = uniqid() . '_' . time() . '.' . $imageFileType;
    $targetFile = $uploadDir . $imageName;
    
    // Simple file upload without image processing
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        header('Location: ../product.php?error=' . urlencode('Failed to upload image file.'));
        exit;
    }
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO products (title, description, image, star_rating, off_price, main_price, quantity, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdddis", $title, $description, $imageName, $star_rating, $off_price, $main_price, $quantity, $status);

if ($stmt->execute()) {
    // Redirect back to products page with success message
    header('Location: ../product.php?success=1');
    exit;
} else {
    // Delete uploaded image if database insert fails
    if ($imageName && file_exists($uploadDir . $imageName)) {
        unlink($uploadDir . $imageName);
    }
    // Redirect back with error message
    header('Location: ../product.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}

$stmt->close();
$conn->close();
?>
