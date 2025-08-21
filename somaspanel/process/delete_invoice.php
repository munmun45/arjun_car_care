<?php
require_once('../config/config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    try {
        $invoice_id = intval($_GET['id']);
        
        if ($invoice_id <= 0) {
            throw new Exception('Invalid invoice ID.');
        }
        
        // Check if invoice exists
        $check_query = "SELECT id FROM invoices WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $invoice_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Invoice not found.');
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        // Delete invoice items first (due to foreign key constraint)
        $delete_items_query = "DELETE FROM invoice_items WHERE invoice_id = ?";
        $delete_items_stmt = $conn->prepare($delete_items_query);
        $delete_items_stmt->bind_param("i", $invoice_id);
        
        if (!$delete_items_stmt->execute()) {
            throw new Exception('Failed to delete invoice items.');
        }
        
        // Delete invoice
        $delete_invoice_query = "DELETE FROM invoices WHERE id = ?";
        $delete_invoice_stmt = $conn->prepare($delete_invoice_query);
        $delete_invoice_stmt->bind_param("i", $invoice_id);
        
        if (!$delete_invoice_stmt->execute()) {
            throw new Exception('Failed to delete invoice.');
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Invoice deleted successfully.'
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method or missing invoice ID.'
    ]);
}
?>
