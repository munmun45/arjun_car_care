<?php
require_once('../config/config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Create invoices table if it doesn't exist
        $create_invoices_table = "CREATE TABLE IF NOT EXISTS `invoices` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoice_number` varchar(50) NOT NULL UNIQUE,
            `customer_name` varchar(255) NOT NULL,
            `customer_phone` varchar(20) NOT NULL,
            `customer_email` varchar(255),
            `customer_address` text,
            `vehicle_number` varchar(50),
            `due_date` date,
            `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
            `total_gst` decimal(10,2) NOT NULL DEFAULT 0.00,
            `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
            `notes` text,
            `status` enum('draft','sent','paid','cancelled') DEFAULT 'draft',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->query($create_invoices_table);

        // Create invoice_items table if it doesn't exist
        $create_items_table = "CREATE TABLE IF NOT EXISTS `invoice_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `description` varchar(500) NOT NULL,
            `quantity` decimal(10,2) NOT NULL,
            `rate` decimal(10,2) NOT NULL,
            `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
            `amount` decimal(10,2) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->query($create_items_table);

        // Generate unique invoice number
        $invoice_number = 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if invoice number already exists
        $check_invoice = $conn->prepare("SELECT id FROM invoices WHERE invoice_number = ?");
        $check_invoice->bind_param("s", $invoice_number);
        $check_invoice->execute();
        
        // If exists, generate new one
        while ($check_invoice->get_result()->num_rows > 0) {
            $invoice_number = 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $check_invoice->bind_param("s", $invoice_number);
            $check_invoice->execute();
        }

        // Get form data
        $customer_name = trim($_POST['customer_name']);
        $customer_phone = trim($_POST['customer_phone']);
        $customer_email = trim($_POST['customer_email']) ?: null;
        $customer_address = trim($_POST['customer_address']) ?: null;
        $vehicle_number = trim($_POST['vehicle_number']) ?: null;
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $invoice_notes = trim($_POST['invoice_notes']) ?: null;
        $items = $_POST['items'] ?? [];

        // Validate required fields
        if (empty($customer_name) || empty($customer_phone) || empty($items)) {
            throw new Exception('Please fill in all required fields.');
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

        // Insert invoice
        $insert_invoice = $conn->prepare("INSERT INTO invoices (invoice_number, customer_name, customer_phone, customer_email, customer_address, vehicle_number, due_date, subtotal, total_gst, grand_total, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_invoice->bind_param("sssssssddds", $invoice_number, $customer_name, $customer_phone, $customer_email, $customer_address, $vehicle_number, $due_date, $subtotal, $total_gst, $grand_total, $invoice_notes);
        
        if (!$insert_invoice->execute()) {
            throw new Exception('Failed to create invoice.');
        }

        $invoice_id = $conn->insert_id;

        // Insert invoice items
        $insert_item = $conn->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, rate, gst_rate, amount) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($valid_items as $item) {
            $insert_item->bind_param("isdddd", $invoice_id, $item['description'], $item['quantity'], $item['rate'], $item['gst_rate'], $item['amount']);
            if (!$insert_item->execute()) {
                throw new Exception('Failed to add invoice items.');
            }
        }

        // Commit transaction
        $conn->commit();

        // Success response
        $response = [
            'success' => true,
            'message' => 'Invoice created successfully!',
            'invoice_number' => $invoice_number,
            'invoice_id' => $invoice_id
        ];

        // Redirect back with success message
        header("Location: ../invoice.php?success=1&invoice_number=" . urlencode($invoice_number));
        exit();

    } catch (Exception $e) {
        // Rollback transaction
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        
        // Error response
        $error_message = $e->getMessage();
        header("Location: ../invoice.php?error=1&message=" . urlencode($error_message));
        exit();
    }
} else {
    // Invalid request method
    header("Location: ../invoice.php?error=1&message=" . urlencode('Invalid request method.'));
    exit();
}
?>
