<?php
require_once '../config/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../product.php?error=' . urlencode('Invalid request method'));
    exit;
}

$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$star_rating = floatval($_POST['star_rating'] ?? 0);
$off_price = !empty($_POST['off_price']) ? floatval($_POST['off_price']) : null;
$main_price = floatval($_POST['main_price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);
$status = $_POST['status'] ?? 'active';

// Validate required fields
if ($id <= 0 || empty($title) || empty($description) || $main_price <= 0 || $quantity < 0) {
    header('Location: ../product.php?error=' . urlencode('ID, title, description, main price, and quantity are required'));
    exit;
}

// Get current product data
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$currentProduct = $result->fetch_assoc();
$stmt->close();

if (!$currentProduct) {
    header('Location: ../product.php?error=' . urlencode('Product not found'));
    exit;
}

$imageName = $currentProduct['image'];

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
    
    // Delete old image if exists
    if ($imageName && file_exists($uploadDir . $imageName)) {
        unlink($uploadDir . $imageName);
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

// Update database
$stmt = $conn->prepare("UPDATE products SET title = ?, description = ?, image = ?, star_rating = ?, off_price = ?, main_price = ?, quantity = ?, status = ? WHERE id = ?");
$stmt->bind_param("sssdddisi", $title, $description, $imageName, $star_rating, $off_price, $main_price, $quantity, $status, $id);

if ($stmt->execute()) {
    // Redirect back to products page with success message
    header('Location: ../product.php?updated=1');
    exit;
} else {
    // Redirect back with error message
    header('Location: ../product.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}

$stmt->close();
$conn->close();
?>
