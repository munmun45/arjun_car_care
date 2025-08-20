<?php
header('Content-Type: application/json');
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$icon = trim($_POST['icon'] ?? '');
$status = $_POST['status'] ?? 'active';

// Validate required fields
if ($id <= 0 || empty($title) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'ID, title and description are required']);
    exit;
}

// Get current service data
$stmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$currentService = $result->fetch_assoc();
$stmt->close();

if (!$currentService) {
    echo json_encode(['success' => false, 'message' => 'Service not found']);
    exit;
}

$imageName = $currentService['image'];

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
    
    // Delete old image if exists
    if ($imageName && file_exists($uploadDir . $imageName)) {
        unlink($uploadDir . $imageName);
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

// Update database
$stmt = $conn->prepare("UPDATE services SET title = ?, description = ?, icon = ?, image = ?, status = ? WHERE id = ?");
$stmt->bind_param("sssssi", $title, $description, $icon, $imageName, $status, $id);

if ($stmt->execute()) {
    // Redirect back to services page with success message
    header('Location: ../services.php?updated=1');
    exit;
} else {
    // Redirect back with error message
    header('Location: ../services.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}

$stmt->close();
$conn->close();
?>
