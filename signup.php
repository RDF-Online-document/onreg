<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $service_number = mysqli_real_escape_string($conn, $_POST['service_number']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check admin count if role is admin
    if ($role === 'admin') {
        $admin_count_query = "SELECT COUNT(*) as admin_count FROM login WHERE Role = 'admin'";
        $admin_result = mysqli_query($conn, $admin_count_query);
        $admin_count = mysqli_fetch_assoc($admin_result)['admin_count'];

        if ($admin_count >= 2) {
            $error = "Maximum number of admin accounts (2) has been reached. Please select a different role.";
            $role = ''; // Reset role selection
        }
    }

    // Continue with validation only if no admin limit error
    if (empty($error)) {
        // Validation checks
        if ($password !== $confirm_password) {
            $error = "Passwords do not match";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } else {
            // Check if service number and ID number exist in any specific table
            $check_query = "SELECT * FROM identification WHERE SVC_No = ? AND ID_No = ?";
            $stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($stmt, "ss", $service_number, $id_number);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if service number, ID number, or email already exists in login table
            $existing_check_query = "SELECT * FROM login WHERE SVC_No = ? OR id_number = ? OR email = ?";
            $existing_stmt = mysqli_prepare($conn, $existing_check_query);
            mysqli_stmt_bind_param($existing_stmt, "sss", $service_number, $id_number, $email);
            mysqli_stmt_execute($existing_stmt);
            $existing_result = mysqli_stmt_get_result($existing_stmt);

            if (mysqli_num_rows($result) == 0) {
                $error = "Invalid Service Number or ID Number. Please contact administrative support.";
            } elseif (mysqli_num_rows($existing_result) > 0) {
                $error = "Service Number, ID Number, or Email is already registered";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Prepare insert statement
                $insert_query = "INSERT INTO login (SVC_No, phone_number, id_number, email, password, Role) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, "ssssss", $service_number, $phone_number, $id_number, $email, $hashed_password, $role);

                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to login page
                    header("Location: index.php?signup=success");
                    exit();
                } else {
                    $error = "Registration failed: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RDF - Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <div class="signup-container">
        <div class="signup-box">
            <div class="logo">
                <img src="rdf-logo.png" alt="RDF Logo">
                <h1>Rwanda Defence Force</h1>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" id="signup-form">
                <div class="input-group">
                    <label for="service-number">Service Number</label>
                    <div class="input-wrapper">
                        <i class="icon-id-card"></i>
                        <input 
                            type="text" 
                            id="service-number" 
                            name="service_number" 
                            placeholder="Enter your service number" 
                            required 
                            pattern="\d+"
                            title="Service number must contain only digits"
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

                <div class="input-group">
                    <label for="phone-number">Phone Number</label>
                    <div class="input-wrapper">
                        <i class="icon-phone"></i>
                        <input 
                            type="tel" 
                            id="phone-number" 
                            name="phone_number" 
                            placeholder="Enter your phone number" 
                            required 
                            pattern="\d{10}"
                            maxlength="10"
                            title="Phone number must be 10 digits"
                        >
                    </div>
                </div>

                <div class="input-group">
                    <label for="id-number">ID Number</label>
                    <div class="input-wrapper">
                        <i class="icon-id-badge"></i>
                        <input 
                            type="text" 
                            id="id-number" 
                            name="id_number" 
                            placeholder="Enter your ID number" 
                            required 
                            pattern="\d{16}"
                            maxlength="16"
                            title="ID number must be 16 digits"
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
                            placeholder="Create a password" 
                            required 
                            minlength="8"
                        >
                        <span class="toggle-password">👁️</span>
                    </div>
                    <small class="password-requirements">
                        Password must be at least 8 characters long
                    </small>
                </div>

                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="icon-lock"></i>
                        <input 
                            type="password" 
                            id="confirm-password" 
                            name="confirm_password" 
                            placeholder="Confirm your password" 
                            required 
                            minlength="8"
                        >
                    </div>
                </div>

                <div class="input-group">
                    <label for="role">User Role</label>
                    <div class="input-wrapper">
                        <select 
                            id="role" 
                            name="role" 
                            required
                        >
                            <option value="">Select User Role</option>
                            <option value="user">Regular User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="signup-button">Sign Up</button>

                <div class="login-link">
                    Already have an account? 
                    <a href="index.php">Login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="signup.js"></script>
</body>
</html>