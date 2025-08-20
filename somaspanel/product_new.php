<!DOCTYPE html>
<html lang="en">

<head>
  <?= require("./config/meta.php") ?>
  <!-- Cropper.js CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
</head>

<body>
  <?= require("./config/header.php") ?>
  <?= require("./config/menu.php") ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Products</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Products</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <?php
          // Display success/error messages
          if (isset($_GET['success'])) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="bi bi-check-circle me-1"></i>
                      Product added successfully!
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
          if (isset($_GET['updated'])) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="bi bi-check-circle me-1"></i>
                      Product updated successfully!
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
          if (isset($_GET['deleted'])) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="bi bi-check-circle me-1"></i>
                      Product deleted successfully!
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
          if (isset($_GET['error'])) {
              echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <i class="bi bi-exclamation-triangle me-1"></i>
                      Error: ' . htmlspecialchars($_GET['error']) . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
          ?>

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Products Management</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                  <i class="bi bi-plus-circle"></i> Add Product
                </button>
              </div>

              <!-- Products Table -->
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Image</th>
                      <th scope="col">Title</th>
                      <th scope="col">Rating</th>
                      <th scope="col">Price</th>
                      <th scope="col">Quantity</th>
                      <th scope="col">Status</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="productsTableBody">
                    <?php
                    require_once './config/config.php';
                    
                    // Check if products table exists, if not create it
                    $tableCheck = $conn->query("SHOW TABLES LIKE 'products'");
                    if ($tableCheck->num_rows == 0) {
                        $createTable = "
                        CREATE TABLE `products` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `title` varchar(255) NOT NULL,
                          `description` text NOT NULL,
                          `image` varchar(255) DEFAULT NULL,
                          `star_rating` decimal(2,1) DEFAULT 0.0,
                          `off_price` decimal(10,2) DEFAULT NULL,
                          `main_price` decimal(10,2) NOT NULL,
                          `quantity` int(11) DEFAULT 0,
                          `status` enum('active','inactive') DEFAULT 'active',
                          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                        ";
                        $conn->query($createTable);
                    }
                    
                    $sql = "SELECT * FROM products ORDER BY id DESC";
                    $result = $conn->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        $counter = 1;
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $counter++ . "</td>";
                            echo "<td>";
                            if ($row['image']) {
                                echo "<img src='uploads/products/" . htmlspecialchars($row['image']) . "' alt='Product Image' style='width: 60px; height: 60px; object-fit: cover; border-radius: 4px;'>";
                            } else {
                                echo "<span class='text-muted'>No image</span>";
                            }
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>";
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $row['star_rating']) {
                                    echo "<i class='bi bi-star-fill text-warning'></i>";
                                } else {
                                    echo "<i class='bi bi-star text-muted'></i>";
                                }
                            }
                            echo " (" . $row['star_rating'] . ")";
                            echo "</td>";
                            echo "<td>";
                            if ($row['off_price'] && $row['off_price'] > 0) {
                                echo "<span class='text-decoration-line-through text-muted'>₹" . number_format($row['off_price'], 2) . "</span><br>";
                            }
                            echo "<strong>₹" . number_format($row['main_price'], 2) . "</strong>";
                            echo "</td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>";
                            if ($row['status'] == 'active') {
                                echo "<span class='badge bg-success'>Active</span>";
                            } else {
                                echo "<span class='badge bg-danger'>Inactive</span>";
                            }
                            echo "</td>";
                            echo "<td>";
                            echo "<button class='btn btn-sm btn-outline-primary me-1' onclick='editProduct(" . $row['id'] . ")' title='Edit'>";
                            echo "<i class='bi bi-pencil'></i>";
                            echo "</button>";
                            echo "<a href='process/delete_product.php?id=" . $row['id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this product?\")' title='Delete'>";
                            echo "<i class='bi bi-trash'></i>";
                            echo "</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>No products found</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form method="POST" action="process/add_product.php" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="productTitle" class="form-label">Title *</label>
                    <input type="text" class="form-control" name="title" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="productRating" class="form-label">Star Rating</label>
                    <select class="form-select" name="star_rating">
                      <option value="0">0 Stars</option>
                      <option value="1">1 Star</option>
                      <option value="2">2 Stars</option>
                      <option value="3">3 Stars</option>
                      <option value="4">4 Stars</option>
                      <option value="5" selected>5 Stars</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Description *</label>
                <textarea class="form-control" name="description" rows="4" required></textarea>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Product Image (1:1 ratio)</label>
                <input type="file" class="form-control" name="image" accept="image/*">
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Off Price (₹)</label>
                    <input type="number" step="0.01" class="form-control" name="off_price" placeholder="0.00">
                    <small class="text-muted">Original price (optional)</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Main Price (₹) *</label>
                    <input type="number" step="0.01" class="form-control" name="main_price" required placeholder="0.00">
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Available Quantity *</label>
                    <input type="number" class="form-control" name="quantity" required placeholder="0">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form method="POST" action="process/edit_product.php" enctype="multipart/form-data">
            <input type="hidden" id="editProductId" name="id">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" class="form-control" id="editProductTitle" name="title" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Star Rating</label>
                    <select class="form-select" id="editProductRating" name="star_rating">
                      <option value="0">0 Stars</option>
                      <option value="1">1 Star</option>
                      <option value="2">2 Stars</option>
                      <option value="3">3 Stars</option>
                      <option value="4">4 Stars</option>
                      <option value="5">5 Stars</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Description *</label>
                <textarea class="form-control" id="editProductDescription" name="description" rows="4" required></textarea>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Product Image (1:1 ratio)</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <div id="editImagePreview" class="mt-2">
                  <img id="editPreviewImg" src="" alt="Current Image" style="max-width: 200px; height: 200px; object-fit: cover;">
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Off Price (₹)</label>
                    <input type="number" step="0.01" class="form-control" id="editProductOffPrice" name="off_price" placeholder="0.00">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Main Price (₹) *</label>
                    <input type="number" step="0.01" class="form-control" id="editProductMainPrice" name="main_price" required placeholder="0.00">
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Available Quantity *</label>
                    <input type="number" class="form-control" id="editProductQuantity" name="quantity" required placeholder="0">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="editProductStatus" name="status">
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </main><!-- End #main -->

  <?= require("./config/footer.php") ?>

  <script>
    // Edit product function
    function editProduct(id) {
      fetch('process/get_product.php?id=' + id)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('editProductId').value = data.product.id;
            document.getElementById('editProductTitle').value = data.product.title;
            document.getElementById('editProductRating').value = data.product.star_rating;
            document.getElementById('editProductDescription').value = data.product.description;
            document.getElementById('editProductOffPrice').value = data.product.off_price || '';
            document.getElementById('editProductMainPrice').value = data.product.main_price;
            document.getElementById('editProductQuantity').value = data.product.quantity;
            document.getElementById('editProductStatus').value = data.product.status;
            
            if (data.product.image) {
              document.getElementById('editPreviewImg').src = 'uploads/products/' + data.product.image;
              document.getElementById('editImagePreview').style.display = 'block';
            } else {
              document.getElementById('editImagePreview').style.display = 'none';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
            modal.show();
          } else {
            alert('Error loading product data: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while loading the product data.');
        });
    }
  </script>

</body>
</html>
