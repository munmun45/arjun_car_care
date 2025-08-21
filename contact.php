<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from autocare-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Aug 2025 19:57:06 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
	
<?php 
require("./config/meta.php");
require_once('./email/email.php');

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = trim($_POST['dzName'] ?? '');
    $email = trim($_POST['dzEmail'] ?? '');
    $phone = trim($_POST['dzOther']['Phone'] ?? '');
    $subject = trim($_POST['dzOther']['Subject'] ?? '');
    $userMessage = trim($_POST['dzMessage'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($userMessage)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        // Combine subject and message
        $fullMessage = !empty($subject) ? "Subject: $subject\n\n$userMessage" : $userMessage;
        
        // Send email
        if (sendContactEmail($name, $email, $phone, $fullMessage)) {
            $message = 'Thank you for your message! We will get back to you soon.';
            $messageType = 'success';
            // Clear form data on success
            $_POST = [];
        } else {
            $message = 'Sorry, there was an error sending your message. Please try again later.';
            $messageType = 'error';
        }
    }
}
?>
	
</head>
<body id="bg"><div id="loading-area"></div>
<div class="page-wraper">


    <?php require("./config/header.php") ?>



    <div class="page-content">
        <!-- inner page banner -->
        <div class="dlab-bnr-inr overlay-black-middle" style="background-image:url(images/background/bg4.jpg);">
            <div class="container">
                <div class="dlab-bnr-inr-entry">
                    <h1 class="text-white">Contact Us</h1>
                </div>
            </div>
        </div>
        <!-- inner page banner END -->
        <!-- Breadcrumb row -->
        <div class="breadcrumb-row">
            <div class="container">
                <ul class="list-inline">
                    <li><a href="index.php">Home</a></li>
                    <li>Contact Us</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb row END -->
        
        <?php
        // Include database connection
        require_once('./somaspanel/config/config.php');
        
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
        $contact = null;
        if ($result && $result->num_rows > 0) {
            $contact = $result->fetch_assoc();
        }
        
        // Default contact info if none exists in database
        if (!$contact) {
            $contact = [
                'mobile1' => '+91 123 456 7890',
                'mobile2' => '+91 987 654 3210',
                'email1' => 'info@arjuncarcare.com',
                'email2' => 'support@arjuncarcare.com',
                'address' => 'Arjun Car Care Center, Main Road, Your City, State - 123456',
                'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d227748.3825624477!2d75.65046970649679!3d26.88544791796718!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x396c4adf4c57e281%3A0xce1c63a0cf22e09!2sJaipur%2C+Rajasthan!5e0!3m2!1sen!2sin!4v1500819483219'
            ];
        }
        ?>
        
        <!-- contact area -->
        <div class="section-full content-inner bg-white contact-style-1">
			<div class="container">
                <div class="row">
					<div class="col-lg-4 col-md-6 col-sm-6 m-b30">
						<div class="icon-bx-wraper bx-style-1 p-a30 center">
							<div class="icon-xl text-primary m-b20"> <a href="#" class="icon-cell"><i class="ti-location-pin"></i></a> </div>
							<div class="icon-content">
								<h5 class="dlab-tilte text-uppercase">Address</h5>
								<p><?php echo htmlspecialchars($contact['address']); ?></p>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-6 col-sm-6 m-b30">
						<div class="icon-bx-wraper bx-style-1 p-a30 center">
							<div class="icon-xl text-primary m-b20"> <a href="mailto:<?php echo htmlspecialchars($contact['email1']); ?>" class="icon-cell"><i class="ti-email"></i></a> </div>
							<div class="icon-content">
								<h5 class="dlab-tilte text-uppercase">Email</h5>
								<p><a href="mailto:<?php echo htmlspecialchars($contact['email1']); ?>"><?php echo htmlspecialchars($contact['email1']); ?></a> <br/> 
								<a href="mailto:<?php echo htmlspecialchars($contact['email2']); ?>"><?php echo htmlspecialchars($contact['email2']); ?></a></p>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-6 col-sm-6 m-b30">
						<div class="icon-bx-wraper bx-style-1 p-a30 center">
							<div class="icon-xl text-primary m-b20"> <a href="tel:<?php echo htmlspecialchars($contact['mobile1']); ?>" class="icon-cell"><i class="ti-mobile"></i></a> </div>
							<div class="icon-content">
								<h5 class="dlab-tilte text-uppercase">Phone</h5>
								<p><a href="tel:<?php echo htmlspecialchars($contact['mobile1']); ?>"><?php echo htmlspecialchars($contact['mobile1']); ?></a> <br/> 
								<a href="tel:<?php echo htmlspecialchars($contact['mobile2']); ?>"><?php echo htmlspecialchars($contact['mobile2']); ?></a></p>
							</div>
						</div>
					</div>
				</div>
                <div class="row">
					<!-- Left part start -->
                    <div class="col-lg-6">
                        <div class="p-a30 bg-gray clearfix m-b30">
							<h2>Send Message Us</h2>
							<div class="dzFormMsg">
								<?php if (!empty($message)): ?>
									<div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
										<?php echo htmlspecialchars($message); ?>
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
								<?php endif; ?>
							</div>
							<form method="post" class="dzForm" action="">
							<input type="hidden" value="Contact" name="dzToDo" >
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input name="dzName" type="text" required class="form-control" placeholder="Your Name" value="<?php echo htmlspecialchars($_POST['dzName'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="input-group"> 
											    <input name="dzEmail" type="email" class="form-control" required  placeholder="Your Email Id" value="<?php echo htmlspecialchars($_POST['dzEmail'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
									<div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input name="dzOther[Phone]" type="text" required class="form-control" placeholder="Phone" value="<?php echo htmlspecialchars($_POST['dzOther']['Phone'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
									<div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input name="dzOther[Subject]" type="text" class="form-control" placeholder="Subject (Optional)" value="<?php echo htmlspecialchars($_POST['dzOther']['Subject'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <textarea name="dzMessage" rows="4" class="form-control" required placeholder="Your Message..."><?php echo htmlspecialchars($_POST['dzMessage'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
									<div class="col-lg-12">
										<div class="form-group">
											<div class="input-group">
												<div class="g-recaptcha" data-sitekey="6LefsVUUAAAAADBPsLZzsNnETChealv6PYGzv3ZN" data-callback="verifyRecaptchaCallback" data-expired-callback="expiredRecaptchaCallback"></div>
												<input class="form-control d-none" style="display:none;" data-recaptcha="true" required data-error="Please complete the Captcha">
											</div>
										</div>
									</div>
                                    <div class="col-lg-12">
                                        <button name="submit" type="submit" value="Submit" class="site-button "> <span>Submit</span> </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Left part END -->
					<!-- right part start -->
                    <div class="col-lg-6 m-b30">
                        <?php if (!empty($contact['map_embed'])): ?>
                            <iframe src="<?php echo htmlspecialchars($contact['map_embed']); ?>" class="align-self-stretch" style="border:0; width:100%; min-height:100%;" allowfullscreen></iframe>
                        <?php else: ?>
                            <div class="p-a30 bg-gray text-center" style="min-height:400px; display:flex; align-items:center; justify-content:center;">
                                <div>
                                    <i class="ti-location-pin" style="font-size:48px; color:#ccc; margin-bottom:20px;"></i>
                                    <h4>Map Location</h4>
                                    <p>Map will be displayed here once location is set in admin panel.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- right part END -->
                </div>
            </div>
        </div>
        <!-- contact area  END -->
    </div>


    
    
   
</div>


<?php require("./config/footer.php") ?>


</body>

<!-- Mirrored from autocare-html.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 16 Aug 2025 19:58:35 GMT -->
</html>