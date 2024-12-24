<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No record ID provided");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT * FROM Identification WHERE id = '$id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("No record found");
}

$record = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detailed Record View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }
        .profile-header {
            background-color: #1A365D;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .profile-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
            font-weight: bold;
        }
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .document-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="profile-section">
                    <div class="profile-header d-flex justify-content-between align-items-center">
                        <h2>
                            <i class="bi bi-person-badge me-2"></i>
                            <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?>
                        </h2>
                        <div>
                            <a href="edit.php?id=<?php echo $record['id']; ?>" class="btn btn-warning me-2">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <a href="readdocument.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Records
                            </a>
                        </div>
                    </div>

                    <div class="row p-4">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <div class="section-title">Personal Information</div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>SVC No</span>
                                <strong><?php echo htmlspecialchars($record['SVC_No']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Rank</span>
                                <strong><?php echo htmlspecialchars($record['ranks']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>ID No</span>
                                <strong><?php echo htmlspecialchars($record['ID_No']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Date of Birth</span>
                                <strong><?php echo htmlspecialchars($record['date_of_birth']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Father's Name</span>
                                <strong><?php echo htmlspecialchars($record['father_Name']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Mother's Name</span>
                                <strong><?php echo htmlspecialchars($record['mother_Name']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Martial Status</span>
                                <strong><?php echo htmlspecialchars($record['martial_status']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Spouse Name</span>
                                <strong><?php echo htmlspecialchars($record['spouse_name']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Next of Kin</span>
                                <strong><?php echo htmlspecialchars($record['Next_of_Kin']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Place of Entry</span>
                                <strong><?php echo htmlspecialchars($record['place_of_entry']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>unity</span>
                                <strong><?php echo htmlspecialchars($record['unit']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Courses</span>
                                <strong><?php echo htmlspecialchars($record['courses']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Level of Edication</span>
                                <strong><?php echo htmlspecialchars($record['Level_Of_Edication']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Last Promotion</span>
                                <strong><?php echo htmlspecialchars($record['last_promotion']); ?></strong>
                            </div>
                        </div> 

                        <!-- Address Information -->
                        <div class="col-md-6">
                            <div class="section-title">Address Details</div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Birth Country</span>
                                <strong><?php echo htmlspecialchars($record['birth_country']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Birth Province</span>
                                <strong><?php echo htmlspecialchars($record['birth_province']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Birh District</span>
                                <strong><?php echo htmlspecialchars($record['birth_district']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Birth Sector</span>
                                <strong><?php echo htmlspecialchars($record['birth_sector']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Current Country</span>
                                <strong><?php echo htmlspecialchars($record['live_country']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Current Province</span>
                                <strong><?php echo htmlspecialchars($record['live_province']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Current District</span>
                                <strong><?php echo htmlspecialchars($record['live_district']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Current Sector</span>
                                <strong><?php echo htmlspecialchars($record['live_sector']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Current Cell</span>
                                <strong><?php echo htmlspecialchars($record['live_cell']); ?></strong>
                            </div>
                            <div class="detail-row d-flex justify-content-between">
                                <span>Current Village</span>
                                <strong><?php echo htmlspecialchars($record['live_village']); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="row p-4">
                        <div class="col-md-12">
                            <div class="section-title">Uploaded Documents</div>
                            <div class="row">
                                <?php 
                                // Diploma upload
                                $diplomaFile = './files/' . htmlspecialchars($record['diploma_path']);
                                if (file_exists($diplomaFile)) {
                                    $fileExtension = strtolower(pathinfo($diplomaFile, PATHINFO_EXTENSION));
                                    $fileIcon = match($fileExtension) {
                                        'pdf' => 'bi-file-pdf text-danger',
                                        'doc' => 'bi-file-word text-primary',
                                        'docx' => 'bi-file-word text-primary',
                                        'jpg' => 'bi-file-image text-success',
                                        'png' => 'bi-file-image text-success',
                                        default => 'bi-file'
                                    };
                                ?>
                                    <div class="col-md-4 text-center">
                                        <i class="bi <?php echo $fileIcon; ?> display-4"></i>
                                        <p>Diploma</p>
                                        <a href="<?php echo $diplomaFile; ?>" target="_blank" class="btn btn-outline-primary">
                                            View Document
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php 
                                // Course certificates
                                $certificateFiles = explode(',', $record['certificates_path']);
                                foreach ($certificateFiles as $certFile) {
                                    $certPath = './files/' . htmlspecialchars(trim($certFile));
                                    if (file_exists($certPath)) {
                                        $fileExtension = strtolower(pathinfo($certPath, PATHINFO_EXTENSION));
                                        $fileIcon = match($fileExtension) {
                                            'pdf' => 'bi-file-pdf text-danger',
                                            'doc' => 'bi-file-word text-primary',
                                            'docx' => 'bi-file-word text-primary',
                                            'jpg' => 'bi-file-image text-success',
                                            'png' => 'bi-file-image text-success',
                                            default => 'bi-file'
                                        };
                                ?>
                                    <div class="col-md-4 text-center">
                                        <i class="bi <?php echo $fileIcon; ?> display-4"></i>
                                        <p>Course Certificate</p>
                                        <a href="<?php echo $certPath; ?>" target="_blank" class="btn btn-outline-primary">
                                            View Document
                                        </a>
                                    </div>
                                <?php 
                                    }
                                } 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>