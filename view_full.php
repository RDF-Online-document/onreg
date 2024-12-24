<?php
session_start(); 
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'project';

// Initialize variables
$record = null;
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

// Check if SVC_No is provided
if (!isset($_GET['svc']) || empty($_GET['svc'])) {
    header("Location: Home.php?error=" . urlencode("Invalid request"));
    exit();
}

$svc_no = $conn->real_escape_string(trim($_GET['svc']));

// Prepare and execute query
try {
    $query = "SELECT * FROM identification WHERE SVC_No = ?";
    
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        throw new Exception("Query preparation failed");
    }
    
    $stmt->bind_param("s", $svc_no);
    
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed");
    }
    
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    
    if (!$record) {
        throw new Exception("Record not found");
    }
    
} catch (Exception $e) {
    $error_message = "An error occurred while retrieving the record.";
    header("Location: Home.php?error=" . urlencode($error_message));
    exit();
}

// Functions for output handling
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatDate($date) {
    return !empty($date) ? date('d M Y', strtotime($date)) : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Details - <?php echo sanitizeOutput($record['ranks'] . ' ' . $record['first_name'] . ' ' . $record['last_name']); ?></title>
    <link rel="stylesheet" href="styl.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="navbar-brand">
                <img src="rdf-logo.png" alt="Logo" class="logo">
                <h1>Personnel Details</h1>
            </div>
        </nav>

        <main class="full-details-content">
            <div class="profile-card">
                <div class="profile-header">
                    <h2><?php echo sanitizeOutput($record['ranks'] . ' ' . $record['first_name'] . ' ' . $record['last_name']); ?></h2>
                    <span class="svc-number">SVC: <?php echo sanitizeOutput($record['SVC_No']); ?></span>
                </div>

                <!-- Personal Information -->
                <section class="details-section">
                    <h3><i class="bi bi-person-fill"></i> Personal Information</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <label>ID Number:</label>
                            <span><?php echo sanitizeOutput($record['ID_No']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Date of Birth:</label>
                            <span><?php echo formatDate($record['date_of_birth']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Father's Name:</label>
                            <span><?php echo sanitizeOutput($record['father_Name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Mother's Name:</label>
                            <span><?php echo sanitizeOutput($record['mother_Name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Martial Status:</label>
                            <span><?php echo sanitizeOutput($record['martial_status']); ?></span>
                        </div>
                        <?php if (!empty($record['spause_name'])): ?>
                        <div class="detail-item">
                            <label>Spouse Name:</label>
                            <span><?php echo sanitizeOutput($record['spause_name']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <label>Next of Kin:</label>
                            <span><?php echo sanitizeOutput($record['Next_of_Kin']); ?></span>
                        </div>
                    </div>
                </section>

                <!-- Military Information -->
                <section class="details-section">
                    <h3><i class="bi bi-shield-fill"></i> Military Information</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <label>Unit:</label>
                            <span><?php echo sanitizeOutput($record['unit']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Rank:</label>
                            <span><?php echo sanitizeOutput($record['ranks']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Place of Entry:</label>
                            <span><?php echo sanitizeOutput($record['place_of_entry']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Courses:</label>
                            <span><?php echo sanitizeOutput($record['courses']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Last Promotion:</label>
                            <span><?php echo sanitizeOutput($record['last_promotion']); ?></span>
                        </div>
                    </div>
                </section>

                <!-- Birth Location -->
                <section class="details-section">
                    <h3><i class="bi bi-geo-alt-fill"></i> Birth Location</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <label>Country:</label>
                            <span><?php echo sanitizeOutput($record['birth_country']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Province:</label>
                            <span><?php echo sanitizeOutput($record['birth_province']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>District:</label>
                            <span><?php echo sanitizeOutput($record['birth_district']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Sector:</label>
                            <span><?php echo sanitizeOutput($record['birth_sector']); ?></span>
                        </div>
                    </div>
                </section>

                <!-- Current Location -->
                <section class="details-section">
                    <h3><i class="bi bi-house-fill"></i> Current Location</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <label>Country:</label>
                            <span><?php echo sanitizeOutput($record['live_country']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Province:</label>
                            <span><?php echo sanitizeOutput($record['live_province']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>District:</label>
                            <span><?php echo sanitizeOutput($record['live_district']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Sector:</label>
                            <span><?php echo sanitizeOutput($record['live_sector']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Cell:</label>
                            <span><?php echo sanitizeOutput($record['live_cell']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Village:</label>
                            <span><?php echo sanitizeOutput($record['live_village']); ?></span>
                        </div>
                    </div>
                </section>
                
                <!-- Education Information -->
                <section class="details-section">
                    <h3><i class="bi bi-book-fill"></i> Education</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <label>Level of Education:</label>
                            <span><?php echo sanitizeOutput($record['Level_Of_Edication']); ?></span>
                    </div>
                    <div class="detail-item">
                            <label>Diploma:</label>
                            <?php if (!empty($record['diploma_path'])): ?>
                                <a href="<?php echo sanitizeOutput($diplomaFile = './files/' . htmlspecialchars($record['diploma_path'])); ?>" target="_blank" class="document-link">
                                
                                    <i class="bi bi-file-earmark-pdf"></i> View Diploma
                                </a>
                            <?php else: ?>
                                <span>No diploma uploaded</span>
                            <?php endif; ?>
                        </div>
                        <div class="detail-item">
                            <label>Certificates:</label>
                            <?php if (!empty($record['certificates_path'])): ?>
                                <a href="<?php echo sanitizeOutput($certFile = './files/' . htmlspecialchars($record['certificates_path'])); ?>" target="_blank" class="document-link">
                                    <i class="bi bi-file-earmark-pdf"></i> View Certificates
                                </a>
                            <?php else: ?>
                                <span>No certificates uploaded</span>
                            <?php endif; ?>
                        </div>
                </div>
                </section>
            </div>
            <div class="action-buttons">
                <a href="imyiondoro.php" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <a href="edit.php?id=<?php echo urlencode($record['id']); ?>" class="btn-primary">
                    <i class="bi bi-pencil-fill"></i> Edit Record
                </a>
            </div>
        </main>
    </div>
</body>
</html>