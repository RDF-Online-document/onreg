<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    error_log("Database connection attempt");
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    error_log("Database connected successfully");

    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        throw new Exception("Invalid record ID");
    }

    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Admin fields
        if ($isAdmin) {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $svcNo = trim($_POST['SVC_No'] ?? '');
            $rank = trim($_POST['ranks'] ?? '');
            $idNo = trim($_POST['ID_No'] ?? '');
            $dob = trim($_POST['date_of_birth'] ?? '');
            $fatherName = trim($_POST['father_Name'] ?? '');
            $motherName = trim($_POST['mother_Name'] ?? '');
            $placeOfEntry = trim($_POST['place_of_entry'] ?? '');
            $unit = trim($_POST['unit'] ?? '');
            $lastPromotion = trim($_POST['last_promotion'] ?? '');
            $birthCountry = trim($_POST['birth_country'] ?? '');
            $birthProvince = trim($_POST['birth_province'] ?? '');
            $birthDistrict = trim($_POST['birth_district'] ?? '');
            $birthSector = trim($_POST['birth_sector'] ?? '');
        }

        // Common fields
        $martialStatus = trim($_POST['martial_status'] ?? '');
        $spouseName = trim($_POST['spouse_name'] ?? '');
        $nextOfKin = trim($_POST['Next_of_Kin'] ?? '');
        $courses = trim($_POST['courses'] ?? '');
        $education = trim($_POST['Level_Of_Edication'] ?? '');
        $liveCountry = trim($_POST['live_country'] ?? '');
        $liveProvince = trim($_POST['live_province'] ?? '');
        $liveDistrict = trim($_POST['live_district'] ?? '');
        $liveSector = trim($_POST['live_sector'] ?? '');
        $liveCell = trim($_POST['live_cell'] ?? '');
        $liveVillage = trim($_POST['live_village'] ?? '');

        // File handling
        $diplomaPath = null;
        $certificatesPath = [];

        if (isset($_FILES['diploma']) && $_FILES['diploma']['error'] === UPLOAD_ERR_OK) {
            $diplomaPath = handleFileUpload($_FILES['diploma'], ['pdf', 'doc', 'docx', 'jpg', 'png'], 'diploma');
        }

        if (isset($_FILES['certificates'])) {
            foreach ($_FILES['certificates']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['certificates']['error'][$key] === UPLOAD_ERR_OK) {
                    $certificatePath = handleFileUpload(
                        [
                            'name' => $_FILES['certificates']['name'][$key],
                            'type' => $_FILES['certificates']['type'][$key],
                            'tmp_name' => $tmp_name,
                            'error' => $_FILES['certificates']['error'][$key],
                            'size' => $_FILES['certificates']['size'][$key]
                        ],
                        ['pdf', 'doc', 'docx', 'jpg', 'png'],
                        'certificate'
                    );
                    if ($certificatePath) {
                        $certificatesPath[] = $certificatePath;
                    }
                }
            }
        }

        // SQL query based on user role
        if ($isAdmin) {
            $sql = "UPDATE Identification SET 
                first_name=?, last_name=?, SVC_No=?, ranks=?, ID_No=?,
                date_of_birth=?, father_Name=?, mother_Name=?, martial_status=?,
                spouse_name=?, Next_of_Kin=?, place_of_entry=?, unit=?,
                courses=?, Level_Of_Edication=?, last_promotion=?,
                birth_country=?, birth_province=?, birth_district=?, birth_sector=?,
                live_country=?, live_province=?, live_district=?, live_sector=?,
                live_cell=?, live_village=?" . 
                ($diplomaPath ? ", diploma_path=?" : "") .
                (!empty($certificatesPath) ? ", certificates_path=?" : "") .
                " WHERE id=?";

            $params = [
                $firstName, $lastName, $svcNo, $rank, $idNo,
                $dob, $fatherName, $motherName, $martialStatus,
                $spouseName, $nextOfKin, $placeOfEntry, $unit,
                $courses, $education, $lastPromotion,
                $birthCountry, $birthProvince, $birthDistrict, $birthSector,
                $liveCountry, $liveProvince, $liveDistrict, $liveSector,
                $liveCell, $liveVillage
            ];
        } else {
            $sql = "UPDATE Identification SET 
                martial_status=?, spouse_name=?, Next_of_Kin=?,
                courses=?, Level_Of_Edication=?,
                live_country=?, live_province=?, live_district=?, 
                live_sector=?, live_cell=?, live_village=?" .
                ($diplomaPath ? ", diploma_path=?" : "") .
                (!empty($certificatesPath) ? ", certificates_path=?" : "") .
                " WHERE id=?";

            $params = [
                $martialStatus, $spouseName, $nextOfKin,
                $courses, $education,
                $liveCountry, $liveProvince, $liveDistrict,
                $liveSector, $liveCell, $liveVillage
            ];
        }

        if ($diplomaPath) {
            $params[] = $diplomaPath;
        }
        if (!empty($certificatesPath)) {
            $params[] = implode(',', $certificatesPath);
        }
        $params[] = $id;

        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($params) - 1) . 'i';
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            error_log("Update successful for ID: $id");
            $_SESSION['success_message'] = "Record updated successfully";
            header("Location: readdocument.php");
            exit();
        } else {
            error_log("Update error: " . $stmt->error);
            throw new Exception("Error updating record: " . $stmt->error);
        }
    }

    // Fetch existing record
    $stmt = $conn->prepare("SELECT * FROM Identification WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Record not found");
    }
    
    $record = $result->fetch_assoc();

} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
    header("Location: readdocument.php");
    exit();
}

