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

// Handle success/error messages
$success_message = '';
$error_message = '';

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Invoice updated successfully!";
}

if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error_message = $_GET['message'] ?? 'An error occurred.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require("./config/meta.php"); ?>
    <title>Edit Invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
</head>

<body>
    <?php require("./config/header.php") ?>
    <?php require("./config/menu.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Edit Invoice</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="invoice.php">Invoice Management</a></li>
                    <li class="breadcrumb-item active">Edit Invoice</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title">Edit Invoice: <?php echo htmlspecialchars($invoice['invoice_number']); ?></h5>
                                <div>
                                    <a href="view_invoice.php?id=<?php echo $invoice['id']; ?>" class="btn btn-info me-2" target="_blank">
                                        <i class="bi bi-eye"></i> View Invoice
                                    </a>
                                    <a href="invoice.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>

                            <!-- Success/Error Messages -->
                            <?php if (!empty($success_message)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form id="editInvoiceForm" method="post" action="process/update_invoice.php">
                                <input type="hidden" name="invoice_id" value="<?php echo $invoice['id']; ?>">
                                
                                <!-- Customer Details Section -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="bi bi-person"></i> Customer Details</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="customerName" class="form-label">Customer Name *</label>
                                            <input type="text" class="form-control" id="customerName" name="customer_name" value="<?php echo htmlspecialchars($invoice['customer_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="customerPhone" class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control" id="customerPhone" name="customer_phone" value="<?php echo htmlspecialchars($invoice['customer_phone']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="customerEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="customerEmail" name="customer_email" value="<?php echo htmlspecialchars($invoice['customer_email']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="vehicleNumber" class="form-label">Vehicle Number</label>
                                            <input type="text" class="form-control" id="vehicleNumber" name="vehicle_number" value="<?php echo htmlspecialchars($invoice['vehicle_number']); ?>" placeholder="e.g., KA01AB1234">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="invoiceDate" class="form-label">Invoice Date</label>
                                            <input type="date" class="form-control" id="invoiceDate" name="invoice_date" value="<?php echo !empty($invoice['invoice_date']) ? htmlspecialchars($invoice['invoice_date']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="dueDate" class="form-label">Due Date</label>
                                            <input type="date" class="form-control" id="dueDate" name="due_date" value="<?php echo $invoice['due_date'] ? $invoice['due_date'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="gstNo" class="form-label">GST No</label>
                                            <input type="text" class="form-control" id="gstNo" name="gst_no" value="<?php echo htmlspecialchars($invoice['gst_no'] ?? ''); ?>" placeholder="e.g., 22ABCDE1234F1Z5">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label for="customerAddress" class="form-label">Address</label>
                                            <textarea class="form-control" id="customerAddress" name="customer_address" rows="2"><?php echo htmlspecialchars($invoice['customer_address']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Invoice Items Section -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-primary mb-0"><i class="bi bi-list-ul"></i> Invoice Items</h6>
                                            <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                                                <i class="bi bi-plus"></i> Add Item
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="itemsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="18%">Item *</th>
                                                        <th width="8%">HSN Code</th>
                                                        <th width="10%">Category</th>
                                                        <th width="8%">MRP</th>
                                                        <th width="10%">Part No.</th>
                                                        <th width="8%">Quantity</th>
                                                        <th width="10%">Rate</th>
                                                        <th width="8%">GST %</th>
                                                        <th width="10%">Amount</th>
                                                        <th width="10%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemsTableBody">
                                                    <!-- Existing items will be loaded here -->
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-info">
                                                        <td colspan="8" class="text-end fw-bold">Subtotal:</td>
                                                        <td class="fw-bold" id="subtotalAmount">₹0.00</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr class="table-info">
                                                        <td colspan="8" class="text-end fw-bold">Total GST:</td>
                                                        <td class="fw-bold" id="totalGST">₹0.00</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr class="table-success">
                                                        <td colspan="8" class="text-end fw-bold">Grand Total:</td>
                                                        <td class="fw-bold" id="grandTotal">₹0.00</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Invoice Notes -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label for="invoiceNotes" class="form-label">Notes/Terms & Conditions</label>
                                            <textarea class="form-control" id="invoiceNotes" name="invoice_notes" rows="3" placeholder="Additional notes or terms and conditions..."><?php echo htmlspecialchars($invoice['notes']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Invoice
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
    let itemCounter = 0;
    const existingItems = <?php echo json_encode($items); ?>;

    // Load existing items on page load
    document.addEventListener('DOMContentLoaded', function() {
        existingItems.forEach(item => {
            addItemRow(item);
        });
        calculateGrandTotal();
    });

    // Add new item row
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addItemRow();
    });

    function addItemRow(existingItem = null) {
        itemCounter++;
        const tbody = document.getElementById('itemsTableBody');
        const row = document.createElement('tr');
        row.id = `item-row-${itemCounter}`;

        const itemName = existingItem ? (existingItem.item_name || existingItem.description || '') : '';
        const hsnCode = existingItem ? (existingItem.hsn_code || '') : '';
        const category = existingItem ? (existingItem.category || '') : '';
        const mrp = existingItem && existingItem.mrp != null ? existingItem.mrp : '';
        const partNumber = existingItem ? (existingItem.part_number || '') : '';
        const quantity = existingItem ? existingItem.quantity : '1';
        const rate = existingItem ? existingItem.rate : '';
        const gstRate = existingItem ? existingItem.gst_rate : '18';
        const amount = existingItem ? existingItem.amount : '0';

        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${itemCounter}][item_name]" value="${itemName}" placeholder="Item name" required>
                ${existingItem ? `<input type=\"hidden\" name=\"items[${itemCounter}][id]\" value=\"${existingItem.id}\">` : ''}
            </td>
            <td>
                <input type="text" class="form-control" name="items[${itemCounter}][hsn_code]" value="${hsnCode}" placeholder="HSN">
            </td>
            <td>
                <select class="form-select" name="items[${itemCounter}][category]">
                    <option value="">Select</option>
                    <option value="Product" ${category==='Product' ? 'selected' : ''}>Product</option>
                    <option value="Service" ${category==='Service' ? 'selected' : ''}>Service</option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="items[${itemCounter}][mrp]" value="${mrp}" placeholder="0.00" min="0" step="0.01">
            </td>
            <td>
                <input type="text" class="form-control" name="items[${itemCounter}][part_number]" value="${partNumber}" placeholder="Part No.">
            </td>
            <td>
                <input type="number" class="form-control item-qty" name="items[${itemCounter}][quantity]" value="${quantity}" min="1" step="0.01" onchange="calculateRowTotal(${itemCounter})" required>
            </td>
            <td>
                <input type="number" class="form-control item-rate" name="items[${itemCounter}][rate]" value="${rate}" placeholder="0.00" min="0" step="0.01" onchange="calculateRowTotal(${itemCounter})" required>
            </td>
            <td>
                <select class="form-select item-gst" name="items[${itemCounter}][gst_rate]" onchange="calculateRowTotal(${itemCounter})" required>
                    <option value="0" ${gstRate == 0 ? 'selected' : ''}>0% (No GST)</option>
                    <option value="5" ${gstRate == 5 ? 'selected' : ''}>5% GST</option>
                    <option value="12" ${gstRate == 12 ? 'selected' : ''}>12% GST</option>
                    <option value="18" ${gstRate == 18 ? 'selected' : ''}>18% GST</option>
                    <option value="28" ${gstRate == 28 ? 'selected' : ''}>28% GST</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control item-amount" id="amount-${itemCounter}" readonly value="₹${parseFloat(amount).toFixed(2)}">
                <input type="hidden" name="items[${itemCounter}][amount]" id="amount-hidden-${itemCounter}" value="${amount}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(${itemCounter})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);

        if (existingItem) {
            calculateRowTotal(itemCounter);
        }
    }

    function removeItemRow(itemId) {
        const row = document.getElementById(`item-row-${itemId}`);
        if (row) {
            row.remove();
            calculateGrandTotal();
        }
    }

    function calculateRowTotal(itemId) {
        const qtyInput = document.querySelector(`#item-row-${itemId} .item-qty`);
        const rateInput = document.querySelector(`#item-row-${itemId} .item-rate`);
        const gstSelect = document.querySelector(`#item-row-${itemId} .item-gst`);
        const amountDisplay = document.getElementById(`amount-${itemId}`);
        const amountHidden = document.getElementById(`amount-hidden-${itemId}`);
        
        const qty = parseFloat(qtyInput.value) || 0;
        const rate = parseFloat(rateInput.value) || 0;
        const gstRate = parseFloat(gstSelect.value) || 0;
        
        const subtotal = qty * rate;
        const gstAmount = (subtotal * gstRate) / 100;
        const total = subtotal + gstAmount;
        
        amountDisplay.value = `₹${total.toFixed(2)}`;
        amountHidden.value = total.toFixed(2);
        
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let subtotal = 0;
        let totalGST = 0;
        
        document.querySelectorAll('#itemsTableBody tr').forEach(row => {
            const qtyInput = row.querySelector('.item-qty');
            const rateInput = row.querySelector('.item-rate');
            const gstSelect = row.querySelector('.item-gst');
            
            if (qtyInput && rateInput && gstSelect) {
                const qty = parseFloat(qtyInput.value) || 0;
                const rate = parseFloat(rateInput.value) || 0;
                const gstRate = parseFloat(gstSelect.value) || 0;
                
                const itemSubtotal = qty * rate;
                const itemGST = (itemSubtotal * gstRate) / 100;
                
                subtotal += itemSubtotal;
                totalGST += itemGST;
            }
        });
        
        const grandTotal = subtotal + totalGST;
        
        document.getElementById('subtotalAmount').textContent = `₹${subtotal.toFixed(2)}`;
        document.getElementById('totalGST').textContent = `₹${totalGST.toFixed(2)}`;
        document.getElementById('grandTotal').textContent = `₹${grandTotal.toFixed(2)}`;
    }

    // Form validation
    document.getElementById('editInvoiceForm').addEventListener('submit', function(e) {
        const itemRows = document.querySelectorAll('#itemsTableBody tr');
        if (itemRows.length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the invoice.');
            return false;
        }
        
        // Validate that all items have required fields
        let isValid = true;
        itemRows.forEach(row => {
            const itemName = row.querySelector('input[name*="[item_name]"]');
            const qty = row.querySelector('.item-qty');
            const rate = row.querySelector('.item-rate');
            
            if (!itemName || !itemName.value.trim() || !qty.value || !rate.value) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields for each item.');
            return false;
        }
    });
    </script>

    <?php require("./config/footer.php") ?>
</body>
</html>
