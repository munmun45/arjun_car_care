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
        <!-- contact area -->
        <div class="section-full content-inner bg-white">
            <!-- Services  -->
            <div class="container">
                <div class="row">
                    <?php
                    require_once 'somaspanel/config/config.php';
                    
                    // Fetch active services from database
                    $query = "SELECT * FROM services WHERE status = 'active' ORDER BY created_at DESC";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="col-lg-4 col-md-6 col-sm-6 m-b30">';
                            echo '<div class="dlab-box">';
                            echo '<div class="dlab-media">';
                            if ($row['image']) {
                                echo '<a href="#"><img src="somaspanel/uploads/services/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" style="width: 100%; height: 200px; object-fit: cover;"></a>';
                            } else {
                                echo '<a href="#"><img src="images/our-work/pic1.jpg" alt="' . htmlspecialchars($row['title']) . '" style="width: 100%; height: 200px; object-fit: cover;"></a>';
                            }
                            echo '</div>';
                            echo '<div class="dlab-info p-a30 border-1">';
                            echo '<h4 class="dlab-title m-t0">';
                            if ($row['icon']) {
                                echo '<i class="' . htmlspecialchars($row['icon']) . '" style="margin-right: 10px; color: #f39c12;"></i>';
                            }
                            echo '<a href="#">' . htmlspecialchars($row['title']) . '</a>';
                            echo '</h4>';
                            echo '<p>' . htmlspecialchars(substr($row['description'], 0, 120)) . '...</p>';
                            echo '<a href="contact.php" class="site-button">Book Appointment</a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col-12 text-center">';
                        echo '<h3>No Services Available</h3>';
                        echo '<p>We are currently updating our services. Please check back soon!</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <!-- Services END -->
        </div>
        
        <!-- Service Modals -->
        <?php
        if ($result && $result->num_rows > 0) {
            $result->data_seek(0); // Reset result pointer
            while($row = $result->fetch_assoc()) {
                echo '<div class="modal fade" id="serviceModal' . $row['id'] . '" tabindex="-1">';
                echo '<div class="modal-dialog modal-lg">';
                echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                echo '<h5 class="modal-title">';
                if ($row['icon']) {
                    echo '<i class="' . htmlspecialchars($row['icon']) . '" style="margin-right: 10px; color: #f39c12;"></i>';
                }
                echo htmlspecialchars($row['title']);
                echo '</h5>';
                echo '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>';
                echo '</div>';
                echo '<div class="modal-body">';
                if ($row['image']) {
                    echo '<img src="somaspanel/uploads/services/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" class="img-fluid mb-3" style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px;">';
                }
                echo '<p>' . nl2br(htmlspecialchars($row['description'])) . '</p>';
                echo '</div>';
                echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                echo '<a href="contact.php" class="btn btn-primary">Contact Us</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
        
        <!-- Bootstrap JS for modals -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- contact area END -->
    </div>
    
   
</div>


<?php require("./config/footer.php") ?>


</body>

<!-- Mirrored from autocare-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Aug 2025 19:58:35 GMT -->
</html>