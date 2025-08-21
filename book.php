<!DOCTYPE html>
<html lang="en">

<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
	
<?php require("./config/meta.php") ?>
	
</head>
<body id="bg"><div id="loading-area"></div>
<div class="page-wraper">

    <?php require("./config/header.php") ?>

    <div class="page-content">
        <!-- inner page banner -->
        <div class="dlab-bnr-inr overlay-black-middle" style="background-image:url(images/background/bg4.jpg);">
            <div class="container">
                <div class="dlab-bnr-inr-entry">
                    <h1 class="text-white">Book Service</h1>
                </div>
            </div>
        </div>
        <!-- inner page banner END -->
        <!-- Breadcrumb row -->
        <div class="breadcrumb-row">
            <div class="container">
                <ul class="list-inline">
                    <li><a href="index.php">Home</a></li>
                    <li>Book Service</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb row END -->
        
        <?php
        // Include database connection
        require_once('./somaspanel/config/config.php');
        
        // Create tables if they don't exist
        $create_bookings_table = "CREATE TABLE IF NOT EXISTS `bookings` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `service_id` int(11) NOT NULL,
          `service_name` varchar(255) NOT NULL,
          `vehicle_brand` varchar(100) NOT NULL,
          `vehicle_model` varchar(100) NOT NULL,
          `owner_name` varchar(100) NOT NULL,
          `mobile_no` varchar(20) NOT NULL,
          `email_id` varchar(100) NOT NULL,
          `booking_date` date NOT NULL,
          `booking_time` time NOT NULL,
          `additional_notes` text,
          `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `service_id` (`service_id`),
          KEY `booking_date` (`booking_date`),
          KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->query($create_bookings_table);
        
        $create_brands_table = "CREATE TABLE IF NOT EXISTS `vehicle_brands` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `brand_name` varchar(100) NOT NULL,
          `is_active` tinyint(1) DEFAULT 1,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->query($create_brands_table);
        
        // Insert default brands if table is empty
        $check_brands = $conn->query("SELECT COUNT(*) as count FROM vehicle_brands");
        if ($check_brands && $check_brands->fetch_assoc()['count'] == 0) {
            $brands = ['Maruti Suzuki', 'Hyundai', 'Tata', 'Mahindra', 'Honda', 'Toyota', 'Ford', 'Renault', 'Nissan', 'Volkswagen', 'Skoda', 'BMW', 'Mercedes-Benz', 'Audi', 'Kia', 'MG Motor'];
            foreach ($brands as $brand) {
                $conn->query("INSERT INTO vehicle_brands (brand_name) VALUES ('$brand')");
            }
        }
        
        $success_message = '';
        $error_message = '';
        
        // Handle form submission
        if ($_POST) {
            $service_id = mysqli_real_escape_string($conn, $_POST['service_id']);
            $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
            $vehicle_brand = mysqli_real_escape_string($conn, $_POST['vehicle_brand']);
            $vehicle_model = mysqli_real_escape_string($conn, $_POST['vehicle_model']);
            $owner_name = mysqli_real_escape_string($conn, $_POST['owner_name']);
            $mobile_no = mysqli_real_escape_string($conn, $_POST['mobile_no']);
            $email_id = mysqli_real_escape_string($conn, $_POST['email_id']);
            $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
            $booking_time = mysqli_real_escape_string($conn, $_POST['booking_time']);
            $additional_notes = mysqli_real_escape_string($conn, $_POST['additional_notes']);
            
            // Insert booking into database
            $insert_sql = "INSERT INTO bookings (service_id, service_name, vehicle_brand, vehicle_model, owner_name, mobile_no, email_id, booking_date, booking_time, additional_notes) 
                          VALUES ('$service_id', '$service_name', '$vehicle_brand', '$vehicle_model', '$owner_name', '$mobile_no', '$email_id', '$booking_date', '$booking_time', '$additional_notes')";
            
            if ($conn->query($insert_sql) === TRUE) {
                $booking_id = $conn->insert_id;
                
                // Send email notification
                require_once('./email/email.php');
                
                $email_body = "
                <h2>New Service Booking - Arjun Car Care</h2>
                <p><strong>Booking ID:</strong> #$booking_id</p>
                <p><strong>Service:</strong> $service_name</p>
                <p><strong>Customer Name:</strong> $owner_name</p>
                <p><strong>Mobile:</strong> $mobile_no</p>
                <p><strong>Email:</strong> $email_id</p>
                <p><strong>Vehicle:</strong> $vehicle_brand $vehicle_model</p>
                <p><strong>Booking Date:</strong> $booking_date</p>
                <p><strong>Booking Time:</strong> $booking_time</p>
                <p><strong>Additional Notes:</strong> $additional_notes</p>
                <hr>
                <p>Please contact the customer to confirm the booking.</p>
                ";
                
                $customer_email_body = "
                <h2>Booking Confirmation - Arjun Car Care</h2>
                <p>Dear $owner_name,</p>
                <p>Thank you for booking our service. Your booking details are:</p>
                <p><strong>Booking ID:</strong> #$booking_id</p>
                <p><strong>Service:</strong> $service_name</p>
                <p><strong>Vehicle:</strong> $vehicle_brand $vehicle_model</p>
                <p><strong>Date:</strong> $booking_date</p>
                <p><strong>Time:</strong> $booking_time</p>
                <p>We will contact you shortly to confirm your appointment.</p>
                <p>Thank you for choosing Arjun Car Care!</p>
                ";
                
                // Send emails
                sendBookingEmail('Arjun Car Care', $email_body);
                sendBookingEmail($owner_name, $customer_email_body, $email_id);
                
                $success_message = 'Booking submitted successfully! We will contact you shortly to confirm your appointment. Your booking ID is #' . $booking_id;
            } else {
                $error_message = 'Error: ' . $conn->error;
            }
        }
        
        // Fetch services for dropdown
        $services_result = $conn->query("SELECT * FROM services WHERE status = 'active' ORDER BY title");
        
        // Fetch vehicle brands for dropdown
        $brands_result = $conn->query("SELECT * FROM vehicle_brands WHERE is_active = 1 ORDER BY brand_name");
        ?>
        
        <!-- booking area -->
        <div class="section-full content-inner bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card">
                            <div class="card-body p-5">
                                <h2 class="text-center mb-4">Book Your Car Service</h2>
                                
                                <?php if ($success_message): ?>
                                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                                <?php endif; ?>
                                
                                <?php if ($error_message): ?>
                                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                <?php endif; ?>
                                
                                <form method="POST" class="row g-3">
                                    <!-- Service Selection -->
                                    <div class="col-md-12">
                                        <label for="service_id" class="form-label">Select Service *</label>
                                        <select class="form-control" id="service_id" name="service_id" required onchange="updateServiceName()">
                                            <option value="">Choose a service...</option>
                                            <?php if ($services_result && $services_result->num_rows > 0): ?>
                                                <?php while ($service = $services_result->fetch_assoc()): ?>
                                                    <option value="<?php echo $service['id']; ?>" 
                                                            data-name="<?php echo htmlspecialchars($service['title']); ?>"
                                                            <?php echo (isset($_GET['service_id']) && $_GET['service_id'] == $service['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($service['title']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                        <input type="hidden" id="service_name" name="service_name" />
                                    </div>
                                    
                                    <!-- Vehicle Details -->
                                    <div class="col-md-6">
                                        <label for="vehicle_brand" class="form-label">Vehicle Brand *</label>
                                        <select class="form-control" id="vehicle_brand" name="vehicle_brand" required>
                                            <option value="">Select brand...</option>
                                            <?php if ($brands_result && $brands_result->num_rows > 0): ?>
                                                <?php while ($brand = $brands_result->fetch_assoc()): ?>
                                                    <option value="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                                                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="vehicle_model" class="form-label">Vehicle Model *</label>
                                        <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" 
                                               placeholder="e.g., Swift, i20, Nexon" required>
                                    </div>
                                    
                                    <!-- Customer Details -->
                                    <div class="col-md-12">
                                        <label for="owner_name" class="form-label">Owner Name *</label>
                                        <input type="text" class="form-control" id="owner_name" name="owner_name" 
                                               placeholder="Enter your full name" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="mobile_no" class="form-label">Mobile Number *</label>
                                        <input type="tel" class="form-control" id="mobile_no" name="mobile_no" 
                                               placeholder="+91 9876543210" pattern="[0-9+\-\s]+" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="email_id" class="form-label">Email ID *</label>
                                        <input type="email" class="form-control" id="email_id" name="email_id" 
                                               placeholder="your.email@example.com" required>
                                    </div>
                                    
                                    <!-- Booking Date & Time -->
                                    <div class="col-md-6">
                                        <label for="booking_date" class="form-label">Preferred Date *</label>
                                        <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                               min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="booking_time" class="form-label">Preferred Time *</label>
                                        <select class="form-control" id="booking_time" name="booking_time" required>
                                            <option value="">Select time...</option>
                                            <option value="09:00">09:00 AM</option>
                                            <option value="10:00">10:00 AM</option>
                                            <option value="11:00">11:00 AM</option>
                                            <option value="12:00">12:00 PM</option>
                                            <option value="14:00">02:00 PM</option>
                                            <option value="15:00">03:00 PM</option>
                                            <option value="16:00">04:00 PM</option>
                                            <option value="17:00">05:00 PM</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Additional Notes -->
                                    <div class="col-md-12">
                                        <label for="additional_notes" class="form-label">Additional Notes</label>
                                        <textarea class="form-control" id="additional_notes" name="additional_notes" rows="3" 
                                                  placeholder="Any specific requirements or issues with your vehicle..."></textarea>
                                    </div>
                                    
                                    <div class="col-12 text-center mt-4">
                                        <button type="submit" class="site-button">
                                            <span>Book Service</span>
                                            <i class="fas fa-calendar-check"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Service Info -->
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h3>Why Choose Arjun Car Care?</h3>
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="icon-bx-wraper bx-style-1 p-a30 center">
                                        <div class="icon-xl text-primary m-b20">
                                            <i class="fas fa-tools"></i>
                                        </div>
                                        <div class="icon-content">
                                            <h5>Expert Technicians</h5>
                                            <p>Certified and experienced mechanics</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="icon-bx-wraper bx-style-1 p-a30 center">
                                        <div class="icon-xl text-primary m-b20">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="icon-content">
                                            <h5>Quick Service</h5>
                                            <p>Fast and efficient service delivery</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="icon-bx-wraper bx-style-1 p-a30 center">
                                        <div class="icon-xl text-primary m-b20">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <div class="icon-content">
                                            <h5>Quality Guarantee</h5>
                                            <p>100% satisfaction guaranteed</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="icon-bx-wraper bx-style-1 p-a30 center">
                                        <div class="icon-xl text-primary m-b20">
                                            <i class="fas fa-rupee-sign"></i>
                                        </div>
                                        <div class="icon-content">
                                            <h5>Affordable Pricing</h5>
                                            <p>Competitive and transparent pricing</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- booking area END -->
    </div>

    <script>
        function updateServiceName() {
            const serviceSelect = document.getElementById('service_id');
            const serviceNameInput = document.getElementById('service_name');
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            
            if (selectedOption.value) {
                serviceNameInput.value = selectedOption.getAttribute('data-name');
            } else {
                serviceNameInput.value = '';
            }
        }
        
        // Auto-select service on page load if service_id is in URL
        document.addEventListener('DOMContentLoaded', function() {
            updateServiceName();
        });
    </script>
</div>

<?php require("./config/footer.php") ?>

</body>
</html>
