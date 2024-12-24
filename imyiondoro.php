<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Search</title>
    <link rel="stylesheet" href="imyi.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="navbar-brand">
                <img src="rdf-logo.png" alt="Logo" class="logo">
                <h1>Document Management</h1>
            </div>
            
            <div class="navbar-menu">
                <ul>
                    <li class="active"><a href="Home.php">
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
                    <input type="search" placeholder="Gushakisha serivise" class="search-input">
                    <button class="search-button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <a href="home.php" class="logout-button">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </nav>

        <main class="document-content">
            <div class="breadcrumb">
                <a href="Home.php">Main Page</a> / <small>Profile Structure</small>
            </div>

            <header>
                <h1>The Structure of Your Profile</h1>
                <p>Search and Access Your Files Easily</p>
            </header>

            <section class="document-search-section">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    </div>
                    <h2>File Description</h2>
                    <p>Search Your Files Using the Service Number</p>

                    <form action="read.php" method="POST" class="search-form">
                        <div class="form-group">
                            <input type="search" id="svc-search" name="search" 
                                   class="search-input" 
                                   placeholder="Andika SVC_No" 
                                   required>
                        </div>
                        <button type="submit" name="submit" class="btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </form>
                </div>
            </section>

            <div class="action-buttons">
                <button type="button" class="btn-secondary">
                    <a href="Home.php">Close</a>
                </button>
            </div>
        </main>

        <footer id="contact">
            <div class="footer-content">
                <div class="contact-info">
                    <h3>Twandikire</h3>
                    <div class="contact-details">
                        <p><i class="bi bi-phone"></i> +250 788 218 939</p>
                        <p><i class="bi bi-envelope"></i> info@example.com</p>
                    </div>
                </div>

                <div class="social-links">
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
                </div>

                <div class="footer-bottom">
                    <p>&copy; 2024 Your Organization</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>