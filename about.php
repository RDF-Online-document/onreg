<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RDF Online Documentation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a5f7a;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --dark-background: #1e3a5f;
            --light-background: #f8f9fa;
            --text-color: #2c3e50;
            --text-light: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-background);
        }

        header {
            background: linear-gradient(135deg, var(--dark-background), var(--primary-color));
            color: var(--text-light);
            padding: 1.2rem 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-light);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo::before {
            content: "📚";
            font-size: 1.5rem;
        }

        .menu ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .menu ul li a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: var(--transition);
        }

        .menu ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .page-title {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(rgba(30, 58, 95, 0.9), rgba(30, 58, 95, 0.9)), url('/api/placeholder/1200/400');
            background-size: cover;
            background-position: center;
            color: var(--text-light);
        }

        .page-title h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: 0.5px;
        }

        .main-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: -3rem auto 4rem;
            padding: 0 2rem;
            position: relative;
        }

        .content-block {
            background-color: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 2rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .content-block::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .content-block:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
        }

        .content-block h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .content-block h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
            border-radius: 3px;
        }

        .content-block p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-color);
        }

        footer {
            background-color: var(--dark-background);
            color: var(--text-light);
            text-align: center;
            padding: 2rem;
            margin-top: 2rem;
        }

        footer p {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1rem;
        }

        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: repeat(2, 1fr);
                margin-top: -2rem;
            }

            .page-title h1 {
                font-size: 2.3rem;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }

            .menu ul {
                gap: 1rem;
            }

            .menu ul li a {
                font-size: 1rem;
                padding: 0.4rem 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                grid-template-columns: 1fr;
                margin-top: -1rem;
            }

            .page-title {
                padding: 3rem 1rem;
            }

            .page-title h1 {
                font-size: 2rem;
            }

            .menu ul {
                flex-direction: column;
                align-items: center;
            }

            .content-block {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="#" class="logo">RDF Docs</a>
            <nav class="menu">
                <ul>
                    <li><a href="Home.php">HOME</a></li>
                    <li><a href="#">DOCUMENTS</a></li>
                    <li><a href="#">CONTACT</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="page-title">
        <h1>About RDF Online Documentation</h1>
    </div>

    <div class="main-content">
        <div class="content-block">
            <h2>About Us</h2>
            <p>We are dedicated to developing an online documentation platform for the Rwanda Defence Forces. Our goal is to streamline the filling, submission, and management of essential documents, enhancing operational efficiency, reducing paperwork, and improving communication among personnel.</p>
        </div>

        <div class="content-block">
            <h2>Mission</h2>
            <p>To empower Rwanda Defence Force personnel by providing a user-friendly online documentation platform that streamlines the process of filling, submitting, and managing essential documents, ensuring data security, and fostering effective communication within the organization.</p>
        </div>

        <div class="content-block">
            <h2>Aim</h2>
            <p>The primary goal of this project is to create a reliable and accessible online documentation resource that serves as a go-to guide for users. By streamlining the documentation process, we aim to reduce confusion, improve user satisfaction, and enhance the overall understanding of our documentation system.</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Rwanda Defence Forces. Contact: info@mod.gov.rw</p>
    </footer>
</body>
</html>