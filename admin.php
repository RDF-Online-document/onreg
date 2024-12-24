
<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
// For admin pages, add this additional check
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RDF-Admin</title>
    <link rel="stylesheet" href="home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="home-container">
        <nav class="navbar">
            <div class="navbar-brand">
                <img src="rdf-logo.png" alt="RDF Logo" class="logo">
                <h1>Rwanda Defence Force</h1>
            </div>
            <div class="navbar-menu">
                <ul>
                    <li class="active"><a href="admin.php">
                        <i class="bi bi-house-door"></i> HOME
                    </a></li>
                    <li><a href="readdocument.php">
                        <i class="bi bi-info-circle"></i> INFORMATION
                    </a></li>
                    <li><a href="new.php">
                        <i class="bi bi-person-plus"></i> REGISTRATION
                    </a></li>
                </ul>
            </div>

            <div class="navbar-actions">
                <a href="index.php" class="logout-button">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </nav>

        <main class="home-content">
        <header>
            <h1>Ministry of Defence</h1>
            <p>Ensuring National Security and Protecting Rwanda's Sovereignty</p>
            </header>
            <section class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <h3>National Security</h3>
                    <p>Comprehensive protection strategies for the nation.</p>
                </div>

                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <h3>International Cooperation</h3>
                    <p>Building strong diplomatic and defense partnerships.</p>
                </div>
                <div class="service-card" id="open-popup">
                <div class="service-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3>Community Engagement</h3>
                    <p>Supporting and protecting Rwanda's citizens.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h4>Contact Information</h4>
                    <p><i class="bi bi-phone"></i> MOD: +250 788 218 939</p>
                    <p><i class="bi bi-phone"></i> JOC: +250 782 875 817</p>
                    <p><i class="bi bi-envelope"></i> info@mod.gov.rw</p>
                </div>
                <div class="col-md-6">
                    <div class="social-icons text-end">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-whatsapp"></i></a>
                    </div>
                    <p class="text-end mt-3">© 2024 Government of the Republic of Rwanda</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>