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


    <div class="page-content bg-white">
        <!-- inner page banner -->
        <div class="dlab-bnr-inr overlay-black-middle" style="background-image:url(images/background/bg4.jpg);">
            <div class="container">
                <div class="dlab-bnr-inr-entry">
                    <h1 class="text-white">Gallery</h1>
                </div>
            </div>
        </div>
        <!-- inner page banner END -->
        <!-- Breadcrumb row -->
        <div class="breadcrumb-row">
            <div class="container">
                <ul class="list-inline">
                    <li><a href="#">Home</a></li>
                    <li>Gallery</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb row END -->
        
        <!-- Gallery Section -->
        <div class="section-full content-inner bg-white">
            <div class="container">
                <div class="row">
                    <?php
                    require_once 'somaspanel/config/config.php';
                    
                    // Fetch active gallery images from database
                    $query = "SELECT * FROM gallery WHERE status = 'active' ORDER BY created_at DESC";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="col-lg-4 col-md-6 col-sm-6 m-b30">';
                            echo '<div class="dlab-box">';
                            echo '<div class="dlab-media dlab-img-overlay1 dlab-img-effect zoom">';
                            if ($row['image']) {
                                echo '<img src="somaspanel/uploads/gallery/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" style="width: 100%; height: 250px; object-fit: cover;">';
                            } else {
                                echo '<img src="images/gallery/pic1.jpg" alt="' . htmlspecialchars($row['title']) . '" style="width: 100%; height: 250px; object-fit: cover;">';
                            }
                            echo '<div class="dlab-info-has p-a20 no-hover">';
                            echo '<div class="dlab-post-title">';
                            echo '<h4 class="post-title"><a href="#" data-bs-toggle="modal" data-bs-target="#galleryModal' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></h4>';
                            if ($row['category']) {
                                echo '<span class="badge bg-primary">' . htmlspecialchars($row['category']) . '</span>';
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col-12 text-center">';
                        echo '<h3>No Gallery Images Available</h3>';
                        echo '<p>We are currently updating our gallery. Please check back soon!</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Gallery Modals -->
        <?php
        if ($result && $result->num_rows > 0) {
            $result->data_seek(0); // Reset result pointer
            while($row = $result->fetch_assoc()) {
                echo '<div class="modal fade" id="galleryModal' . $row['id'] . '" tabindex="-1">';
                echo '<div class="modal-dialog modal-lg">';
                echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                echo '<h5 class="modal-title">' . htmlspecialchars($row['title']) . '</h5>';
                echo '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>';
                echo '</div>';
                echo '<div class="modal-body text-center">';
                if ($row['image']) {
                    echo '<img src="somaspanel/uploads/gallery/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" class="img-fluid" style="max-height: 500px; border-radius: 8px;">';
                }
                if ($row['description']) {
                    echo '<p class="mt-3">' . nl2br(htmlspecialchars($row['description'])) . '</p>';
                }
                echo '<p><span class="badge bg-info">' . htmlspecialchars($row['category']) . '</span></p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
        
        <!-- Bootstrap JS for modals -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
        
    </div>
    
   
</div>


<?php require("./config/footer.php") ?>


</body>

<!-- Mirrored from autocare-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Aug 2025 19:58:35 GMT -->
</html>