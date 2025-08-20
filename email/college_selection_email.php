<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendCollegeSelectionEmail($to, $studentName, $collegeName, $additionalInfo = '') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
               $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->Port = 587;
        $mail->SMTPDebug = 0; // Set to 2 for debugging
        $mail->SMTPAuth = true;
        $mail->Username = 'info@satyameducation.org.in';
        $mail->Password = 'Qwertyuiop1@';

        // Recipients
        $mail->setFrom('info@satyameducation.org.in', 'Satyam Education');
        $mail->addAddress($to, $studentName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'College Selection Confirmation - ' . $collegeName;
        
        
        // Email body with responsive design
        $mail->Body = '<!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; background-color: #f9f9f9; border-radius: 0 0 5px 5px; }
                .button { 
                    display: inline-block; 
                    padding: 10px 20px; 
                    background-color: #4CAF50; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 5px; 
                    margin: 20px 0; 
                }
                .footer { 
                    margin-top: 20px; 
                    padding-top: 20px; 
                    border-top: 1px solid #ddd; 
                    font-size: 0.9em; 
                    color: #777; 
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>College Selection Confirmation</h2>
            </div>
            <div class="content">
                <p>Dear ' . htmlspecialchars($studentName) . ',</p>
                <p>We are pleased to inform you that you have been selected for admission to:</p>
                
                ' . (!empty($additionalInfo) ? '<p>' . nl2br(htmlspecialchars($additionalInfo)) . '</p>' : '') . '
                
                <p><strong>Next Steps:</strong></p>
               <ul>
  <li>Keep checking your email and admission portal regularly for updates.</li>
  <li>Wait until you receive your confirmation message.</li>
  <li>After confirmation, follow the instructions to complete the admission process.</li>
</ul>
                
                <p>If you have any questions or need further assistance, feel free to contact our support team.
</p>
                
                <p>Best regards,<br>Admission Team</p>
                
                <div class="footer">
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->AltBody = "College Selection Confirmation\n\n" .
            "Dear $studentName,\n\n" .
            "We are pleased to inform you that you have been selected for admission to:\n" .
            "$collegeName\n\n" .
            (!empty($additionalInfo) ? "$additionalInfo\n\n" : "") .
            "Next Steps:\n" .
            "- Keep checking your email and admission portal regularly for updates.\n" .
            "- Wait until you receive your confirmation message.\n" .
            "- After confirmation, follow the instructions to complete the admission process.\n\n" .
            "If you have any questions or need further assistance, please don't hesitate to contact our support team.\n\n" .
            "Best regards,\n" .
            "Admission Team\n" .
            "Satyam Education";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error
        error_log("College selection email could not be sent to $to. Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Function to get student email from document table
function getStudentEmail($conn, $studentId) {
    $stmt = $conn->prepare("SELECT email FROM document WHERE id = ?");
    $stmt->bind_param('i', $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    return $student ? $student['email'] : null;
}
?>
