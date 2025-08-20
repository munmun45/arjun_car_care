<?php
header('Content-Type: application/json');
require_once './config/config.php';

// Test database connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if services table exists
$result = $conn->query("SHOW TABLES LIKE 'services'");
if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Services table does not exist. Please run the SQL file to create it.']);
    exit;
}

// Check table structure
$result = $conn->query("DESCRIBE services");
$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

echo json_encode([
    'success' => true, 
    'message' => 'Database connection successful',
    'table_exists' => true,
    'columns' => $columns
]);

$conn->close();
?>
