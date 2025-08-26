<?php
require_once('../config/config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $invoice_id = intval($_POST['invoice_id']);
        
        if ($invoice_id <= 0) {
            throw new Exception('Invalid invoice ID.');
        }
        
        // Check if invoice exists and get current status
        $check_query = "SELECT id, status FROM invoices WHERE id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $invoice_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Invoice not found.');
        }
        $existing = $result->fetch_assoc();

        // Get form data
        $customer_name = trim($_POST['customer_name']);
        $customer_phone = trim($_POST['customer_phone']);
        $customer_email = trim($_POST['customer_email']) ?: null;
        $customer_address = trim($_POST['customer_address']) ?: null;
        $vehicle_number = trim($_POST['vehicle_number']) ?: null;
        $invoice_date = !empty($_POST['invoice_date']) ? $_POST['invoice_date'] : null;
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $gst_no = isset($_POST['gst_no']) && trim($_POST['gst_no']) !== '' ? trim($_POST['gst_no']) : null;
        $invoice_notes = trim($_POST['invoice_notes']) ?: null;
        $posted_status = isset($_POST['status']) ? trim($_POST['status']) : '';
        $items = $_POST['items'] ?? [];

        // Validate required fields
        if (empty($customer_name) || empty($customer_phone) || empty($items)) {
            throw new Exception('Please fill in all required fields.');
        }

        // Determine and validate status
        $valid_statuses = ['draft', 'sent', 'paid', 'cancelled'];
        $status = $posted_status !== '' ? $posted_status : ($existing['status'] ?? 'draft');
        if (!in_array($status, $valid_statuses)) {
            throw new Exception('Invalid status selected.');
        }

        // Ensure invoice_items table has new columns (best-effort, ignore errors if already exist)
        $conn->query("ALTER TABLE invoice_items ADD COLUMN IF NOT EXISTS item_name VARCHAR(255) NULL");
        $conn->query("ALTER TABLE invoice_items ADD COLUMN IF NOT EXISTS hsn_code VARCHAR(50) NULL");
        $conn->query("ALTER TABLE invoice_items ADD COLUMN IF NOT EXISTS category VARCHAR(20) NULL");
        $conn->query("ALTER TABLE invoice_items ADD COLUMN IF NOT EXISTS mrp DECIMAL(10,2) NULL");
        $conn->query("ALTER TABLE invoice_items ADD COLUMN IF NOT EXISTS part_number VARCHAR(100) NULL");

        // Calculate totals
        $subtotal = 0;
        $total_gst = 0;
        $valid_items = [];

        foreach ($items as $item) {
            $item_name = isset($item['item_name']) ? trim($item['item_name']) : '';
            $legacy_desc = isset($item['description']) ? trim($item['description']) : '';
            $final_desc = $item_name !== '' ? $item_name : $legacy_desc; // Backward compatibility

            if (!empty($final_desc) && !empty($item['quantity']) && !empty($item['rate'])) {
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
                    'description' => $final_desc,
                    'item_name' => $item_name !== '' ? $item_name : null,
                    'hsn_code' => isset($item['hsn_code']) && $item['hsn_code'] !== '' ? trim($item['hsn_code']) : null,
                    'category' => isset($item['category']) && $item['category'] !== '' ? trim($item['category']) : null,
                    'mrp' => isset($item['mrp']) && $item['mrp'] !== '' ? floatval($item['mrp']) : null,
                    'part_number' => isset($item['part_number']) && $item['part_number'] !== '' ? trim($item['part_number']) : null,
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
        $update_invoice = $conn->prepare("UPDATE invoices SET customer_name = ?, customer_phone = ?, customer_email = ?, customer_address = ?, vehicle_number = ?, invoice_date = ?, due_date = ?, gst_no = ?, subtotal = ?, total_gst = ?, grand_total = ?, notes = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $update_invoice->bind_param("ssssssssdddssi", $customer_name, $customer_phone, $customer_email, $customer_address, $vehicle_number, $invoice_date, $due_date, $gst_no, $subtotal, $total_gst, $grand_total, $invoice_notes, $status, $invoice_id);
        
        if (!$update_invoice->execute()) {
            throw new Exception('Failed to update invoice.');
        }

        // Delete existing items
        $delete_items = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
        $delete_items->bind_param("i", $invoice_id);
        if (!$delete_items->execute()) {
            throw new Exception('Failed to update invoice items.');
        }

        // Insert updated invoice items with new fields
        $insert_sql = "INSERT INTO invoice_items (invoice_id, description, item_name, hsn_code, category, part_number, mrp, quantity, rate, gst_rate, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insert_item = $conn->prepare($insert_sql);

        foreach ($valid_items as $item) {
            $desc = $item['description'];
            $iname = $item['item_name'];
            $hsn = $item['hsn_code'];
            $cat = $item['category'];
            $part = $item['part_number'];
            $mrp = $item['mrp'];
            $qty = $item['quantity'];
            $rate = $item['rate'];
            $gst = $item['gst_rate'];
            $amt = $item['amount'];
            $insert_item->bind_param("isssssddddd", $invoice_id, $desc, $iname, $hsn, $cat, $part, $mrp, $qty, $rate, $gst, $amt);
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
        // Rollback transaction (mysqli)
        $conn->rollback();
        
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
