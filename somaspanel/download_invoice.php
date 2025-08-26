<?php
require_once('./config/config.php');

// Get invoice ID from URL
$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($invoice_id <= 0) {
    header("Location: invoice.php?error=1&message=" . urlencode('Invalid invoice ID.'));
    exit();
}

// Fetch invoice details
try {
    $invoice_query = "SELECT * FROM invoices WHERE id = ?";
    $invoice_stmt = $conn->prepare($invoice_query);
    $invoice_stmt->bind_param("i", $invoice_id);
    $invoice_stmt->execute();
    $invoice_result = $invoice_stmt->get_result();
    
    if ($invoice_result->num_rows === 0) {
        header("Location: invoice.php?error=1&message=" . urlencode('Invoice not found.'));
        exit();
    }
    
    $invoice = $invoice_result->fetch_assoc();
    
    // Fetch invoice items
    $items_query = "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id";
    $items_stmt = $conn->prepare($items_query);
    $items_stmt->bind_param("i", $invoice_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $items = [];
    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }
    
} catch (Exception $e) {
    header("Location: invoice.php?error=1&message=" . urlencode('Error fetching invoice: ' . $e->getMessage()));
    exit();
}

// Set headers for PDF download
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: inline; filename="Invoice_' . $invoice['invoice_number'] . '.pdf"');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            margin: -20px -20px 30px -20px;
            border-radius: 0;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #667eea;
            font-weight: bold;
            margin-right: 20px;
        }
        
        .company-details h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .company-details p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .invoice-meta {
            text-align: right;
        }
        
        .invoice-meta h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .invoice-meta h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-draft { background: #6c757d; }
        .status-sent { background: #17a2b8; }
        .status-paid { background: #28a745; }
        .status-cancelled { background: #dc3545; }
        
        .details-section {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
        }
        
        .details-box {
            width: 48%;
            padding: 20px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
        }
        
        .details-box h4 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .details-box strong {
            display: block;
            margin-bottom: 5px;
        }
        
        .invoice-dates {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
        }
        
        .date-item {
            text-align: center;
        }
        
        .date-item strong {
            display: block;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table th {
            background: #667eea;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .items-table .text-center { text-align: center; }
        .items-table .text-right { text-align: right; }
        
        .totals-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .notes-section {
            width: 60%;
        }
        
        .notes-section h4 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .totals-box {
            width: 35%;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .grand-total {
            background: #667eea;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-top: 15px;
        }
        
        .grand-total .amount {
            font-size: 20px;
            font-weight: bold;
        }
        
        .invoice-footer {
            background: #343a40;
            color: white;
            padding: 20px;
            margin: 30px -20px -20px -20px;
            text-align: center;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-left, .footer-right {
            font-size: 11px;
        }
        
        @media print {
            body { margin: 0; }
            .invoice-container { padding: 0; }
            .invoice-header { margin: 0 0 30px 0; }
            .invoice-footer { margin: 30px 0 0 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div class="company-info">
                    <div class="company-logo">ACC</div>
                    <div class="company-details">
                        <h1>Arjun Car Care</h1>
                        <p>Professional Auto Care Services</p>
                    </div>
                </div>
                <div class="invoice-meta">
                    <h2>INVOICE</h2>
                    <h3><?php echo htmlspecialchars($invoice['invoice_number']); ?></h3>
                    <?php
                    $status_class = 'status-' . $invoice['status'];
                    ?>
                    <span class="status-badge <?php echo $status_class; ?>">
                        <?php echo ucfirst($invoice['status']); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Company and Customer Details -->
        <div class="details-section">
            <div class="details-box">
                <h4>FROM</h4>
                <strong>Arjun Car Care</strong>
                Professional Auto Care Services<br>
                Phone: +91 98765 43210<br>
                Email: info@arjuncarcare.com<br>
                GST: 29ABCDE1234F1Z5
            </div>
            <div class="details-box">
                <h4>BILL TO</h4>
                <strong><?php echo htmlspecialchars($invoice['customer_name']); ?></strong>
                Phone: <?php echo htmlspecialchars($invoice['customer_phone']); ?><br>
                <?php if ($invoice['customer_email']): ?>
                    Email: <?php echo htmlspecialchars($invoice['customer_email']); ?><br>
                <?php endif; ?>
                <?php if ($invoice['vehicle_number']): ?>
                    Vehicle: <?php echo htmlspecialchars($invoice['vehicle_number']); ?><br>
                <?php endif; ?>
                <?php if (!empty($invoice['gst_no'])): ?>
                    GST No: <?php echo htmlspecialchars($invoice['gst_no']); ?><br>
                <?php endif; ?>
                <?php if ($invoice['customer_address']): ?>
                    <?php echo nl2br(htmlspecialchars($invoice['customer_address'])); ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Invoice Dates -->
        <div class="invoice-dates">
            <div class="date-item">
                <strong>Invoice Date</strong>
                <?php echo date('d M Y', strtotime($invoice['invoice_date'] ?: $invoice['created_at'])); ?>
            </div>
            <div class="date-item">
                <strong>Due Date</strong>
                <?php 
                if ($invoice['due_date']) {
                    echo date('d M Y', strtotime($invoice['due_date']));
                } else {
                    $baseDate = $invoice['invoice_date'] ?: $invoice['created_at'];
                    echo date('d M Y', strtotime($baseDate . ' +30 days'));
                }
                ?>
            </div>
            <div class="date-item">
                <strong>Payment Terms</strong>
                <?php 
                if ($invoice['due_date']) {
                    $baseDate = $invoice['invoice_date'] ?: $invoice['created_at'];
                    $days = ceil((strtotime($invoice['due_date']) - strtotime($baseDate)) / (60 * 60 * 24));
                    echo "Net {$days} Days";
                } else {
                    echo "Net 30 Days";
                }
                ?>
            </div>
        </div>

        <!-- Invoice Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="40%">Description</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Rate</th>
                    <th width="10%" class="text-center">GST %</th>
                    <th width="20%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><strong><?php echo htmlspecialchars($item['description']); ?></strong></td>
                    <td class="text-center"><?php echo number_format($item['quantity'], 2); ?></td>
                    <td class="text-right">₹<?php echo number_format($item['rate'], 2); ?></td>
                    <td class="text-center"><?php echo number_format($item['gst_rate'], 0); ?>%</td>
                    <td class="text-right">₹<?php echo number_format($item['amount'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals and Notes -->
        <div class="totals-section">
            <div class="notes-section">
                <?php if ($invoice['notes']): ?>
                <h4>Notes & Terms</h4>
                <p><?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></p>
                <?php endif; ?>
            </div>
            <div class="totals-box">
                <div class="total-row">
                    <span><strong>Subtotal:</strong></span>
                    <span>₹<?php echo number_format($invoice['subtotal'], 2); ?></span>
                </div>
                <div class="total-row">
                    <span><strong>Total GST:</strong></span>
                    <span>₹<?php echo number_format($invoice['total_gst'], 2); ?></span>
                </div>
                <div class="grand-total">
                    <div>TOTAL AMOUNT</div>
                    <div class="amount">₹<?php echo number_format($invoice['grand_total'], 2); ?></div>
                </div>
            </div>
        </div>

        <!-- Invoice Footer -->
        <div class="invoice-footer">
            <div class="footer-content">
                <div class="footer-left">
                    <strong>Payment Methods:</strong><br>
                    Cash, UPI, Card, Bank Transfer
                </div>
                <div class="footer-right">
                    Thank you for choosing Arjun Car Care!<br>
                    Generated on <?php echo date('d M Y, h:i A'); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print when page loads for download
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
