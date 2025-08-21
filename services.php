<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from autocare-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Aug 2025 19:57:06 GMT -->
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
                    <h1 class="text-white">Services 1</h1>
                </div>
            </div>
        </div>
        <!-- inner page banner END -->
        <!-- Breadcrumb row -->
        <div class="breadcrumb-row">
            <div class="container">
                <ul class="list-inline">
                    <li><a href="#">Home</a></li>
                    <li>Services 1</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb row END -->
        <!-- Services Section -->
        <div class="section-full content-inner" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 80px 0;">
            <div class="container">
                <!-- Section Header -->
                <div class="section-head text-center mb-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <span class="badge mb-3 px-4 py-2" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; font-size: 14px; letter-spacing: 1px;">OUR SERVICES</span>
                            <h2 class="display-4 font-weight-bold text-dark mb-4" style="line-height: 1.2;">
                                Professional <span style="color: #dc3545; position: relative;">Car Care Services
                                    <div style="position: absolute; bottom: -5px; left: 0; width: 100%; height: 3px; background: linear-gradient(90deg, #dc3545, #c82333); border-radius: 2px;"></div>
                                </span>
                            </h2>
                            <p class="lead text-muted mb-0" style="font-size: 18px; line-height: 1.6;">
                                Comprehensive automotive solutions delivered by certified professionals using state-of-the-art equipment and genuine parts.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Services Grid -->
                <div class="row g-4">
                    <?php
                    require_once 'somaspanel/config/config.php';
                    
                    // Fetch active services from database
                    $query = "SELECT * FROM services WHERE status = 'active' ORDER BY created_at DESC";
                    $result = $conn->query($query);
                    
                    // Color schemes for service cards
                    $color_schemes = [
                        ['primary' => '#dc3545', 'secondary' => '#fdeaea', 'accent' => '#c82333'],
                        ['primary' => '#e74c3c', 'secondary' => '#fdf2f2', 'accent' => '#c0392b'],
                        ['primary' => '#f39c12', 'secondary' => '#fef9e7', 'accent' => '#d68910'],
                        ['primary' => '#e67e22', 'secondary' => '#fdf4e7', 'accent' => '#ca6f1e'],
                        ['primary' => '#ad1457', 'secondary' => '#fce4ec', 'accent' => '#880e4f'],
                        ['primary' => '#d32f2f', 'secondary' => '#ffebee', 'accent' => '#b71c1c']
                    ];
                    
                    if ($result && $result->num_rows > 0) {
                        $card_index = 0;
                        while($row = $result->fetch_assoc()) {
                            $colors = $color_schemes[$card_index % count($color_schemes)];
                            $card_index++;
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card h-100" style="
                            background: white;
                            border-radius: 20px;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                            border: none;
                            overflow: hidden;
                            position: relative;
                        ">
                            
                            <!-- Service Image -->
                            <div class="service-image position-relative" style="height: 250px; overflow: hidden;">
                                <?php if ($row['image']): ?>
                                    <img src="somaspanel/uploads/services/<?php echo htmlspecialchars($row['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <img src="images/our-work/pic1.jpg" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php endif; ?>
                                
                                <!-- Service Icon Overlay -->
                                
                            </div>
                            
                            <!-- Service Content -->
                            <div class="service-content p-4" style="flex: 1; display: flex; flex-direction: column;">
                                <h4 class="service-title font-weight-bold mb-3" style="
                                    color: #2c3e50;
                                    font-size: 20px;
                                    line-height: 1.3;
                                "><?php echo htmlspecialchars($row['title']); ?></h4>
                                
                                <p class="service-description text-muted mb-4" style="
                                    line-height: 1.6;
                                    font-size: 15px;
                                    flex: 1;
                                "><?php echo htmlspecialchars(substr($row['description'], 0, 150)) . (strlen($row['description']) > 150 ? '...' : ''); ?></p>
                                
                                
                                
                                <!-- Action Button -->
                                <div class="mt-auto">
                                    <a href="book.php?service_id=<?php echo $row['id']; ?>" class="btn btn-block" style="
                                        background: linear-gradient(135deg, <?php echo $colors['primary']; ?> 0%, <?php echo $colors['accent']; ?> 100%);
                                        border: none;
                                        color: white;
                                        padding: 12px 24px;
                                        border-radius: 25px;
                                        font-weight: 600;
                                        text-decoration: none;
                                        transition: all 0.3s ease;
                                        text-align: center;
                                        display: block;
                                    ">
                                        <i class="fas fa-calendar-check mr-2"></i>Book Appointment
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                    ?>
                    <div class="col-12">
                        <div class="text-center py-5" style="
                            background: white;
                            border-radius: 20px;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                        ">
                            <div class="mb-4">
                                <i class="fas fa-tools" style="font-size: 64px; color: #dc3545; opacity: 0.7;"></i>
                            </div>
                            <h3 class="font-weight-bold text-dark mb-3">Services Coming Soon!</h3>
                            <p class="text-muted mb-4 lead">We're preparing our comprehensive service offerings. Please check back soon!</p>
                            <a href="contact.php" class="btn btn-lg px-5" style="
                                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                                border: none;
                                color: white;
                                border-radius: 25px;
                                padding: 15px 40px;
                                font-weight: 600;
                            ">
                                <i class="fas fa-phone mr-2"></i>Contact Us
                            </a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- contact area END -->
    </div>
    
   
</div>


<?php require("./config/footer.php") ?>


</body>

<!-- Mirrored from autocare-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Aug 2025 19:58:35 GMT -->
</html>