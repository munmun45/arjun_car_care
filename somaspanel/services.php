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
      <h1>Services</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Services</li>
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
                      Service added successfully!
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
          if (isset($_GET['updated'])) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="bi bi-check-circle me-1"></i>
                      Service updated successfully!
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
          }
          if (isset($_GET['deleted'])) {
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="bi bi-check-circle me-1"></i>
                      Service deleted successfully!
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
                <h5 class="card-title">Services Management</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                  <i class="bi bi-plus-circle"></i> Add Service
                </button>
              </div>

              <!-- Services Table -->
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Image</th>
                      <th scope="col">Icon</th>
                      <th scope="col">Title</th>
                      <th scope="col">Description</th>
                      <th scope="col">Status</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="servicesTableBody">
                    <!-- Services will be loaded here via PHP -->
                    <?php
                    require_once './config/config.php';
                    
                    $sql = "SELECT * FROM services ORDER BY id DESC";
                    $result = $conn->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        $counter = 1;
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $counter++ . "</td>";
                            echo "<td>";
                            if ($row['image']) {
                                echo "<img src='uploads/services/" . htmlspecialchars($row['image']) . "' alt='Service Image' style='width: 60px; height: 34px; object-fit: cover; border-radius: 4px;'>";
                            } else {
                                echo "<span class='text-muted'>No image</span>";
                            }
                            echo "</td>";
                            echo "<td>";
                            if ($row['icon']) {
                                echo "<i class='" . htmlspecialchars($row['icon']) . "' style='font-size: 24px;'></i>";
                            } else {
                                echo "<span class='text-muted'>No icon</span>";
                            }
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>" . htmlspecialchars(substr($row['description'], 0, 100)) . "...</td>";
                            echo "<td>";
                            if ($row['status'] == 'active') {
                                echo "<span class='badge bg-success'>Active</span>";
                            } else {
                                echo "<span class='badge bg-danger'>Inactive</span>";
                            }
                            echo "</td>";
                            echo "<td>";
                            echo "<button class='btn btn-sm btn-outline-primary me-1' onclick='editService(" . $row['id'] . ")' title='Edit'>";
                            echo "<i class='bi bi-pencil'></i>";
                            echo "</button>";
                            echo "<a href='process/delete_service.php?id=" . $row['id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this service?\")' title='Delete'>";
                            echo "<i class='bi bi-trash'></i>";
                            echo "</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No services found</td></tr>";
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

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Service</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form id="addServiceForm" method="POST" action="process/add_service.php" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="serviceTitle" class="form-label">Title *</label>
                    <input type="text" class="form-control" id="serviceTitle" name="title" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="serviceIcon" class="form-label">Icon</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="serviceIcon" name="icon" placeholder="e.g., bi bi-gear">
                      <button type="button" class="btn btn-outline-secondary" onclick="showIconPicker()">
                        <i class="bi bi-palette"></i>
                      </button>
                    </div>
                    <small class="text-muted">Use Bootstrap Icons (e.g., bi bi-gear, bi bi-car-front)</small>
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="serviceDescription" class="form-label">Description *</label>
                <textarea class="form-control" id="serviceDescription" name="description" rows="4" required></textarea>
              </div>
              
              <div class="mb-3">
                <label for="serviceImage" class="form-label">Service Image (16:9 ratio)</label>
                <input type="file" class="form-control" id="serviceImage" name="image" accept="image/*">
                <div id="imagePreview" class="mt-2" style="display: none;">
                  <div class="crop-container" style="max-width: 100%; height: 400px;">
                    <img id="previewImg" src="" alt="Preview" style="max-width: 100%; display: block;">
                  </div>
                  <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success" id="cropImageBtn">
                      <i class="bi bi-crop"></i> Crop Image
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="resetCropBtn">
                      <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                  </div>
                  <canvas id="cropCanvas" style="display: none;"></canvas>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="serviceStatus" class="form-label">Status</label>
                <select class="form-select" id="serviceStatus" name="status">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Add Service</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Service</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form id="editServiceForm" method="POST" action="process/edit_service.php" enctype="multipart/form-data">
            <input type="hidden" id="editServiceId" name="id">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="editServiceTitle" class="form-label">Title *</label>
                    <input type="text" class="form-control" id="editServiceTitle" name="title" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="editServiceIcon" class="form-label">Icon</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="editServiceIcon" name="icon" placeholder="e.g., bi bi-gear">
                      <button type="button" class="btn btn-outline-secondary" onclick="showIconPicker('edit')">
                        <i class="bi bi-palette"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="editServiceDescription" class="form-label">Description *</label>
                <textarea class="form-control" id="editServiceDescription" name="description" rows="4" required></textarea>
              </div>
              
              <div class="mb-3">
                <label for="editServiceImage" class="form-label">Service Image (16:9 ratio)</label>
                <input type="file" class="form-control" id="editServiceImage" name="image" accept="image/*">
                <div id="editImagePreview" class="mt-2">
                  <img id="editPreviewImg" src="" alt="Current Image" style="max-width: 300px; height: auto;">
                </div>
                <div id="editImageCropPreview" class="mt-2" style="display: none;">
                  <div class="crop-container" style="max-width: 100%; height: 400px;">
                    <img id="editCropImg" src="" alt="Preview" style="max-width: 100%; display: block;">
                  </div>
                  <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success" id="editCropImageBtn">
                      <i class="bi bi-crop"></i> Crop Image
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="editResetCropBtn">
                      <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                  </div>
                  <canvas id="editCropCanvas" style="display: none;"></canvas>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="editServiceStatus" class="form-label">Status</label>
                <select class="form-select" id="editServiceStatus" name="status">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Service</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Icon Picker Modal -->
    <div class="modal fade" id="iconPickerModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Choose Icon</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row" id="iconGrid">
              <!-- Popular Bootstrap Icons for services -->
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-gear">
                  <i class="bi bi-gear" style="font-size: 24px;"></i><br>
                  <small>Gear</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-car-front">
                  <i class="bi bi-car-front" style="font-size: 24px;"></i><br>
                  <small>Car</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-tools">
                  <i class="bi bi-tools" style="font-size: 24px;"></i><br>
                  <small>Tools</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-wrench">
                  <i class="bi bi-wrench" style="font-size: 24px;"></i><br>
                  <small>Wrench</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-shield-check">
                  <i class="bi bi-shield-check" style="font-size: 24px;"></i><br>
                  <small>Shield</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-speedometer2">
                  <i class="bi bi-speedometer2" style="font-size: 24px;"></i><br>
                  <small>Speed</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-droplet">
                  <i class="bi bi-droplet" style="font-size: 24px;"></i><br>
                  <small>Oil</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-battery">
                  <i class="bi bi-battery" style="font-size: 24px;"></i><br>
                  <small>Battery</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-lightning">
                  <i class="bi bi-lightning" style="font-size: 24px;"></i><br>
                  <small>Electric</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-thermometer">
                  <i class="bi bi-thermometer" style="font-size: 24px;"></i><br>
                  <small>Temp</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-brush">
                  <i class="bi bi-brush" style="font-size: 24px;"></i><br>
                  <small>Wash</small>
                </button>
              </div>
              <div class="col-2 text-center mb-3">
                <button type="button" class="btn btn-outline-secondary icon-btn" data-icon="bi bi-star">
                  <i class="bi bi-star" style="font-size: 24px;"></i><br>
                  <small>Premium</small>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->







  <?= require("./config/footer.php") ?>

  <!-- Cropper.js JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
  
  <script>
    let currentIconMode = 'add';
    let cropper = null;
    let editCropper = null;
    let croppedImageData = null;
    let editCroppedImageData = null;
    
    // Image preview and crop functionality
    function handleImageUpload(input, previewId, isEdit = false) {
      const file = input.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          if (isEdit) {
            // Hide current image preview and show crop preview
            document.getElementById('editImagePreview').style.display = 'none';
            document.getElementById('editImageCropPreview').style.display = 'block';
            
            const img = document.getElementById('editCropImg');
            img.src = e.target.result;
            
            // Initialize cropper for edit
            if (editCropper) {
              editCropper.destroy();
            }
            editCropper = new Cropper(img, {
              aspectRatio: 16 / 9,
              viewMode: 1,
              autoCropArea: 1,
              responsive: true,
              restore: false,
              guides: true,
              center: true,
              highlight: false,
              cropBoxMovable: true,
              cropBoxResizable: true,
              toggleDragModeOnDblclick: false,
            });
          } else {
            const img = document.getElementById(previewId);
            img.src = e.target.result;
            img.parentElement.parentElement.style.display = 'block';
            
            // Initialize cropper for add
            if (cropper) {
              cropper.destroy();
            }
            cropper = new Cropper(img, {
              aspectRatio: 16 / 9,
              viewMode: 1,
              autoCropArea: 1,
              responsive: true,
              restore: false,
              guides: true,
              center: true,
              highlight: false,
              cropBoxMovable: true,
              cropBoxResizable: true,
              toggleDragModeOnDblclick: false,
            });
          }
        };
        reader.readAsDataURL(file);
      }
    }

    // Crop image function
    function cropImage(isEdit = false) {
      const currentCropper = isEdit ? editCropper : cropper;
      const canvasId = isEdit ? 'editCropCanvas' : 'cropCanvas';
      
      if (currentCropper) {
        const canvas = currentCropper.getCroppedCanvas({
          width: 800,
          height: 450,
          imageSmoothingEnabled: true,
          imageSmoothingQuality: 'high',
        });
        
        // Store the cropped image data
        canvas.toBlob(function(blob) {
          if (isEdit) {
            editCroppedImageData = blob;
          } else {
            croppedImageData = blob;
          }
        }, 'image/jpeg', 0.9);
        
        // Show preview of cropped image
        const croppedCanvas = document.getElementById(canvasId);
        croppedCanvas.style.display = 'block';
        croppedCanvas.width = 300;
        croppedCanvas.height = 169;
        
        const ctx = croppedCanvas.getContext('2d');
        ctx.drawImage(canvas, 0, 0, 300, 169);
        
        // Hide the cropper
        currentCropper.destroy();
        if (isEdit) {
          editCropper = null;
        } else {
          cropper = null;
        }
      }
    }

    // Reset crop function
    function resetCrop(isEdit = false) {
      if (isEdit) {
        if (editCropper) {
          editCropper.reset();
        }
        editCroppedImageData = null;
        document.getElementById('editCropCanvas').style.display = 'none';
      } else {
        if (cropper) {
          cropper.reset();
        }
        croppedImageData = null;
        document.getElementById('cropCanvas').style.display = 'none';
      }
    }

    // Icon picker functionality
    function showIconPicker(mode = 'add') {
      currentIconMode = mode;
      const modal = new bootstrap.Modal(document.getElementById('iconPickerModal'));
      modal.show();
    }

    // Handle icon selection
    document.addEventListener('DOMContentLoaded', function() {
      // Icon picker event listeners
      document.querySelectorAll('.icon-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const icon = this.getAttribute('data-icon');
          if (currentIconMode === 'add') {
            document.getElementById('serviceIcon').value = icon;
          } else {
            document.getElementById('editServiceIcon').value = icon;
          }
          bootstrap.Modal.getInstance(document.getElementById('iconPickerModal')).hide();
        });
      });

      // Image upload handlers
      document.getElementById('serviceImage').addEventListener('change', function() {
        handleImageUpload(this, 'previewImg', false);
      });

      document.getElementById('editServiceImage').addEventListener('change', function() {
        handleImageUpload(this, 'editCropImg', true);
      });

      // Crop button handlers
      document.getElementById('cropImageBtn').addEventListener('click', function() {
        cropImage(false);
      });

      document.getElementById('resetCropBtn').addEventListener('click', function() {
        resetCrop(false);
      });

      document.getElementById('editCropImageBtn').addEventListener('click', function() {
        cropImage(true);
      });

      document.getElementById('editResetCropBtn').addEventListener('click', function() {
        resetCrop(true);
      });

      // Forms will now submit normally without AJAX
    });

    // Edit service function
    function editService(id) {
      fetch('process/get_service.php?id=' + id)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('editServiceId').value = data.service.id;
            document.getElementById('editServiceTitle').value = data.service.title;
            document.getElementById('editServiceIcon').value = data.service.icon || '';
            document.getElementById('editServiceDescription').value = data.service.description;
            document.getElementById('editServiceStatus').value = data.service.status;
            
            if (data.service.image) {
              document.getElementById('editPreviewImg').src = 'uploads/services/' + data.service.image;
              document.getElementById('editImagePreview').style.display = 'block';
            } else {
              document.getElementById('editImagePreview').style.display = 'none';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('editServiceModal'));
            modal.show();
          } else {
            alert('Error loading service data: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while loading the service data.');
        });
    }

    // Delete service function
    function deleteService(id) {
      if (confirm('Are you sure you want to delete this service?')) {
        fetch('process/delete_service.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({id: id})
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Service deleted successfully!');
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the service.');
        });
      }
    }
  </script>




</body>

</html>