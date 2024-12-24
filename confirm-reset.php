<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$messageType = '';
$validToken = false;

// Verify token and show reset form
if (isset($_GET['token']) && isset($_GET['svc'])) {
    $token = $conn->real_escape_string($_GET['token']);
    $svc = $conn->real_escape_string($_GET['svc']);
    
    $stmt = $conn->prepare("SELECT * FROM login WHERE SVC_No = ? AND reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param("ss", $svc, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $validToken = true;
    } else {
        $message = "Invalid or expired reset link.";
        $messageType = "error";
    }
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $token = $conn->real_escape_string($_POST['token']);
    $svc = $conn->real_escape_string($_POST['svc']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password === $confirm_password) {
        if (strlen($new_password) >= 8) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("UPDATE login SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE SVC_No = ? AND reset_token = ? AND reset_expiry > NOW()");
            $stmt->bind_param("sss", $hashed_password, $svc, $token);
            
            if ($stmt->execute()) {
                $message = "Password successfully reset. You can now login with your new password.";
                $messageType = "success";
                header("refresh:3;url=login.php");
            } else {
                $message = "Error resetting password. Please try again.";
                $messageType = "error";
            }
        } else {
            $message = "Password must be at least 8 characters long.";
            $messageType = "error";
        }
    } else {
        $message = "Passwords do not match.";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - RDF</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

            <?php if ($validToken): ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                    <input type="hidden" name="svc" value="<?php echo htmlspecialchars($_GET['svc']); ?>">
                    
                    <div class="input-group">
                        <label for="new-password">New Password</label>
                        <div class="input-wrapper">
                            <i class="icon-lock"></i>
                            <input 
                                type="password" 
                                id="new-password" 
                                name="new_password" 
                                placeholder="Enter new password"
                                required
                                minlength="8"
                            >
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="confirm-password">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="icon-lock"></i>
                            <input 
                                type="password" 
                                id="confirm-password" 
                                name="confirm_password" 
                                placeholder="Confirm new password"
                                required
                                minlength="8"
                            >
                        </div>
                    </div>

                    <button type="submit" class="login-button">Set New Password</button>
                </form>
            <?php else: ?>
                <div class="back-to-login">
                    <a href="index.php">Back to Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>