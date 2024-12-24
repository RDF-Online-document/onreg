<?php
session_start(); // Start the session at the beginning

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location:login.php"); // Redirect to login page if not logged in
    exit(); 
}

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'project';

// Initialize variables
$result = null;
$error_message = '';

// Create database connection
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    $error_message = "Database connection error. Please try again later.";
    header("Location: Home.php?error=" . urlencode($error_message));
    exit();
}

// Handle search
if (isset($_POST['submit']) && isset($_POST['search'])) {
    $search_term = $conn->real_escape_string(trim($_POST['search']));
    $logged_in_svc = $_SESSION['user_id']; // Get the logged-in user's SVC number
    
    // Validate search input
    if (empty($search_term)) {
        $error_message = "Please enter a search term.";
        header("Location: Home.php?error=" . urlencode($error_message));
        exit();
    }
    
    // Modified query to only search for the logged-in user's records
    $query = "SELECT * FROM identification WHERE 
              SVC_No = ? AND (
                  SVC_No LIKE ? 
              )";
    
    try {
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Query preparation failed");
        }
        
        $search_pattern = "%{$search_term}%";
        $stmt->bind_param("ss", 
            $logged_in_svc,      // Exact match for SVC_No
            $search_pattern   // Search patterns for other fields

        );
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed");
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error_message = "No records found.";
            header("Location: Home.php?error=" . urlencode($error_message));
            exit();
        }
        
    } catch (Exception $e) {
        $error_message = "An error occurred while searching. Please try again.";
        header("Location: Home.php?error=" . urlencode($error_message));
        exit();
    }
} else {
    // If no search is performed, show the logged-in user's information by default
    try {
        $logged_in_svc = $_SESSION['SVC_No'];
        $query = "SELECT * FROM identification WHERE SVC_No = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            throw new Exception("Query preparation failed");
        }
        
        $stmt->bind_param("s", $logged_in_svc);
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed");
        }
        
        $result = $stmt->get_result();
        
    } catch (Exception $e) {
        $error_message = "An error occurred while retrieving your information.";
        header("Location: Home.php?error=" . urlencode($error_message));
        exit();
    }
}

// Function to sanitize output
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Function to format date
function formatDate($date) {
    return !empty($date) ? date('d M Y', strtotime($date)) : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="read.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="navbar-brand">
                <img src="rdf-logo.png" alt="Logo" class="logo">
                <h1>Search Results</h1>
            </div>
        </nav>

        <main class="results-content">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="result-card">
                        <div class="result-header">
                            <h2><?php echo sanitizeOutput($row['ranks'] . ' ' . $row['first_name'] . ' ' . $row['last_name']); ?></h2>
                            <span class="svc-number">SVC: <?php echo sanitizeOutput($row['SVC_No']); ?></span>
                        </div>
                        
                        <div class="result-details">
                            <div class="detail-group">
                                <label>ID Number:</label>
                                <span><?php echo sanitizeOutput($row['ID_No']); ?></span>
                            </div>
                            
                            <div class="detail-group">
                                <label>Unit:</label>
                                <span><?php echo sanitizeOutput($row['unit']); ?></span>
                            </div>
                            
                            <div class="detail-group">
                                <label>Birth Place:</label>
                                <span><?php echo sanitizeOutput($row['birth_district'] . ', ' . $row['birth_province']); ?></span>
                            </div>
                            
                            <div class="detail-group">
                                <label>Current Location:</label>
                                <span><?php echo sanitizeOutput($row['live_district'] . ', ' . $row['live_province']); ?></span>
                            </div>
                        </div>
                        
                        <div class="result-actions">
                            <a href="view_full.php?svc=<?php echo urlencode($row['SVC_No']); ?>" class="btn-primary">
                                <i class="bi bi-eye"></i> View Full Details
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="bi bi-exclamation-circle"></i>
                    <p><?php echo $error_message ?: "No records found."; ?></p>
                    <a href="Home.php" class="btn-primary">Back to Search</a>
                </div>
            <?php endif; ?>
        </main>

        <div class="action-buttons">
            <a href="imyiondoro.php" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
</body>
</html>