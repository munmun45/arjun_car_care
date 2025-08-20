<!-- Footer -->
<?php
// Include database connection for footer
if (!isset($conn)) {
    require_once('./somaspanel/config/config.php');
}

// Initialize footer_contact variable
if (!isset($footer_contact)) {
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
    $footer_contact = null;
    if ($result && $result->num_rows > 0) {
        $footer_contact = $result->fetch_assoc();
    }
    
    // Default contact info if none exists in database
    if (!$footer_contact) {
        $footer_contact = [
            'mobile1' => '+91 98765 43210',
            'mobile2' => '+91 98765 43211',
            'email1' => 'info@arjuncarcare.com',
            'email2' => 'support@arjuncarcare.com',
            'address' => 'Tamando, Bhubaneswar, Odisha 751002, India'
        ];
    }
}
?>
<footer class="site-footer">
        <!-- footer top part -->
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 footer-col-4">
                        <div class="widget widget_about">
                            <div class="logo-footer logo-white">
                                <img src="images/logo.png" alt="Arjun Car Care">
                            </div>
                            <p><strong>Arjun Car Care</strong> is your trusted car service center. We provide comprehensive car care services including repairs, maintenance, washing, and detailing to keep your vehicle in top condition.</p>
                            <ul class="dlab-social-icon dez-border">
                                <li><a class="fab fa-facebook-f" href="https://www.facebook.com/arjuncarcare" target="_blank"></a></li>
                                <li><a class="fab fa-instagram" href="https://www.instagram.com/arjuncarcare" target="_blank"></a></li>
                                <li><a class="fab fa-whatsapp" href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', $footer_contact['mobile1']); ?>" target="_blank"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 footer-col-4">
                        <div class="widget">
                            <h4 class="m-b15 text-uppercase">Contact Info</h4>
                            <div class="dlab-separator-outer m-b10">
                                <div class="dlab-separator bg-white style-skew"></div>
                            </div>
                            <ul class="widget_getintuch">
                                <li>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <strong>address</strong>
                                    <?php echo nl2br(htmlspecialchars($footer_contact['address'])); ?>
                                </li>
                                <li>
                                    <i class="fas fa-phone-alt"></i>
                                    <strong>phone</strong>
                                    <a href="tel:<?php echo htmlspecialchars($footer_contact['mobile1']); ?>"><?php echo htmlspecialchars($footer_contact['mobile1']); ?></a><br>
                                    <a href="tel:<?php echo htmlspecialchars($footer_contact['mobile2']); ?>"><?php echo htmlspecialchars($footer_contact['mobile2']); ?></a>
                                </li>
                                <li>
                                    <i class="fas fa-envelope"></i>
                                    <strong>email</strong>
                                    <a href="mailto:<?php echo htmlspecialchars($footer_contact['email1']); ?>"><?php echo htmlspecialchars($footer_contact['email1']); ?></a><br>
                                    <a href="mailto:<?php echo htmlspecialchars($footer_contact['email2']); ?>"><?php echo htmlspecialchars($footer_contact['email2']); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 footer-col-4">
                        <div class="widget widget_services">
                            <h4 class="m-b15 text-uppercase">Our Services</h4>
                            <div class="dlab-separator-outer m-b10">
                                <div class="dlab-separator bg-white style-skew"></div>
                            </div>
                            <ul>
                                <li><a href="services.php#car-service">Car Servicing</a></li>
                                <li><a href="services.php#car-repair">Car Repairs</a></li>
                                <li><a href="services.php#car-wash">Car Wash & Detailing</a></li>
                                <li><a href="services.php#ac-service">AC Service & Repair</a></li>
                                <li><a href="services.php#battery">Battery Replacement</a></li>
                                <li><a href="services.php#tyre-service">Tyre Services</a></li>
                                <li><a href="services.php#denting-painting">Denting & Painting</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer bottom part -->
        <div class="footer-bottom footer-line">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6 text-left">
                        <span> 2025 Arjun Car Care. All rights reserved.</span>
                    </div>
                    <div class="col-lg-6 col-md-6 text-end">
                        <span>Designed with <i class="fas fa-heart text-danger"></i> by Arjun Car Care Team</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer END -->


<!-- JavaScript  files ========================================= -->
<script src="js/jquery.min.js"></script><!-- JQUERY.MIN JS -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script><!-- BOOTSTRAP.MIN JS -->
<script src="plugins/bootstrap-select/bootstrap-select.min.js"></script><!-- FORM JS -->
<script src="plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.js"></script><!-- FORM JS -->
<script src="plugins/magnific-popup/magnific-popup.js"></script><!-- MAGNIFIC POPUP JS -->
<script src="plugins/counter/waypoints-min.js"></script><!-- WAYPOINTS JS -->
<script src="plugins/counter/counterup.min.js"></script><!-- COUNTERUP JS -->
<script src="plugins/imagesloaded/imagesloaded.js"></script><!-- IMAGESLOADED -->
<script src="plugins/masonry/masonry-3.1.4.js"></script><!-- MASONRY -->
<script src="plugins/masonry/masonry.filter.js"></script><!-- MASONRY -->
<script src="plugins/owl-carousel/owl.carousel.js"></script><!-- OWL SLIDER -->
<script src="js/custom.min.js"></script><!-- CUSTOM FUCTIONS  -->
<script src="js/dz.carousel.min.js"></script><!-- SORTCODE FUCTIONS  -->
<script src="plugins/lightgallery/js/lightgallery-all.js"></script><!-- LIGHT GALLERY -->
<script src="js/dz.ajax.js"></script><!-- CONTACT JS -->
<script src="plugins/switcher/js/switcher.js"></script><!-- SWITCHER -->
<!-- REVOLUTION JS FILES -->
<script src="plugins/revolution/js/jquery.themepunch.tools.min.js"></script>
<script src="plugins/revolution/js/jquery.themepunch.revolution.min.js"></script>
<!-- Slider revolution 5.0 Extensions  (Load Extensions only on Local File Systems !  The following part can be removed on Server for On Demand Loading) -->
<script src="plugins/revolution/js/extensions/revolution.extension.actions.min.js"></script>
<script src="plugins/revolution/js/extensions/revolution.extension.carousel.min.js"></script>
<script src="plugins/revolution/js/extensions/revolution.extension.kenburn.min.js"></script>
<script src="plugins/revolution/js/extensions/revolution.extension.layeranimation.min.js"></script>
<script src="plugins/revolution/js/extensions/revolution.extension.migration.min.js"></script>
<script src="plugins/revolution/js/extensions/revolution.extension.navigation.min.js"></script>
<script src="plugins/revolution/js/extensions/revolution.extension.parallax.min.js"></script>
<script src="plugins/revolution/js/extensions/revolution.extension.slideanims.min.js"></script>
<script src="plugins/revolution/js/extensions/revolution.extension.video.min.js"></script>
<script src="js/rev.slider.js"></script>
<script>
jQuery(document).ready(function() {
	'use strict';
	dz_rev_slider_1();
});	/*ready*/
</script>
