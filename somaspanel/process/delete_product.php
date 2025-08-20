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
    header('Location: ../product.php?error=' . urlencode('Invalid product ID'));
    exit;
}

// Get product data to delete associated image
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: ../product.php?error=' . urlencode('Product not found'));
    exit;
}

// Delete the product from database
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Delete associated image file if exists
    if ($product['image']) {
        $imagePath = '../uploads/products/' . $product['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    header('Location: ../product.php?deleted=1');
    exit;
} else {
    header('Location: ../product.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}

$stmt->close();
$conn->close();
?>