function handleFileUpload($file, $allowedExtensions, $prefix = '') {
    try {
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid file type");
        }
        
        if ($fileSize > 5000000) {
            throw new Exception("File is too large");
        }
        
        $newFileName = $prefix . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
        $uploadPath = './files/' . $newFileName;
        
        if (!move_uploaded_file($fileTmp, $uploadPath)) {
            throw new Exception("Failed to upload file");
        }
        
        return $newFileName;
    } catch (Exception $e) {
        error_log("File upload error: " . $e->getMessage());
        return null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }
        .form-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
            padding: 20px;
        }
        .section-title {
            background-color: #1A365D;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .readonly-field {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="form-section">
            <h2 class="section-title">
                <i class="bi bi-pencil-square me-2"></i>
                Edit Record
                <?php if ($isAdmin): ?>
                    <span class="badge bg-warning ms-2">Admin Mode</span>
                <?php endif; ?>
            </h2>

            <form action="edit.php?id=<?= htmlspecialchars($id) ?>" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Personal Information</h4>

                        <!-- Fields that only admin can edit -->
                        <div class="mb-3">
                            <label for="SVC_No" class="form-label">SVC_No</label>
                            <input type="number" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>" 
                                   id="SVC_No" name="SVC_No"
                                   value="<?= htmlspecialchars($record['SVC_No'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>

                        <div class="mb-3">
                            <label for="ranks" class="form-label">Rank</label>
                            <input type="text" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="ranks" name="ranks"
                                   value="<?= htmlspecialchars($record['ranks'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="first_name" name="first_name"
                                   value="<?= htmlspecialchars($record['first_name'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="last_name" name="last_name"
                                   value="<?= htmlspecialchars($record['last_name'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="ID_No" class="form-label">ID Number</label>
                            <input type="number" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="ID_No" name="ID_No"
                                   value="<?= htmlspecialchars($record['ID_No'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="date_of_birth" name="date_of_birth"
                                   value="<?= htmlspecialchars($record['date_of_birth'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="father_Name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="father_Name" name="father_Name"
                                   value="<?= htmlspecialchars($record['father_Name'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="mother_Name" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="mother_Name" name="mother_Name"
                                   value="<?= htmlspecialchars($record['mother_Name'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                    

                        <!-- Add other admin-only fields following the same pattern -->

                        <!-- Fields that all users can edit -->
                        <div class="mb-3">
                            <label for="martial_status" class="form-label">Martial Status</label>
                            <select class="form-control" id="martial_status" name="martial_status" required>
                                <option value="" disabled>Select Martial Status</option>
                                <option value="Single" <?= ($record['martial_status'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                                <option value="Married" <?= ($record['martial_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                                <option value="Divorced" <?= ($record['martial_status'] ?? '') === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                                <option value="Widowed" <?= ($record['martial_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="spouse_name" class="form-label">Spouse Name</label>
                            <input type="text" class="form-control"
                                   id="spouse_name" name="spouse_name"
                                   value="<?= htmlspecialchars($record['spouse_name'] ?? '') ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="Next_of_Kin" class="form-label">Next of Kin</label>
                            <input type="text" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="Next_of_Kin" name="Next_of_Kin"
                                   value="<?= htmlspecialchars($record['Next_of_Kin'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="place_of_entry" class="form-label">Place of Entry</label>
                            <input type="text" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="place_of_entry" name="place_of_entry"
                                   value="<?= htmlspecialchars($record['place_of_entry'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="Unity" class="form-label">Unity</label>
                            <input type="text" class="form-control <?= !$isAdmin ? 'readonly-field' : '' ?>"
                                   id="unit" name="unit"
                                   value="<?= htmlspecialchars($record['unit'] ?? '') ?>"
                                   <?= !$isAdmin ? 'readonly' : '' ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="course" class="form-label">Course</label>
                            <input type="text" class="form-control"
                                   id="courses" name="courses"
                                   value="<?= htmlspecialchars($record['courses'] ?? '') ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="Level_Of_Edication" class="form-label">Level of Edication</label>
                            <input type="text" class="form-control"
                                   id="Level_Of_Edication" name="Level_Of_Edication"
                                   value="<?= htmlspecialchars($record['Level_Of_Edication'] ?? '') ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="last_promotion" class="form-label">Last Promotion</label>
                            <input type="text" class="form-control"
                                   id="last_promotion" name="last_promotion"
                                   value="<?= htmlspecialchars($record['last_promotion'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- Add remaining editable fields -->

                    </div>
                    <!-- Continue with address and document sections -->
                     <!-- Address Information -->
<div class="col-md-6">
    <h4 class="mb-3">Address Information</h4>
    
    <!-- Birth Address (Admin Only) -->
    <?php if ($isAdmin): ?>
    <div class="mb-3">
        <label class="form-label">Birth Address</label>
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" class="form-control" name="birth_country" 
                       placeholder="Country" value="<?= htmlspecialchars($record['birth_country'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="birth_province" 
                       placeholder="Province" value="<?= htmlspecialchars($record['birth_province'] ?? '') ?>">
            </div>
            <div class="col-md-6 mt-2">
                <input type="text" class="form-control" name="birth_district" 
                       placeholder="District" value="<?= htmlspecialchars($record['birth_district'] ?? '') ?>">
            </div>
            <div class="col-md-6 mt-2">
                <input type="text" class="form-control" name="birth_sector" 
                       placeholder="Sector" value="<?= htmlspecialchars($record['birth_sector'] ?? '') ?>">
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Current Address (All Users) -->
    <div class="mb-3">
        <label class="form-label">Current Address</label>
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" class="form-control" name="live_country" 
                       placeholder="Country" value="<?= htmlspecialchars($record['live_country'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="live_province" 
                       placeholder="Province" value="<?= htmlspecialchars($record['live_province'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mt-2">
                <input type="text" class="form-control" name="live_district" 
                       placeholder="District" value="<?= htmlspecialchars($record['live_district'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mt-2">
                <input type="text" class="form-control" name="live_sector" 
                       placeholder="Sector" value="<?= htmlspecialchars($record['live_sector'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mt-2">
                <input type="text" class="form-control" name="live_cell" 
                       placeholder="Cell" value="<?= htmlspecialchars($record['live_cell'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mt-2">
                <input type="text" class="form-control" name="live_village" 
                       placeholder="Village" value="<?= htmlspecialchars($record['live_village'] ?? '') ?>" required>
            </div>
        </div>
    </div>
</div>

<!-- Document Upload Section -->
<div class="col-12 mt-4">
    <div class="card">
        <div class="card-header bg-light">
            <h4 class="mb-0">Document Upload</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="diploma" class="form-label">Diploma</label>
                <input type="file" class="form-control" id="diploma" name="diploma" 
                       accept=".pdf,.doc,.docx,.jpg,.png">
                <?php if (!empty($record['diploma_path'])): ?>
                <div class="mt-2">
                    <small class="text-muted">Current file: <?= htmlspecialchars($record['diploma_path']) ?></small>
                </div>
                <?php endif; ?>
                <small class="text-muted d-block mt-1">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</small>
            </div>

            <div class="mb-3">
                <label for="certificates" class="form-label">Certificates</label>
                <input type="file" class="form-control" id="certificates" name="certificates[]" 
                       accept=".pdf,.doc,.docx,.jpg,.png" multiple>
                <?php if (!empty($record['certificates_path'])): ?>
                <div class="mt-2">
                    <small class="text-muted">Current files: <?= htmlspecialchars($record['certificates_path']) ?></small>
                </div>
                <?php endif; ?>
                <small class="text-muted d-block mt-1">You can select multiple files. Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB each)</small>
            </div>
        </div>
    </div>
</div>
                    <!-- ... (rest of the form remains the same) ... -->

                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                        <a href="readdocument.php" class="btn btn-secondary ms-2">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>