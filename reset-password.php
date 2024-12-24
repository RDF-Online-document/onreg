<?php
session_start();
require 'vendor/autoload.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli('localhost', 'root', '', 'project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$messageType = '';

// Function to generate reset token
function generateToken() {
    return bin2hex(random_bytes(32));
}

// Function to send reset email
function sendResetEmail($email, $token, $service_number) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'dnsanzabaganwa@gmail.com'; // Replace with your email
        $mail->Password = 'gdls frkf pxin tdlz'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('dnsanzabaganwa@gmail.com', 'RDF System');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $resetLink = "http://yourwebsite.com/confirm-reset.php?token=" . $token . "&svc=" . $service_number;
        
        $mail->Body = "
            <h2>Password Reset Request</h2>
            <p>You have requested to reset your password.</p>
            <p>Please click the link below to reset your password:</p>
            <p><a href='$resetLink'>Reset Password</a></p>
            <p>If you didn't request this, please ignore this email.</p>
            <p>This link will expire in 1 hour.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['service_number']) && isset($_POST['email'])) {
        $service_number = $conn->real_escape_string($_POST['service_number']);
        $email = $conn->real_escape_string($_POST['email']);
        
        // Verify user exists
        $stmt = $conn->prepare("SELECT * FROM login WHERE SVC_No = ? AND email = ?");
        $stmt->bind_param("ss", $service_number, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Generate token and expiry time
            $token = generateToken();
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token in database
            $stmt = $conn->prepare("UPDATE login SET reset_token = ?, reset_expiry = ? WHERE SVC_No = ?");
            $stmt->bind_param("sss", $token, $expiry, $service_number);
            
            if ($stmt->execute() && sendResetEmail($email, $token, $service_number)) {
                $message = "Password reset instructions have been sent to your email.";
                $messageType = "success";
            } else {
                $message = "An error occurred. Please try again later.";
                $messageType = "error";
            }
        } else {
            $message = "No account found with that service number and email.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - RDF</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="rdf-logo.png" alt="RDF Logo">
                <h1>Reset Password</h1>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="input-group">
                    <label for="service-number">Service Number</label>
                    <div class="input-wrapper">
                        <i class="icon-user"></i>
                        <input 
                            type="text" 
                            id="service-number" 
                            name="service_number" 
                            placeholder="Enter your service number"
                            required
                        >
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="icon-envelope"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email address"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="login-button">Reset Password</button>

                <div class="back-to-login">
                    <a href="index.php">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>