<?php
require_once('../config/config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $invoice_id = intval($_POST['invoice_id']);
        
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

        // Get form data
        $customer_name = trim($_POST['customer_name']);
        $customer_phone = trim($_POST['customer_phone']);
        $customer_email = trim($_POST['customer_email']) ?: null;
        $customer_address = trim($_POST['customer_address']) ?: null;
        $vehicle_number = trim($_POST['vehicle_number']) ?: null;
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $invoice_notes = trim($_POST['invoice_notes']) ?: null;
        $status = trim($_POST['status']);
        $items = $_POST['items'] ?? [];

        // Validate required fields
        if (empty($customer_name) || empty($customer_phone) || empty($items)) {
            throw new Exception('Please fill in all required fields.');
        }

        // Validate status
        $valid_statuses = ['draft', 'sent', 'paid', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            throw new Exception('Invalid status selected.');
        }

        // Calculate totals
        $subtotal = 0;
        $total_gst = 0;
        $valid_items = [];

        foreach ($items as $item) {
            if (!empty($item['description']) && !empty($item['quantity']) && !empty($item['rate'])) {
                $qty = floatval($item['quantity']);
                $rate = floatval($item['rate']);
                $gst_rate = floatval($item['gst_rate']);
                
                $item_subtotal = $qty * $rate;
                $item_gst = ($item_subtotal * $gst_rate) / 100;
                $item_total = $item_subtotal + $item_gst;
                
                $subtotal += $item_subtotal;
                $total_gst += $item_gst;
                
                $valid_items[] = [
                    'id' => isset($item['id']) ? intval($item['id']) : null,
                    'description' => trim($item['description']),
                    'quantity' => $qty,
                    'rate' => $rate,
                    'gst_rate' => $gst_rate,
                    'amount' => $item_total
                ];
            }
        }

        if (empty($valid_items)) {
            throw new Exception('Please add at least one valid item to the invoice.');
        }

        $grand_total = $subtotal + $total_gst;

        // Start transaction
        $conn->begin_transaction();

        // Update invoice
        $update_invoice = $conn->prepare("UPDATE invoices SET customer_name = ?, customer_phone = ?, customer_email = ?, customer_address = ?, vehicle_number = ?, due_date = ?, subtotal = ?, total_gst = ?, grand_total = ?, notes = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $update_invoice->bind_param("ssssssdddssi", $customer_name, $customer_phone, $customer_email, $customer_address, $vehicle_number, $due_date, $subtotal, $total_gst, $grand_total, $invoice_notes, $status, $invoice_id);
        
        if (!$update_invoice->execute()) {
            throw new Exception('Failed to update invoice.');
        }

        // Delete existing items
        $delete_items = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
        $delete_items->bind_param("i", $invoice_id);
        if (!$delete_items->execute()) {
            throw new Exception('Failed to update invoice items.');
        }

        // Insert updated invoice items
        $insert_item = $conn->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, rate, gst_rate, amount) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($valid_items as $item) {
            $insert_item->bind_param("isdddd", $invoice_id, $item['description'], $item['quantity'], $item['rate'], $item['gst_rate'], $item['amount']);
            if (!$insert_item->execute()) {
                throw new Exception('Failed to update invoice items.');
            }
        }

        // Commit transaction
        $conn->commit();

        // Success response
        header("Location: ../edit_invoice.php?id=" . $invoice_id . "&success=1");
        exit();

    } catch (Exception $e) {
        // Rollback transaction
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        
        // Error response
        $error_message = $e->getMessage();
        $invoice_id = isset($_POST['invoice_id']) ? intval($_POST['invoice_id']) : 0;
        header("Location: ../edit_invoice.php?id=" . $invoice_id . "&error=1&message=" . urlencode($error_message));
        exit();
    }
} else {
    // Invalid request method
    header("Location: ../invoice.php?error=1&message=" . urlencode('Invalid request method.'));
    exit();
}
?>
