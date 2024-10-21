<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Struggling to discover fun and exciting events near you? Chautari brings the best local events tailored to you so you can join
            local happenings with ease.">
    <meta property="og:title" content="Chautari — Find out about events around you.">

    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico" sizes="any">
    <link rel="stylesheet" href="../assets/css/header.css">
    <script src="../assets/js/hamburger.js"></script>
</head>

<body>
    <div class="header-wrapper">
        <header>
            <div class="logo-header">
                <a href="/index.php">
                    <div class="logo-header-inner">
                        <img src="../assets/images/logo-32.svg" alt="logo">
                        <p>Chautari</p>
                    </div>
                </a>
            </div>

            <div>
                <nav>
                    <ul>
                        <li><a href="/index.php">Home</a></li>
                        <li><a href="../pages/about.php">About</a></li>
                        <li><a href="../pages/contact.php">Contact</a></li>
                    </ul>
                </nav>
            </div>

            <div>
                <a class="nav-button-secondary" href="../pages/login.php">Log in</a>
                <a class="nav-button-primary" href="../pages/signup.php">Sign up today</a>
            </div>

            <div class="hamburger" onclick="toggleOverlay()">
                <svg class="nav-toggle" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M4 6H20M4 12H20M4 18H20"></path>
                </svg>
            </div>

            <div id="overlay" class="overlay">
                <div class="overlay-content">
                    <h2>Menu</h2>
                    <hr>
                    <ul>
                        <li><a href="../pages/about.php">About</a></li>
                        <li><a href="../pages/contact.php">Contact</a></li>
                        <li><a href="../pages/privacy.php">Privacy</a></li>
                        <hr>
                        <li><a href="../pages/login.php">Log in</a></li>
                        <li><a href="../pages/signup.php">Sign up</a></li>
                        <hr>
                    </ul>
                    <button onclick="toggleOverlay()">Close</button>
                </div>
            </div>
        </header>
    </div>
</body>