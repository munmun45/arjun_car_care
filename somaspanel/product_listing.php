<?php
// Single-page CRUD for Product Names Only
require_once __DIR__ . '/config/config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS `product_names` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

// Handle form actions (PRG pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $_SESSION['flash_error'] = 'Product name is required.';
            header('Location: product_listing.php');
            exit;
        }
        $stmt = $conn->prepare('INSERT INTO product_names (name) VALUES (?)');
        $stmt->bind_param('s', $name);
        if ($stmt->execute()) { $_SESSION['flash_success'] = 'Product name added.'; } else { $_SESSION['flash_error'] = 'DB error: ' . $conn->error; }
        $stmt->close();
        header('Location: product_listing.php');
        exit;
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($id <= 0 || $name === '') {
            $_SESSION['flash_error'] = 'Valid ID and name are required.';
            header('Location: product_listing.php?edit=' . max(0, $id));
            exit;
        }
        $stmt = $conn->prepare('UPDATE product_names SET name = ? WHERE id = ?');
        $stmt->bind_param('si', $name, $id);
        if ($stmt->execute()) { $_SESSION['flash_success'] = 'Product name updated.'; } else { $_SESSION['flash_error'] = 'DB error: ' . $conn->error; }
        $stmt->close();
        header('Location: product_listing.php');
        exit;
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid ID.';
            header('Location: product_listing.php');
            exit;
        }
        $stmt = $conn->prepare('DELETE FROM product_names WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) { $_SESSION['flash_success'] = 'Product name deleted.'; } else { $_SESSION['flash_error'] = 'DB error: ' . $conn->error; }
        $stmt->close();
        header('Location: product_listing.php');
        exit;
    }
}

// Flash messages
$error = $_SESSION['flash_error'] ?? '';
$success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

// If editing, fetch current row
$editId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$editRow = null;
if ($editId > 0) {
    $stmt = $conn->prepare('SELECT * FROM product_names WHERE id = ?');
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $res = $stmt->get_result();
    $editRow = $res->fetch_assoc();
    $stmt->close();
}

// Fetch all for listing
$rows = [];
$rs = $conn->query('SELECT * FROM product_names ORDER BY id DESC');
if ($rs) { while ($r = $rs->fetch_assoc()) { $rows[] = $r; } $rs->close(); }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?= require("./config/meta.php") ?>
</head>

<body>
  <?= require("./config/header.php") ?>
  <?= require("./config/menu.php") ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Product Listing</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Product Listing</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?= $editRow ? 'Edit Product Name' : 'Add Product Name' ?></h5>

              <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 px-3"><?= htmlspecialchars($error) ?></div>
              <?php elseif (!empty($success)): ?>
                <div class="alert alert-success py-2 px-3"><?= htmlspecialchars($success) ?></div>
              <?php endif; ?>

              <form method="post">
                <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'add' ?>">
                <?php if ($editRow): ?>
                  <input type="hidden" name="id" value="<?= (int)$editRow['id'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                  <label class="form-label">Product/Service Name</label>
                  <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($editRow['name'] ?? '') ?>" required>
                </div>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary btn-sm"><?= $editRow ? 'Update' : 'Add' ?></button>
                  <?php if ($editRow): ?>
                    <a href="product_listing.php" class="btn btn-secondary btn-sm">Cancel</a>
                  <?php endif; ?>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All Product Names</h5>

              <div class="table-responsive">
                <table class="table table-striped table-sm align-middle">
                  <thead>
                    <tr>
                      <th style="width:80px">#ID</th>
                      <th>Name</th>
                      <th style="width:160px" class="text-end">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($rows)): ?>
                      <tr><td colspan="3" class="text-center text-muted">No product names found</td></tr>
                    <?php else: foreach ($rows as $row): ?>
                      <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td class="text-end">
                          <a class="btn btn-outline-primary btn-sm" href="product_listing.php?edit=<?= (int)$row['id'] ?>">Edit</a>
                          <form method="post" style="display:inline-block" onsubmit="return confirm('Delete this product name?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

   
   
  </main><!-- End #main -->

  <?= require("./config/footer.php") ?>

</body>
</html>