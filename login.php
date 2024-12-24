<?php
session_start(); // Start session at the very beginning of the file

// Database connection function
function getConnection() {
    $conn = new mysqli('localhost', 'root', '', 'project');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Login function
function loginUser($service_number, $password) {
    $conn = getConnection();
    $error = null;
    
    $stmt = $conn->prepare("SELECT * FROM login WHERE SVC_No = ?");
    $stmt->bind_param("s", $service_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['SVC_No'];
            $_SESSION['user_role'] = $user['Role'];
            $_SESSION['logged_in'] = true;
            
            // Return success with redirect URL
            if ($user['Role'] == 'admin') {
                return ['success' => true, 'redirect' => 'admin.php'];
            } else {
                return ['success' => true, 'redirect' => 'Home.php'];
            }
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "No user found with this service number";
    }
    
    return ['success' => false, 'error' => $error];
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['service_number']) && isset($_POST['password'])) {
        $result = loginUser($_POST['service_number'], $_POST['password']);
        
        if ($result['success']) {
            header("Location: " . $result['redirect']);
            exit();
        } else {
            $error_message = $result['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RDF Login</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="rdf-logo.png" alt="RDF Logo">
                <h1>Rwanda Defence Force</h1>
            </div>
            
            <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
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
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="icon-lock"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password" 
                            required
                        >
                        <span class="toggle-password">👁️</span>
                    </div>
                </div>

                <div class="forgot-password">
                    <a href="reset-password.php">Forgot Password?</a>
                </div>

                <button type="submit" class="login-button">Login</button>

                <div class="social-login">
                    <p>Or login with:</p>
                    <div class="social-icons">
                        <a href="#" class="facebook">Facebook</a>
                        <a href="#" class="google">Google</a>
                        <a href="#" class="twitter">Twitter</a>
                    </div>
                </div>

                <div class="signup-link">
                    Don't have an account? 
                    <a href="signup.php">Sign Up</a>
                </div>
            </form>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>
