<?php
header('Content-Type: application/json');
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid gallery ID']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM gallery WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$gallery = $result->fetch_assoc();

if ($gallery) {
    echo json_encode(['success' => true, 'gallery' => $gallery]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gallery item not found']);
}

$stmt->close();
$conn->close();
?>
