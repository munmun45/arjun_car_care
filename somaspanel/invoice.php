

<!DOCTYPE html>
<html lang="en">

<head>
  <?php 
require("./config/meta.php");
require("./config/config.php");

// Handle success/error messages
$success_message = '';
$error_message = '';

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $invoice_number = $_GET['invoice_number'] ?? '';
    $success_message = "Invoice {$invoice_number} created successfully!";
}

if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error_message = $_GET['message'] ?? 'An error occurred.';
}

// Fetch invoices from database
$invoices = [];
// Fetch product names for item selector
$product_names = [];
try {
    $query = "SELECT id, invoice_number, customer_name, customer_phone, grand_total, status, created_at 
              FROM invoices 
              ORDER BY created_at DESC";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $invoices[] = $row;
        }
    }
    // Load product names
    $pnResult = $conn->query("SELECT id, name FROM product_names ORDER BY name ASC");
    if ($pnResult && $pnResult->num_rows > 0) {
        while ($pn = $pnResult->fetch_assoc()) {
            $product_names[] = $pn;
        }
    }
} catch (Exception $e) {
    $error_message = "Error fetching invoices: " . $e->getMessage();
}
?>
</head>

<body>
  <?php require("./config/header.php") ?>
  <?php require("./config/menu.php") ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Invoice Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Invoice Management</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Invoice Management</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                  <i class="bi bi-plus-circle"></i> Create New Invoice
                </button>
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
              
              <!-- Invoice List Table -->
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Invoice #</th>
                      <th>Customer</th>
                      <th>Date</th>
                      <th>Total Amount</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($invoices)): ?>
                      <tr>
                        <td colspan="6" class="text-center text-muted">No invoices found. Create your first invoice!</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($invoices as $invoice): ?>
                        <tr>
                          <td>
                            <strong><?php echo htmlspecialchars($invoice['invoice_number']); ?></strong>
                          </td>
                          <td>
                            <div>
                              <strong><?php echo htmlspecialchars($invoice['customer_name']); ?></strong><br>
                              <small class="text-muted"><?php echo htmlspecialchars($invoice['customer_phone']); ?></small>
                            </div>
                          </td>
                          <td>
                            <?php echo date('d M Y', strtotime($invoice['created_at'])); ?><br>
                            <small class="text-muted"><?php echo date('h:i A', strtotime($invoice['created_at'])); ?></small>
                          </td>
                          <td>
                            <strong>₹<?php echo number_format($invoice['grand_total'], 2); ?></strong>
                          </td>
                          <td>
                            <?php
                            $status_class = '';
                            $status_text = ucfirst($invoice['status']);
                            switch ($invoice['status']) {
                              case 'draft':
                                $status_class = 'bg-secondary';
                                break;
                              case 'sent':
                                $status_class = 'bg-info';
                                break;
                              case 'paid':
                                $status_class = 'bg-success';
                                break;
                              case 'cancelled':
                                $status_class = 'bg-danger';
                                break;
                              default:
                                $status_class = 'bg-secondary';
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                          </td>
                          <td>
                            <div class="btn-group" role="group">
                              <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewInvoice(<?php echo $invoice['id']; ?>)" title="View Invoice">
                                <i class="bi bi-eye"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadInvoice(<?php echo $invoice['id']; ?>)" title="Download PDF">
                                <i class="bi bi-download"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-outline-warning" onclick="editInvoice(<?php echo $invoice['id']; ?>)" title="Edit Invoice">
                                <i class="bi bi-pencil"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteInvoice(<?php echo $invoice['id']; ?>)" title="Delete Invoice">
                                <i class="bi bi-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <style>
      /* Ensure full-screen modal scrolls reliably */
      #invoiceModal .modal-dialog { height: 100vh; }
      #invoiceModal .modal-content { height: 100%; display: flex; flex-direction: column; min-height: 0; }
      #invoiceModal .modal-header, #invoiceModal .modal-footer { flex: 0 0 auto; }
      #invoiceModal form { display: flex; flex-direction: column; min-height: 0; flex: 1 1 auto; }
      #invoiceModal .modal-body { flex: 1 1 auto; overflow-y: auto; min-height: 0; -webkit-overflow-scrolling: touch; }
    </style>

    <!-- Invoice Creation Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content h-100 d-flex flex-column">
          <div class="modal-header">
            <h5 class="modal-title" id="invoiceModalLabel">
              <i class="bi bi-receipt"></i> Create New Invoice
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="invoiceForm" method="post" action="process/create_invoice.php" class="d-flex flex-column flex-grow-1">
            <div class="modal-body flex-grow-1 overflow-auto">
              <!-- Customer Details Section -->
              <div class="row mb-3">
                <div class="col-12">
                  <h6 class="text-primary mb-2"><i class="bi bi-person"></i> Customer Details</h6>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-2">
                    <label for="customerName" class="form-label small mb-1">Customer Name *</label>
                    <input type="text" class="form-control form-control-sm" id="customerName" name="customer_name" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-2">
                    <label for="customerPhone" class="form-label small mb-1">Phone Number *</label>
                    <input type="tel" class="form-control form-control-sm" id="customerPhone" name="customer_phone" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-2">
                    <label for="customerEmail" class="form-label small mb-1">Email</label>
                    <input type="email" class="form-control form-control-sm" id="customerEmail" name="customer_email">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-2">
                    <label for="vehicleNumber" class="form-label small mb-1">Vehicle Number</label>
                    <input type="text" class="form-control form-control-sm" id="vehicleNumber" name="vehicle_number" placeholder="e.g., KA01AB1234">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-2">
                    <label for="invoiceDate" class="form-label small mb-1">Invoice Date</label>
                    <input type="date" class="form-control form-control-sm" id="invoiceDate" name="invoice_date" value="<?php echo date('Y-m-d'); ?>">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-2">
                    <label for="dueDate" class="form-label small mb-1">Due Date</label>
                    <input type="date" class="form-control form-control-sm" id="dueDate" name="due_date" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group mb-2">
                    <label for="gstNo" class="form-label small mb-1">GST No</label>
                    <input type="text" class="form-control form-control-sm" id="gstNo" name="gst_no" placeholder="e.g., 22ABCDE1234F1Z5">
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group mb-2">
                    <label for="customerAddress" class="form-label small mb-1">Address</label>
                    <textarea class="form-control form-control-sm" id="customerAddress" name="customer_address" rows="2"></textarea>
                  </div>
                </div>
              </div>

              <hr>

              <!-- Global datalist with all product names -->
              <datalist id="productNamesList">
                <?php foreach ($product_names as $pn): ?>
                  <option value="<?php echo htmlspecialchars($pn['name']); ?>"></option>
                <?php endforeach; ?>
              </datalist>

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
                          <th width="20%">Item</th>
                          <th width="10%">HSN Code</th>
                          <th width="10%">Category</th>
                          <th width="10%">MRP</th>
                          <th width="10%">Part Number</th>
                          <th width="8%">Quantity</th>
                          <th width="8%">Rate</th>
                          <th width="7%">GST %</th>
                          <th width="10%">Amount</th>
                          <th width="10%">Action</th>
                        </tr>
                      </thead>
                      <tbody id="itemsTableBody">
                        <!-- Dynamic rows will be added here -->
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
              <div class="row">
                <div class="col-12">
                  <div class="form-group mb-3">
                    <label for="invoiceNotes" class="form-label">Notes/Terms & Conditions</label>
                    <textarea class="form-control" id="invoiceNotes" name="invoice_notes" rows="3" placeholder="Additional notes or terms and conditions..."></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Create Invoice
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
    let itemCounter = 0;

    // Add new item row
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addItemRow();
    });

    // Add initial row on page load
    document.addEventListener('DOMContentLoaded', function() {
        addItemRow();
    });

    function addItemRow() {
        itemCounter++;
        const tbody = document.getElementById('itemsTableBody');
        const row = document.createElement('tr');
        row.id = `item-row-${itemCounter}`;
        
        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${itemCounter}][item_name]" placeholder="Item" list="productNamesList" required>
            </td>
            <td>
                <input type="text" class="form-control" name="items[${itemCounter}][hsn_code]" placeholder="HSN/SAC">
            </td>
            <td>
                <select class="form-select" name="items[${itemCounter}][category]">
                    <option value="product">Product</option>
                    <option value="service">Service</option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="items[${itemCounter}][mrp]" placeholder="0.00" min="0" step="0.01">
            </td>
            <td>
                <input type="text" class="form-control" name="items[${itemCounter}][part_number]" placeholder="Part #">
            </td>
            <td>
                <input type="number" class="form-control item-qty" name="items[${itemCounter}][quantity]" value="1" min="1" step="0.01" onchange="calculateRowTotal(${itemCounter})" required>
            </td>
            <td>
                <input type="number" class="form-control item-rate" name="items[${itemCounter}][rate]" placeholder="0.00" min="0" step="0.01" onchange="calculateRowTotal(${itemCounter})" required>
            </td>
            <td>
                <select class="form-select item-gst" name="items[${itemCounter}][gst_rate]" onchange="calculateRowTotal(${itemCounter})" required>
                    <option value="0">0% (No GST)</option>
                    <option value="5">5% GST</option>
                    <option value="12">12% GST</option>
                    <option value="18" selected>18% GST</option>
                    <option value="28">28% GST</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control item-amount" id="amount-${itemCounter}" readonly value="₹0.00">
                <input type="hidden" name="items[${itemCounter}][amount]" id="amount-hidden-${itemCounter}" value="0">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(${itemCounter})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
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
    document.getElementById('invoiceForm').addEventListener('submit', function(e) {
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
            
            if (!itemName.value.trim() || !qty.value || !rate.value) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields for each item.');
            return false;
        }
    });

    // Invoice action functions
    function viewInvoice(invoiceId) {
        // Open invoice view in new window/tab
        window.open(`view_invoice.php?id=${invoiceId}`, '_blank');
    }

    function downloadInvoice(invoiceId) {
        // Download invoice as PDF
        window.open(`download_invoice.php?id=${invoiceId}`, '_blank');
    }

    function editInvoice(invoiceId) {
        // Redirect to edit invoice page
        window.location.href = `edit_invoice.php?id=${invoiceId}`;
    }

    function deleteInvoice(invoiceId) {
        if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
            // Send delete request
            fetch(`process/delete_invoice.php?id=${invoiceId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Invoice deleted successfully!');
                    location.reload();
                } else {
                    alert('Error deleting invoice: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error deleting invoice: ' + error.message);
            });
        }
    }
    </script>

  </main><!-- End #main -->

  <?php require("./config/footer.php") ?>

</body>

</html>