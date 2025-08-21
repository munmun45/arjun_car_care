<?php
require_once('./config/config.php');

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['booking_id']) && isset($_POST['new_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    
    $update_query = "UPDATE bookings SET status = '$new_status', updated_at = CURRENT_TIMESTAMP WHERE id = $booking_id";
    
    if ($conn->query($update_query)) {
        $success_message = "Booking status updated successfully!";
    } else {
        $error_message = "Error updating status: " . $conn->error;
    }
}

// Handle booking deletion
if (isset($_POST['delete_booking']) && isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];
    
    $delete_query = "DELETE FROM bookings WHERE id = $booking_id";
    
    if ($conn->query($delete_query)) {
        $success_message = "Booking deleted successfully!";
    } else {
        $error_message = "Error deleting booking: " . $conn->error;
    }
}

// Fetch all bookings
$bookings_query = "SELECT * FROM bookings ORDER BY created_at DESC";
$bookings_result = $conn->query($bookings_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require("./config/meta.php") ?>
</head>

<body>
  <?php require("./config/header.php") ?>
  <?php require("./config/menu.php") ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Booking Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Booking Management</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          
          <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo $success_message; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>
          
          <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $error_message; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <!-- Compact Statistics -->
          <div class="row mb-4">
            <?php
            $stats_query = "SELECT 
                              COUNT(*) as total,
                              SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                              SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                              SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                              SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                            FROM bookings";
            $stats_result = $conn->query($stats_query);
            $stats = $stats_result->fetch_assoc();
            ?>
            
            <div class="col-6 col-md-3">
              <div class="card text-center " style="margin-bottom: 0px;" >
                <div class="card-body p-2">
                  <h6 class="mb-1"><?php echo $stats['total']; ?></h6>
                  <small class="text-muted">Total</small>
                </div>
              </div>
            </div>

            <div class="col-6 col-md-3">
              <div class="card text-center " style="margin-bottom: 0px;" >
                <div class="card-body p-2">
                  <h6 class="mb-1 text-warning"><?php echo $stats['pending']; ?></h6>
                  <small class="text-muted">Pending</small>
                </div>
              </div>
            </div>

            <div class="col-6 col-md-3">
              <div class="card text-center " style="margin-bottom: 0px;" >
                <div class="card-body p-2">
                  <h6 class="mb-1 text-info"><?php echo $stats['confirmed']; ?></h6>
                  <small class="text-muted">Confirmed</small>
                </div>
              </div>
            </div>

            <div class="col-6 col-md-3">
              <div class="card text-center " style="margin-bottom: 0px;" >
                <div class="card-body p-2">
                  <h6 class="mb-1 text-success"><?php echo $stats['completed']; ?></h6>
                  <small class="text-muted">Completed</small>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All Bookings</h5>

              <!-- Compact Table -->
              <div class="table-responsive">
                <table class="table table-hover table-sm">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Service & Customer</th>
                      <th>Vehicle</th>
                      <th>Contact</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($bookings_result && $bookings_result->num_rows > 0): ?>
                      <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                        <tr>
                          <td><small><?php echo $booking['id']; ?></small></td>
                          <td>
                            <div><strong><?php echo htmlspecialchars($booking['service_name']); ?></strong></div>
                            <small class="text-muted"><?php echo htmlspecialchars($booking['owner_name']); ?></small>
                          </td>
                          <td>
                            <small><?php echo htmlspecialchars($booking['vehicle_brand']); ?> <?php echo htmlspecialchars($booking['vehicle_model']); ?></small>
                          </td>
                          <td>
                            <small><?php echo htmlspecialchars($booking['mobile_no']); ?></small>
                          </td>
                          <td>
                            <small><?php echo date('M d', strtotime($booking['booking_date'])); ?><br><?php echo date('h:i A', strtotime($booking['booking_time'])); ?></small>
                          </td>
                          <td>
                            <?php
                            $status_class = '';
                            switch ($booking['status']) {
                                case 'pending': $status_class = 'bg-warning text-dark'; break;
                                case 'confirmed': $status_class = 'bg-info text-white'; break;
                                case 'completed': $status_class = 'bg-success text-white'; break;
                                case 'cancelled': $status_class = 'bg-danger text-white'; break;
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?> small"><?php echo ucfirst($booking['status']); ?></span>
                          </td>
                          <td>
                            <div class="btn-group btn-group-sm">
                              <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-gear"></i>
                              </button>
                              <ul class="dropdown-menu">
                                <li><form method="POST" class="d-inline">
                                  <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                  <input type="hidden" name="new_status" value="pending">
                                  <button type="submit" name="update_status" class="dropdown-item"><i class="bi bi-clock text-warning"></i> Pending</button>
                                </form></li>
                                <li><form method="POST" class="d-inline">
                                  <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                  <input type="hidden" name="new_status" value="confirmed">
                                  <button type="submit" name="update_status" class="dropdown-item"><i class="bi bi-check-circle text-info"></i> Confirmed</button>
                                </form></li>
                                <li><form method="POST" class="d-inline">
                                  <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                  <input type="hidden" name="new_status" value="completed">
                                  <button type="submit" name="update_status" class="dropdown-item"><i class="bi bi-check-all text-success"></i> Completed</button>
                                </form></li>
                                <li><form method="POST" class="d-inline">
                                  <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                  <input type="hidden" name="new_status" value="cancelled">
                                  <button type="submit" name="update_status" class="dropdown-item"><i class="bi bi-x-circle text-danger"></i> Cancelled</button>
                                </form></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><form method="POST" class="d-inline" onsubmit="return confirm('Delete this booking?')">
                                  <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                  <button type="submit" name="delete_booking" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Delete</button>
                                </form></li>
                              </ul>
                            </div>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="7" class="text-center py-4">
                          <i class="bi bi-calendar-x text-muted"></i>
                          <div class="text-muted">No bookings found</div>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <!-- End Table with hoverable rows -->

            </div>
          </div>


        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <?php require("./config/footer.php") ?>

</body>

</html>