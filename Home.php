
<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
// For admin pages, add this additional check
if ($_SESSION['user_role'] !== 'user') {
    header("Location: Home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RDF - Home</title>
    <link rel="stylesheet" href="home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
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
                    <li class="active"><a href="home.php">
                        <i class="bi bi-house-door"></i> HOME
                    </a></li>
                    <li><a href="about.php">
                        <i class="bi bi-info-circle"></i> ABOUT
                    </a></li>
                    <li><a href="#contact">
                        <i class="bi bi-telephone"></i> CONTACT
                    </a></li>
                </ul>
            </div>

            <div class="navbar-actions">
                <div class="search-container">
                    <input type="search" placeholder="Search services" class="search-input">
                    <button class="search-button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <a href="index.php" class="logout-button">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </nav>

        <main class="home-content">
            <header>
                <h1>Welcome Back!</h1>
                <p>Welcome to the Ministry of Defense portal. Easily manage and update your personal information using advanced technology.</p>
            </header>

            <section class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-collection"></i>
                    </div>
                    <h2>New Documents</h2>
                    <p>We've updated new methods to help you fill out your personal information.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h2>Document Management</h2>
                    <p>Easily manage and view your personal documents and records.</p>
                </div>

                <div class="service-card" id="open-popup">
                    <div class="service-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h2>Personal Information</h2>
                    <p>Update and verify your personal information securely.</p>
                </div>
            </section>

            <div class="popup" id="popup">
                <div class="popup-content">
                    <h2>Your Personal Information</h2>
                    <h3>About This Service</h3>
                    <p>This service allows users to view their personal information using their Service Number (SVC_No).</p>
                    <div class="popup-actions">
                        <button class="btn-secondary close-btn">Close</button>
                        <button class="btn-primary" id="continue-btn">Proceed</button>
                    </div>
                </div>
            </div>
        </main>

        <footer id="contact">
            <div class="footer-content">
                <div class="contact-info">
                    <h3>Contact Us</h3>
                    <div class="contact-details">
                        <p><i class="bi bi-phone"></i> MOD: +250 788 218 939</p>
                        <p><i class="bi bi-phone"></i> JOC: +250 782 875 817</p>
                        <p><i class="bi bi-phone"></i> MMI: 1535</p>
                        <p><i class="bi bi-phone"></i> GENDER DESK: 3945</p>
                        <p><i class="bi bi-envelope"></i> info@mod.gov.rw</p>
                    </div>
                </div>

                <div class="social-links">
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-google"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-whatsapp"></i></a>
                </div>

                <div class="footer-bottom">
                    <p>&copy; 2024 Government of the Republic of Rwanda</p>
                </div>
            </div>
        </footer>

        <button class="back-to-top" aria-label="Scroll to top">
            <i class="bi bi-arrow-up"></i>
            ⬆
        </button>
    </div>

    <script src="home.js"></script>
</body>
</html>