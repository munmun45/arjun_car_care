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
    header('Location: ../services.php?error=' . urlencode('Invalid service ID'));
    exit;
}

// Get service data to delete associated image
$stmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();
$stmt->close();

if (!$service) {
    header('Location: ../services.php?error=' . urlencode('Service not found'));
    exit;
}

// Delete the service from database
$stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Delete associated image file if exists
    if ($service['image']) {
        $imagePath = '../uploads/services/' . $service['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    header('Location: ../services.php?deleted=1');
    exit;
} else {
    header('Location: ../services.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}

$stmt->close();
$conn->close();
?>
