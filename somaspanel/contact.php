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
      <h1>Contact</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Contact</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Contact Information Management</h5>

              <?php
              require_once('./config/config.php');

              // Create table if it doesn't exist
              $create_table_sql = "CREATE TABLE IF NOT EXISTS `contact_info` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `mobile1` varchar(20) NOT NULL,
                `mobile2` varchar(20) NOT NULL,
                `email1` varchar(100) NOT NULL,
                `email2` varchar(100) NOT NULL,
                `address` text NOT NULL,
                `map_embed` text,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
              $conn->query($create_table_sql);

              // Handle form submission
              if ($_POST) {
                $mobile1 = mysqli_real_escape_string($conn, $_POST['mobile1']);
                $mobile2 = mysqli_real_escape_string($conn, $_POST['mobile2']);
                $email1 = mysqli_real_escape_string($conn, $_POST['email1']);
                $email2 = mysqli_real_escape_string($conn, $_POST['email2']);
                $address = mysqli_real_escape_string($conn, $_POST['address']);
                $map_embed = mysqli_real_escape_string($conn, $_POST['map_embed']);

                // Check if contact info exists
                $check_sql = "SELECT id FROM contact_info LIMIT 1";
                $check_result = $conn->query($check_sql);

                if ($check_result->num_rows > 0) {
                  // Update existing record
                  $update_sql = "UPDATE contact_info SET 
                                mobile1 = '$mobile1', 
                                mobile2 = '$mobile2', 
                                email1 = '$email1', 
                                email2 = '$email2', 
                                address = '$address', 
                                map_embed = '$map_embed',
                                updated_at = CURRENT_TIMESTAMP
                                WHERE id = 1";
                  
                  if ($conn->query($update_sql) === TRUE) {
                    echo '<div class="alert alert-success">Contact information updated successfully!</div>';
                  } else {
                    echo '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
                  }
                } else {
                  // Insert new record
                  $insert_sql = "INSERT INTO contact_info (mobile1, mobile2, email1, email2, address, map_embed) 
                                VALUES ('$mobile1', '$mobile2', '$email1', '$email2', '$address', '$map_embed')";
                  
                  if ($conn->query($insert_sql) === TRUE) {
                    echo '<div class="alert alert-success">Contact information saved successfully!</div>';
                  } else {
                    echo '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
                  }
                }
              }

              // Fetch existing contact info
              $result = $conn->query("SELECT * FROM contact_info LIMIT 1");
              $contact = null;
              if ($result && $result->num_rows > 0) {
                $contact = $result->fetch_assoc();
              }
              ?>

              <!-- Contact Information Form -->
              <form method="POST" class="row g-3">
                <div class="col-md-6">
                  <label for="mobile1" class="form-label">Primary Mobile Number</label>
                  <input type="tel" class="form-control" id="mobile1" name="mobile1" 
                         value="<?php echo $contact ? htmlspecialchars($contact['mobile1']) : '+91 '; ?>" 
                         required placeholder="+91 98765 43210">
                </div>
                
                <div class="col-md-6">
                  <label for="mobile2" class="form-label">Secondary Mobile Number</label>
                  <input type="tel" class="form-control" id="mobile2" name="mobile2" 
                         value="<?php echo $contact ? htmlspecialchars($contact['mobile2']) : '+91 '; ?>" 
                         required placeholder="+91 98765 43211">
                </div>

                <div class="col-md-6">
                  <label for="email1" class="form-label">Primary Email</label>
                  <input type="email" class="form-control" id="email1" name="email1" 
                         value="<?php echo $contact ? htmlspecialchars($contact['email1']) : ''; ?>" 
                         required placeholder="info@arjuncarcare.com">
                </div>
                
                <div class="col-md-6">
                  <label for="email2" class="form-label">Secondary Email</label>
                  <input type="email" class="form-control" id="email2" name="email2" 
                         value="<?php echo $contact ? htmlspecialchars($contact['email2']) : ''; ?>" 
                         required placeholder="support@arjuncarcare.com">
                </div>

                <div class="col-12">
                  <label for="address" class="form-label">Business Address</label>
                  <textarea class="form-control" id="address" name="address" rows="3" 
                            required placeholder="Enter complete business address"><?php echo $contact ? htmlspecialchars($contact['address']) : ''; ?></textarea>
                </div>

                <div class="col-12">
                  <label for="map_embed" class="form-label">Google Maps Embed Code</label>
                  <textarea class="form-control" id="map_embed" name="map_embed" rows="4" 
                            placeholder="Paste Google Maps embed iframe code here"><?php echo $contact ? htmlspecialchars($contact['map_embed']) : ''; ?></textarea>
                  <div class="form-text">
                    <strong>How to get Google Maps embed code:</strong><br>
                    1. Go to <a href="https://maps.google.com" target="_blank">Google Maps</a><br>
                    2. Search for your business location<br>
                    3. Click "Share" → "Embed a map"<br>
                    4. Copy the entire iframe code and paste it here
                  </div>
                </div>

                <div class="col-12">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Save Contact Information
                  </button>
                </div>
              </form>

            </div>
          </div>
        </div>

        <!-- Preview Section -->
        <?php if ($contact): ?>
        <div class="col-lg-12 mt-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Contact Information Preview</h5>
              
              <div class="row">
                <div class="col-md-6">
                  <h6><i class="bi bi-telephone"></i> Phone Numbers</h6>
                  <p class="mb-1"><strong>Primary:</strong> <?php echo htmlspecialchars($contact['mobile1']); ?></p>
                  <p class="mb-3"><strong>Secondary:</strong> <?php echo htmlspecialchars($contact['mobile2']); ?></p>
                  
                  <h6><i class="bi bi-envelope"></i> Email Addresses</h6>
                  <p class="mb-1"><strong>Primary:</strong> <?php echo htmlspecialchars($contact['email1']); ?></p>
                  <p class="mb-3"><strong>Secondary:</strong> <?php echo htmlspecialchars($contact['email2']); ?></p>
                  
                  <h6><i class="bi bi-geo-alt"></i> Address</h6>
                  <p><?php echo nl2br(htmlspecialchars($contact['address'])); ?></p>
                </div>
                
                <div class="col-md-6">
                  <h6><i class="bi bi-map"></i> Location Map</h6>
                  <?php if (!empty($contact['map_embed'])): ?>
                    <div class="map-container">
                      <?php echo $contact['map_embed']; ?>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-info">
                      <i class="bi bi-info-circle"></i> No map embed code provided yet.
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              
              <div class="mt-3">
                <small class="text-muted">
                  Last updated: <?php echo date('F j, Y g:i A', strtotime($contact['updated_at'])); ?>
                </small>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </section>

   

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->







  <?= require("./config/footer.php") ?>




</body>

</html>