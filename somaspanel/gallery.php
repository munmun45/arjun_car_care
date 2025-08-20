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
      <h1>Gallery</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Gallery</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Gallery Management</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                  <i class="bi bi-plus-circle"></i> Add Image
                </button>
              </div>

              <?php
              require_once 'config/config.php';
              
              // Display success/error messages
              if (isset($_GET['success'])) {
                  echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                          Image added successfully!
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
              }
              if (isset($_GET['updated'])) {
                  echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                          Image updated successfully!
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
              }
              if (isset($_GET['deleted'])) {
                  echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                          Image deleted successfully!
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
              }
              if (isset($_GET['error'])) {
                  echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                          Error: ' . htmlspecialchars($_GET['error']) . '
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
              }
              ?>

              <!-- Gallery Table -->
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Image</th>
                      <th scope="col">Title</th>
                      <th scope="col">Category</th>
                      <th scope="col">Description</th>
                      <th scope="col">Status</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $query = "SELECT * FROM gallery ORDER BY created_at DESC";
                    $result = $conn->query($query);
                    $counter = 1;
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $counter++ . "</td>";
                            echo "<td>";
                            if ($row['image']) {
                                echo "<img src='uploads/gallery/" . htmlspecialchars($row['image']) . "' alt='Gallery Image' style='width: 80px; height: 60px; object-fit: cover; border-radius: 4px;'>";
                            } else {
                                echo "<span class='text-muted'>No image</span>";
                            }
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td><span class='badge bg-info'>" . htmlspecialchars($row['category']) . "</span></td>";
                            echo "<td>" . htmlspecialchars(substr($row['description'] ?? '', 0, 80)) . "...</td>";
                            echo "<td>";
                            if ($row['status'] == 'active') {
                                echo "<span class='badge bg-success'>Active</span>";
                            } else {
                                echo "<span class='badge bg-danger'>Inactive</span>";
                            }
                            echo "</td>";
                            echo "<td>";
                            echo "<button class='btn btn-sm btn-outline-primary me-1' onclick='editGallery(" . $row['id'] . ")' title='Edit'>";
                            echo "<i class='bi bi-pencil'></i>";
                            echo "</button>";
                            echo "<a href='process/delete_gallery.php?id=" . $row['id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this image?\")' title='Delete'>";
                            echo "<i class='bi bi-trash'></i>";
                            echo "</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No images found</td></tr>";
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

    <!-- Add Gallery Modal -->
    <div class="modal fade" id="addGalleryModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Image</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form method="POST" action="process/add_gallery.php" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" class="form-control" name="title" required>
                  </div>
                  <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" name="category">
                      <option value="general">General</option>
                      <option value="services">Services</option>
                      <option value="products">Products</option>
                      <option value="workshop">Workshop</option>
                      <option value="events">Events</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status">
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="image" class="form-label">Image *</label>
                    <input type="file" class="form-control" name="image" accept="image/*" required onchange="previewImage(this, 'addPreview')">
                    <small class="text-muted">Max size: 5MB. Formats: JPG, PNG, GIF, WEBP</small>
                  </div>
                  <div class="mb-3">
                    <img id="addPreview" src="#" alt="Preview" style="display: none; width: 100%; max-height: 200px; object-fit: cover; border-radius: 4px;">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Add Image</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Gallery Modal -->
    <div class="modal fade" id="editGalleryModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Image</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form method="POST" action="process/edit_gallery.php" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editId">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="editTitle" class="form-label">Title *</label>
                    <input type="text" class="form-control" name="title" id="editTitle" required>
                  </div>
                  <div class="mb-3">
                    <label for="editCategory" class="form-label">Category</label>
                    <select class="form-select" name="category" id="editCategory">
                      <option value="general">General</option>
                      <option value="services">Services</option>
                      <option value="products">Products</option>
                      <option value="workshop">Workshop</option>
                      <option value="events">Events</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="editStatus" class="form-label">Status</label>
                    <select class="form-select" name="status" id="editStatus">
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="editDescription" class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="editImage" class="form-label">Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*" onchange="previewImage(this, 'editPreview')">
                    <small class="text-muted">Leave empty to keep current image</small>
                  </div>
                  <div class="mb-3">
                    <img id="editPreview" src="#" alt="Preview" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 4px;">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Image</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function editGallery(id) {
        fetch('process/get_gallery.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const gallery = data.gallery;
                    document.getElementById('editId').value = gallery.id;
                    document.getElementById('editTitle').value = gallery.title;
                    document.getElementById('editCategory').value = gallery.category;
                    document.getElementById('editStatus').value = gallery.status;
                    document.getElementById('editDescription').value = gallery.description || '';
                    
                    const preview = document.getElementById('editPreview');
                    if (gallery.image) {
                        preview.src = 'uploads/gallery/' + gallery.image;
                        preview.style.display = 'block';
                    } else {
                        preview.style.display = 'none';
                    }
                    
                    new bootstrap.Modal(document.getElementById('editGalleryModal')).show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading the gallery data.');
            });
    }
    </script>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->







  <?= require("./config/footer.php") ?>




</body>

</html>