<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';



function sendBookingEmail($destination, $body, $customerEmail = null) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->Port = 587;
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Username = 'no_reply@arjuncarcare.com';
        $mail->Password = 'YourPassword123';

        //Recipients
        $mail->setFrom('no_reply@arjuncarcare.com', 'Arjun Car Care');
        
        $mail->addAddress('maharanamunmun@gmail.com', $destination);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation - Arjun Car Care';
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Booking email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendContactEmail($name, $email, $phone, $message) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->Port = 587;
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Username = 'no_reply@arjuncarcare.com';
        $mail->Password = 'YourPassword123';

        //Recipients
        $mail->setFrom('no_reply@arjuncarcare.com', 'Arjun Car Care');
        $mail->addAddress('maharanamunmun@gmail.com', 'Arjun Car Care');
        $mail->addReplyTo($email, $name);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission - Arjun Car Care';
        
        $emailBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 20px; background-color: #f9f9f9; border-radius: 0 0 8px 8px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #555; }
                .value { margin-top: 5px; padding: 10px; background-color: white; border-left: 4px solid #007bff; border-radius: 4px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🌟 New Contact Form Submission</h2>
                    <p>Someone has reached out through your website!</p>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>👤 Name:</div>
                        <div class='value'>" . htmlspecialchars($name) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>📧 Email:</div>
                        <div class='value'>" . htmlspecialchars($email) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>📱 Phone:</div>
                        <div class='value'>" . htmlspecialchars($phone) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>💬 Message:</div>
                        <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>🕒 Submitted:</div>
                        <div class='value'>" . date('F d, Y \a\t g:i A') . "</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>This email was sent from your website contact form.<br>
                    Please reply directly to this email to respond to the customer.</p>
                </div>
            </div>
        </body>
        </html>";
        
        $mail->Body = $emailBody;
        $mail->AltBody = "New Contact Form Submission\n\nName: $name\nEmail: $email\nPhone: $phone\nMessage: $message\nSubmitted: " . date('F d, Y \a\t g:i A');

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Contact email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

