<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Import</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fontawesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding-top: 80px;
            color: #2d3748;
        }
        .navbar {
            background-color: #3182ce;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: white !important;
            transform: translateY(-1px);
        }
        .import-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 30px;
            margin-top: 50px;
            max-width: 500px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .import-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-upload {
            background-color: #3182ce;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-upload:hover {
            background-color: #2c5282;
            transform: translateY(-2px);
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
        }
        .file-input-label {
            border: 2px dashed #e2e8f0;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .file-input-label:hover {
            background-color: #f7fafc;
            border-color: #3182ce;
        }
        .text-success {
            color: #3182ce !important;
        }
        .alert-success {
            background-color: #ebf8ff;
            border-color: #bee3f8;
            color: #2c5282;
        }
        .alert-danger {
            background-color: #fff5f5;
            border-color: #fed7d7;
            color: #c53030;
        }
    </style>
</head>
<body>
    <!-- Rest of the HTML remains the same -->
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-database me-2"></i>
                Data Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-file-import me-1"></i> Import</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="readdocument.php"><i class="fas fa-table me-1"></i> View Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-cog me-1"></i> Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="import-container">
                    <div class="text-center mb-4">
                        <i class="fas fa-file-upload fa-4x text-success mb-3"></i>
                        <h2 class="mb-3">Excel Import</h2>
                        <p class="text-muted">Upload your Excel file to import data</p>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <div class="file-input-wrapper w-100">
                                <div class="file-input-label">
                                    <i class="fas fa-cloud-upload-alt text-success me-2"></i>
                                    <span id="file-chosen">No file chosen</span>
                                    <input 
                                        type="file" 
                                        class="form-control" 
                                        id="excel_file" 
                                        name="excel_file" 
                                        accept=".xlsx,.xls,.csv" 
                                        required
                                        onchange="updateFileName(this)"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button 
                                type="submit" 
                                name="import" 
                                class="btn btn-upload w-100 py-2"
                            >
                                <i class="fas fa-upload me-2"></i>Import Data
                            </button>
                        </div>
                    </form>

                    <div id="messageContainer" class="mt-3">
                        <?php 
                        if (!empty($successMessages)) {
                            echo '<div class="alert alert-success">';
                            foreach ($successMessages as $success) {
                                echo "<p>$success</p>";
                            }
                            echo '</div>';
                        }

                        if (!empty($errorMessages)) {
                            echo '<div class="alert alert-danger">';
                            foreach ($errorMessages as $error) {
                                echo "<p>$error</p>";
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script>
        function updateFileName(input) {
            const fileChosen = document.getElementById('file-chosen');
            if (input.files && input.files.length > 0) {
                fileChosen.textContent = input.files[0].name;
            } else {
                fileChosen.textContent = 'No file chosen';
            }
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>