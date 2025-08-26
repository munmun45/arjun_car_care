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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?> - Arjun Car Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .invoice-container { box-shadow: none !important; }
            body { background: white !important; }
        }
        
        .invoice-container {
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #667eea;
            font-weight: bold;
        }
        
        .invoice-details {
            background: #f8f9fa;
            padding: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        
        .items-table {
            margin: 0;
        }
        
        .items-table th {
            background: #667eea;
            color: white;
            border: none;
            padding: 1rem;
        }
        
        .items-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .total-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .grand-total {
            background: #667eea;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .invoice-footer {
            background: #343a40;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body style="background: #f4f6f9;">
    <div class="container-fluid py-4">
        <!-- Action Buttons -->
        <div class="row mb-4 no-print">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="invoice.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Invoices
                    </a>
                    <div>
                        <button onclick="window.print()" class="btn btn-primary me-2">
                            <i class="bi bi-printer"></i> Print Invoice
                        </button>
                        <a href="edit_invoice.php?id=<?php echo $invoice['id']; ?>" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Container -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="invoice-container">
                    <!-- Invoice Header -->
                    <div class="invoice-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="company-logo me-3">
                                        ACC
                                    </div>
                                    <div>
                                        <h2 class="mb-1">Arjun Car Care</h2>
                                        <p class="mb-0">Professional Auto Care Services</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h1 class="mb-2">INVOICE</h1>
                                <h4 class="mb-1"><?php echo htmlspecialchars($invoice['invoice_number']); ?></h4>
                                <?php
                                $status_class = '';
                                switch ($invoice['status']) {
                                    case 'draft': $status_class = 'bg-secondary'; break;
                                    case 'sent': $status_class = 'bg-info'; break;
                                    case 'paid': $status_class = 'bg-success'; break;
                                    case 'cancelled': $status_class = 'bg-danger'; break;
                                    default: $status_class = 'bg-secondary';
                                }
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo ucfirst($invoice['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Details -->
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="invoice-details">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-building"></i> From
                                </h5>
                                <strong>Arjun Car Care</strong><br>
                                Professional Auto Care Services<br>
                                Phone: +91 98765 43210<br>
                                Email: info@arjuncarcare.com<br>
                                GST: 29ABCDE1234F1Z5
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="invoice-details">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-person"></i> Bill To
                                </h5>
                                <strong><?php echo htmlspecialchars($invoice['customer_name']); ?></strong><br>
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
                    </div>

                    <!-- Invoice Meta -->
                    <div class="row g-0">
                        <div class="col-12">
                            <div class="invoice-details">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Invoice Date:</strong><br>
                                        <?php echo date('d M Y', strtotime($invoice['invoice_date'] ?: $invoice['created_at'])); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Due Date:</strong><br>
                                        <?php 
                                        if ($invoice['due_date']) {
                                            echo date('d M Y', strtotime($invoice['due_date']));
                                        } else {
                                            $baseDate = $invoice['invoice_date'] ?: $invoice['created_at'];
                                            echo date('d M Y', strtotime($baseDate . ' +30 days'));
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Payment Terms:</strong><br>
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
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="table-responsive">
                        <table class="table items-table mb-0">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">Description</th>
                                    <th width="10%" class="text-center">Qty</th>
                                    <th width="15%" class="text-end">Rate</th>
                                    <th width="10%" class="text-center">GST %</th>
                                    <th width="20%" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $index => $item): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['description']); ?></strong>
                                    </td>
                                    <td class="text-center"><?php echo number_format($item['quantity'], 2); ?></td>
                                    <td class="text-end">₹<?php echo number_format($item['rate'], 2); ?></td>
                                    <td class="text-center"><?php echo number_format($item['gst_rate'], 0); ?>%</td>
                                    <td class="text-end">₹<?php echo number_format($item['amount'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Section -->
                    <div class="row g-0">
                        <div class="col-md-6">
                            <?php if ($invoice['notes']): ?>
                            <div class="p-4">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-file-text"></i> Notes & Terms
                                </h6>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="total-section m-4">
                                <div class="row mb-2">
                                    <div class="col-6"><strong>Subtotal:</strong></div>
                                    <div class="col-6 text-end">₹<?php echo number_format($invoice['subtotal'], 2); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6"><strong>Total GST:</strong></div>
                                    <div class="col-6 text-end">₹<?php echo number_format($invoice['total_gst'], 2); ?></div>
                                </div>
                                <div class="grand-total text-center">
                                    <div>TOTAL AMOUNT</div>
                                    <div style="font-size: 1.5rem;">₹<?php echo number_format($invoice['grand_total'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Footer -->
                    <div class="invoice-footer">
                        <div class="row">
                            <div class="col-md-6 text-md-start">
                                <small>
                                    <strong>Payment Methods:</strong><br>
                                    Cash, UPI, Card, Bank Transfer
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small>
                                    Thank you for choosing Arjun Car Care!<br>
                                    Generated on <?php echo date('d M Y, h:i A'); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
