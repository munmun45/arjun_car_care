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
            `invoice_date` date,
            `due_date` date,
            `gst_no` varchar(30),
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

        // Ensure missing columns exist if table was created previously
        $dbResult = $conn->query("SELECT DATABASE() as db");
        $dbName = $dbResult && $dbResult->num_rows ? $dbResult->fetch_assoc()['db'] : null;
        if ($dbName) {
            // Check and add invoice_date
            $chk = $conn->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='invoices' AND COLUMN_NAME='invoice_date'");
            $chk->bind_param("s", $dbName);
            $chk->execute();
            $cnt = $chk->get_result()->fetch_assoc()['cnt'] ?? 0;
            if ((int)$cnt === 0) {
                $conn->query("ALTER TABLE `invoices` ADD COLUMN `invoice_date` date AFTER `vehicle_number`");
            }
            // Check and add gst_no
            $chk2 = $conn->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='invoices' AND COLUMN_NAME='gst_no'");
            $chk2->bind_param("s", $dbName);
            $chk2->execute();
            $cnt2 = $chk2->get_result()->fetch_assoc()['cnt'] ?? 0;
            if ((int)$cnt2 === 0) {
                $conn->query("ALTER TABLE `invoices` ADD COLUMN `gst_no` varchar(30) AFTER `due_date`");
            }
        }

        // Create invoice_items table if it doesn't exist
        $create_items_table = "CREATE TABLE IF NOT EXISTS `invoice_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `description` varchar(500) NOT NULL,
            `item_name` varchar(255) DEFAULT NULL,
            `hsn_code` varchar(50) DEFAULT NULL,
            `category` enum('product','service') DEFAULT 'product',
            `mrp` decimal(10,2) DEFAULT NULL,
            `part_number` varchar(100) DEFAULT NULL,
            `quantity` decimal(10,2) NOT NULL,
            `rate` decimal(10,2) NOT NULL,
            `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
            `amount` decimal(10,2) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->query($create_items_table);

        // Ensure new columns exist for existing invoice_items tables
        if ($dbName) {
            $columnsToEnsure = [
                'item_name' => "ALTER TABLE `invoice_items` ADD COLUMN `item_name` varchar(255) DEFAULT NULL AFTER `description`",
                'hsn_code' => "ALTER TABLE `invoice_items` ADD COLUMN `hsn_code` varchar(50) DEFAULT NULL AFTER `item_name`",
                'category' => "ALTER TABLE `invoice_items` ADD COLUMN `category` enum('product','service') DEFAULT 'product' AFTER `hsn_code`",
                'mrp' => "ALTER TABLE `invoice_items` ADD COLUMN `mrp` decimal(10,2) DEFAULT NULL AFTER `category`",
                'part_number' => "ALTER TABLE `invoice_items` ADD COLUMN `part_number` varchar(100) DEFAULT NULL AFTER `mrp`"
            ];
            foreach ($columnsToEnsure as $col => $alterSql) {
                $chkCol = $conn->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='invoice_items' AND COLUMN_NAME=?");
                $chkCol->bind_param("ss", $dbName, $col);
                $chkCol->execute();
                $cntCol = $chkCol->get_result()->fetch_assoc()['cnt'] ?? 0;
                if ((int)$cntCol === 0) {
                    $conn->query($alterSql);
                }
            }
        }

        // Get form data
        $customer_name = trim($_POST['customer_name']);
        $customer_phone = trim($_POST['customer_phone']);
        $customer_email = trim($_POST['customer_email']) ?: null;
        $customer_address = trim($_POST['customer_address']) ?: null;
        $vehicle_number = trim($_POST['vehicle_number']) ?: null;
        $invoice_date = !empty($_POST['invoice_date']) ? $_POST['invoice_date'] : date('Y-m-d');
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $gst_no = isset($_POST['gst_no']) && trim($_POST['gst_no']) !== '' ? trim($_POST['gst_no']) : null;
        $posted_invoice_number = isset($_POST['invoice_number']) ? trim($_POST['invoice_number']) : '';
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
            // Prefer new item_name; fallback to legacy description if provided
            $desc_from_item = isset($item['item_name']) ? trim($item['item_name']) : '';
            $desc_legacy = isset($item['description']) ? trim($item['description']) : '';
            $desc = $desc_from_item !== '' ? $desc_from_item : $desc_legacy;

            if ($desc !== '' && !empty($item['quantity']) && !empty($item['rate'])) {
                $qty = floatval($item['quantity']);
                $rate = floatval($item['rate']);
                $gst_rate = floatval($item['gst_rate']);

                // Optional fields
                $item_name = $desc_from_item !== '' ? $desc_from_item : null;
                $hsn_code = isset($item['hsn_code']) && trim($item['hsn_code']) !== '' ? trim($item['hsn_code']) : null;
                $category = isset($item['category']) && in_array($item['category'], ['product','service']) ? $item['category'] : 'product';
                $mrp = isset($item['mrp']) && $item['mrp'] !== '' ? floatval($item['mrp']) : null;
                $part_number = isset($item['part_number']) && trim($item['part_number']) !== '' ? trim($item['part_number']) : null;
                
                $item_subtotal = $qty * $rate;
                $item_gst = ($item_subtotal * $gst_rate) / 100;
                $item_total = $item_subtotal + $item_gst;
                
                $subtotal += $item_subtotal;
                $total_gst += $item_gst;
                
                $valid_items[] = [
                    'description' => $desc, // map to NOT NULL column
                    'item_name' => $item_name,
                    'hsn_code' => $hsn_code,
                    'category' => $category,
                    'mrp' => $mrp,
                    'part_number' => $part_number,
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

        // Determine invoice number: use posted if provided and unique; else auto-generate unique
        if ($posted_invoice_number !== '') {
            $check_invoice = $conn->prepare("SELECT id FROM invoices WHERE invoice_number = ?");
            $check_invoice->bind_param("s", $posted_invoice_number);
            $check_invoice->execute();
            $res = $check_invoice->get_result();
            if ($res && $res->num_rows > 0) {
                throw new Exception('The provided Invoice Number already exists. Please use a different one or leave it blank to auto-generate.');
            }
            $invoice_number = $posted_invoice_number;
        } else {
            // Auto-generate unique invoice number
            $invoice_number = 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $check_invoice = $conn->prepare("SELECT id FROM invoices WHERE invoice_number = ?");
            $check_invoice->bind_param("s", $invoice_number);
            $check_invoice->execute();
            $res = $check_invoice->get_result();
            while ($res && $res->num_rows > 0) {
                $invoice_number = 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $check_invoice->bind_param("s", $invoice_number);
                $check_invoice->execute();
                $res = $check_invoice->get_result();
            }
        }

        // Start transaction
        $conn->begin_transaction();

        // Insert invoice
        $insert_invoice = $conn->prepare("INSERT INTO invoices (invoice_number, customer_name, customer_phone, customer_email, customer_address, vehicle_number, invoice_date, due_date, gst_no, subtotal, total_gst, grand_total, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_invoice->bind_param("sssssssssddds", $invoice_number, $customer_name, $customer_phone, $customer_email, $customer_address, $vehicle_number, $invoice_date, $due_date, $gst_no, $subtotal, $total_gst, $grand_total, $invoice_notes);
        
        if (!$insert_invoice->execute()) {
            throw new Exception('Failed to create invoice.');
        }

        $invoice_id = $conn->insert_id;

        // Insert invoice items (with new fields)
        $insert_item = $conn->prepare("INSERT INTO invoice_items (invoice_id, description, item_name, hsn_code, category, mrp, part_number, quantity, rate, gst_rate, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($valid_items as $item) {
            $insert_item->bind_param(
                "issssdsdddd",
                $invoice_id,
                $item['description'],
                $item['item_name'],
                $item['hsn_code'],
                $item['category'],
                $item['mrp'],
                $item['part_number'],
                $item['quantity'],
                $item['rate'],
                $item['gst_rate'],
                $item['amount']
            );
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
        // Rollback transaction (mysqli)
        $conn->rollback();
        
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
