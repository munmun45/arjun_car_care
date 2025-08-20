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
                    <h1 class="text-white">Shop</h1>
                </div>
            </div>
        </div>
        <!-- inner page banner END -->
        <!-- Breadcrumb row -->
        <div class="breadcrumb-row">
            <div class="container">
                <ul class="list-inline">
                    <li><a href="#">Home</a></li>
                    <li>Shop</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb row END -->
        <!-- contact area -->
        <div class="section-full content-inner">
            <!-- Product -->
            <div class="container">
                <div class="row">
                    <?php
                    require_once 'somaspanel/config/config.php';
                    
                    // Fetch active products from database
                    $query = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="col-lg-3 col-md-4 col-sm-6">';
                            echo '<div class="item-box m-b10">';
                            echo '<div class="item-img">';
                            if ($row['image']) {
                                echo '<img src="somaspanel/uploads/products/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" style="width: 100%; height: 250px; object-fit: cover;">';
                            } else {
                                echo '<img src="images/product/item1.jpg" alt="' . htmlspecialchars($row['title']) . '" style="width: 100%; height: 250px; object-fit: cover;">';
                            }
                            echo '<div class="item-info-in">';
                            echo '<ul>';
                            echo '<li><a href="#" data-bs-toggle="modal" data-bs-target="#productModal' . $row['id'] . '"><i class="ti-eye"></i></a></li>';
                            echo '<li><a href="contact.php"><i class="ti-shopping-cart"></i></a></li>';
                            echo '<li><a href="#"><i class="ti-heart"></i></a></li>';
                            echo '</ul>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="item-info text-center text-black p-a10">';
                            echo '<h6 class="item-title"><a href="#">' . htmlspecialchars($row['title']) . '</a></h6>';
                            
                            // Display star rating
                            echo '<ul class="item-review">';
                            $rating = floatval($row['star_rating']);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<li><i class="fas fa-star"></i></li>';
                                } elseif ($i - 0.5 <= $rating) {
                                    echo '<li><i class="fas fa-star-half-alt"></i></li>';
                                } else {
                                    echo '<li><i class="far fa-star"></i></li>';
                                }
                            }
                            echo '</ul>';
                            
                            // Display pricing
                            echo '<h4 class="item-price">';
                            if ($row['off_price'] && $row['off_price'] > 0) {
                                echo '<del>₹' . number_format($row['off_price'], 2) . '</del> ';
                            }
                            echo '<span class="text-primary">₹' . number_format($row['main_price'], 2) . '</span>';
                            echo '</h4>';
                            
                            // Display quantity if available
                            if ($row['quantity'] > 0) {
                                echo '<small class="text-success">In Stock (' . $row['quantity'] . ' available)</small>';
                                echo '<div class="mt-2">';
                                echo '<a href="contact.php" class="btn btn-primary btn-sm">Buy Now</a>';
                                echo '</div>';
                            } else {
                                echo '<small class="text-danger">Out of Stock</small>';
                                echo '<div class="mt-2">';
                                echo '<button class="btn btn-secondary btn-sm" disabled>Out of Stock</button>';
                                echo '</div>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col-12 text-center">';
                        echo '<h3>No Products Available</h3>';
                        echo '<p>We are currently updating our product catalog. Please check back soon!</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <!-- Product END -->
		</div>
		<div class="section-full p-t50 p-b20 bg-gray">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-lg-4">
						<div class="icon-bx-wraper left m-b30">
							<div class="icon-md text-black radius"> 
								<a href="#" class="icon-cell text-black"><i class="fas fa-gift"></i></a> 
							</div>
							<div class="icon-content">
								<h5 class="dlab-tilte">Free shipping on orders $60+</h5>
								<p>Order more than 60$ and you will get free shippining Worldwide. More info.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-lg-4">
						<div class="icon-bx-wraper left m-b30">
							<div class="icon-md text-black radius"> 
								<a href="#" class="icon-cell text-black"><i class="fas fa-plane"></i></a> 
							</div>
							<div class="icon-content">
								<h5 class="dlab-tilte">Worldwide delivery</h5>
								<p>We deliver to the following countries: USA, Canada, Europe, Australia</p>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-lg-4">
						<div class="icon-bx-wraper left m-b30">
							<div class="icon-md text-black radius"> 
								<a href="#" class="icon-cell text-black"><i class="fas fa-history"></i></a> 
							</div>
							<div class="icon-content">
								<h5 class="dlab-tilte">60 days money back guranty!</h5>
								<p>Not happy with our product, feel free to return it, we will refund 100% your money!</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
    
   
</div>


<?php require("./config/footer.php") ?>


</body>

<!-- Mirrored from autocare-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Aug 2025 19:58:35 GMT -->
</html>