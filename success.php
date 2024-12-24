<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }
        .success-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .success-icon {
            color: #48BB78;
            font-size: 80px;
            margin-bottom: 20px;
        }
        .btn-home {
            margin-top: 20px;
            background-color: #1A365D;
            border-color: #1A365D;
        }
        .btn-home:hover {
            background-color: #2C5282;
        }
    </style>
</head>
<body>
    <?php
    // Optional: Capture submission details or timestamp
    $submissionTime = date('Y-m-d H:i:s');
    ?>
    <div class="success-container">
        <div class="success-icon">
            ✓
        </div>
        <h2 class="mb-4">Submission Successful!</h2>
        <p class="text-muted">
            Your online documentation has been successfully submitted. 
            Our team will review your information and contact you if additional details are required.
        </p>
        <p class="small text-muted">
            Submission Time: <?php echo $submissionTime; ?>
        </p>
        <div class="mt-4">
            <a href="admin.php" class="btn btn-primary btn-home">
                Return to Home
            </a>
            <a href="Insertonlinedocumentation.php" class="btn btn-secondary ms-2">
                Submit Another Form
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>