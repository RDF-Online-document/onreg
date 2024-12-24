<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }
        .error-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        .error-icon {
            color: #E53E3E;
            font-size: 80px;
            margin-bottom: 20px;
        }
        .btn-retry {
            margin-top: 20px;
            background-color: #1A365D;
            border-color: #1A365D;
        }
        .btn-retry:hover {
            background-color: #2C5282;
        }
        .error-details {
            background-color: #FFF5F5;
            border-left: 4px solid #E53E3E;
            padding: 15px;
            margin-top: 20px;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php
    // Retrieve error message from URL parameter
    $errorMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'An unknown error occurred';
    
    // Log the error (optional)
    error_log("Form Submission Error: " . $errorMessage);
    ?>
    <div class="error-container">
        <div class="error-icon">
            ⚠️
        </div>
        <h2 class="mb-4 text-danger">Submission Failed</h2>
        <p class="text-muted">
            We're sorry, but there was a problem submitting your online documentation. 
            Please review the details below and try again.
        </p>
        
        <div class="error-details">
            <h5>Error Details:</h5>
            <p class="text-danger"><?php echo $errorMessage; ?></p>
        </div>

        <div class="mt-4">
            <a href="Insertonlinedocumentation.php" class="btn btn-primary btn-retry">
                Try Again
            </a>
            <a href="index.html" class="btn btn-secondary ms-2">
                Return to Home
            </a>
        </div>

        <div class="mt-3 small text-muted">
            <p>If the problem persists, please contact support with the error details.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>