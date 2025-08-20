-- Contact Information Table
CREATE TABLE IF NOT EXISTS `contact_info` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default contact information
INSERT INTO `contact_info` (`mobile1`, `mobile2`, `email1`, `email2`, `address`, `map_embed`) VALUES
('+91 98765 43210', '+91 98765 43211', 'info@arjuncarcare.com', 'support@arjuncarcare.com', 'Tamando, Bhubaneswar, Odisha 751002, India', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3742.1234567890!2d85.8245!3d20.2961!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjDCsDE3JzQ2LjAiTiA4NcKwNDknMjguMiJF!5e0!3m2!1sen!2sin!4v1234567890" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>');
