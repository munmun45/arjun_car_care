<!-- header -->
<?php
// Include database connection for header
if (!isset($conn)) {
    require_once('./somaspanel/config/config.php');
}

// Initialize header_contact variable
if (!isset($header_contact)) {
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
    
    // Fetch contact information from database
    $result = $conn->query("SELECT * FROM contact_info LIMIT 1");
    $header_contact = null;
    if ($result && $result->num_rows > 0) {
        $header_contact = $result->fetch_assoc();
    }
    
    // Default contact info if none exists in database
    if (!$header_contact) {
        $header_contact = [
            'mobile1' => '+91 98765 43210',
            'mobile2' => '+91 98765 43211',
            'email1' => 'info@arjuncarcare.com',
            'email2' => 'support@arjuncarcare.com',
            'address' => 'Tamando, Bhubaneswar, Odisha 751002, India'
        ];
    }
}
?>
<header class="site-header header mo-left header-style-1">
        <!-- top bar -->
        <div class="top-bar">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="dlab-topbar-left">
                        <ul class="list-inline">
                            <li><a href="tel:<?php echo htmlspecialchars($header_contact['mobile1']); ?>"><i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($header_contact['mobile1']); ?></a></li>
                            <li><a href="mailto:<?php echo htmlspecialchars($header_contact['email1']); ?>"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($header_contact['email1']); ?></a></li>
                            <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($header_contact['address']); ?></li>
                        </ul>
                    </div>
                    <div class="dlab-topbar-right">
                        <ul class="social-bx list-inline float-end">
                            <li><a class="fab fa-facebook-f" href="https://www.facebook.com/arjuncarcare" target="_blank"></a></li>
                            <li><a class="fab fa-instagram" href="https://www.instagram.com/arjuncarcare" target="_blank"></a></li>
                            <li><a class="fab fa-whatsapp" href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', $header_contact['mobile1']); ?>" target="_blank"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- top bar END-->
        <!-- main header -->
        <div class="sticky-header header-curve main-bar-wraper navbar-expand-lg">
            <div class="main-bar bg-primary clearfix ">
                <div class="container clearfix">
                    <!-- website logo -->
                    <div class="logo-header logo-white mostion">
                        <a href="index.php">
                            <img src="images/logo.png" width="193" height="89" alt="Arjun Car Care">
                        </a>
                    </div>
                    <!-- nav toggle button -->
                    <button class="navbar-toggler collapsed navicon justify-content-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <!-- extra nav -->
                    <div class="extra-nav">
                        <div class="extra-cell">
                            <a href="contact.php" class="site-button">Book Service</a>
                        </div>
                    </div>
                    <!-- main nav -->
                    <div class="header-nav navbar-collapse collapse justify-content-end" id="navbarNavDropdown">
                        <ul class="nav navbar-nav nav-style">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="services.php">Our Services</a></li>
                            <li><a href="gallery.php">Gallery</a></li>
                            <li><a href="product.php">Products</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- main header END -->
    </header>
    <!-- header END -->