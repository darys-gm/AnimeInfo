<?php
// Load authentication - MUST be at the very top before any HTML output
require_once __DIR__ . '/../config/auth.php';

// Initialize auth and check login status
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = $isLoggedIn ? $auth->getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="description" content="Website info anime terbaru, jadwal tayang episode anime, dan daftar anime yang akan datang">
    <meta name="keywords" content="anime, jadwal anime, episode anime, upcoming anime, anime schedule, anime terbaru">
    <meta name="author" content="AnimeInfo">
    <meta name="theme-color" content="#3498db">
    
    <title>AnimeInfo - Info Jadwal Tayang Anime Terbaru</title>
    
    <!-- ========================================
         FAVICON / LOGO DI TITLE BROWSER
         ======================================== -->
    
    <!-- Cara 1: Menggunakan file gambar logo (REKOMENDASI) -->
    <link rel="icon" type="image/png" sizes="32x32" href="images/logo.png">

    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        /* ========================================
           NAVBAR STYLES - RAPI & RESPONSIVE
           ======================================== */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        
        /* Navbar Container */
        .navbar {
            background: #ffffff;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e9ecef;
        }
        
        .navbar-scrolled {
            padding: 0.3rem 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        /* Logo */
        .navbar-brand {
            padding: 0;
            margin-right: 1rem;
        }
        
        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logo-image {
            height: 35px;
            width: auto;
        }
        
        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        
        .logo-main {
            font-size: 1rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .logo-main .text-primary {
            color: #3498db;
        }
        
        .logo-sub {
            font-size: 0.55rem;
            color: #7f8c8d;
            letter-spacing: 0.3px;
        }
        
        /* Navbar Toggler */
        .navbar-toggler {
            border: none;
            padding: 0.4rem 0.6rem;
            background: transparent;
            border-radius: 8px;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }
        
        .navbar-toggler-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .navbar-toggler-icon i {
            font-size: 1.3rem;
            color: #3498db;
        }
        
        /* Navbar Navigation */
        .navbar-nav {
            gap: 0.25rem;
            margin: 0.5rem 0;
        }
        
        .nav-item {
            list-style: none;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.85rem;
            color: #2c3e50;
            border-radius: 10px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .nav-link i {
            font-size: 0.9rem;
            width: 20px;
        }
        
        .nav-link:hover {
            color: #3498db;
            background: #ebf5ff;
        }
        
        .nav-link.active {
            color: #3498db;
            background: #ebf5ff;
        }
        
        /* Search Form Desktop */
        .search-form {
            position: relative;
            min-width: 220px;
        }
        
        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .search-icon {
            position: absolute;
            left: 12px;
            color: #7f8c8d;
            font-size: 0.8rem;
            pointer-events: none;
        }
        
        .search-input {
            width: 100%;
            padding: 0.45rem 1rem 0.45rem 2rem;
            border: 1px solid #e9ecef;
            border-radius: 50px;
            font-size: 0.8rem;
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52,152,219,0.1);
            background: #ffffff;
        }
        
        .search-btn {
            position: absolute;
            right: 4px;
            background: #3498db;
            border: none;
            border-radius: 50px;
            padding: 0.3rem 0.9rem;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.7rem;
        }
        
        .search-btn:hover {
            background: #2980b9;
        }
        
        /* Login Button */
        .login-btn {
            background: #3498db;
            border: none;
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        
        .login-btn:hover {
            background: #2980b9;
            transform: translateY(-1px);
            color: white;
        }
        
        /* User Menu Button */
        .user-menu-btn {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .user-menu-btn:hover {
            background: #ebf5ff;
            border-color: #3498db;
        }
        
        /* Dropdown */
        .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border: 1px solid #e9ecef;
            padding: 0.5rem 0;
            min-width: 180px;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.2rem;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
        }
        
        .dropdown-item:hover {
            background: #ebf5ff;
            padding-left: 1.5rem;
        }
        
        /* Right Section */
        .navbar-right-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Mobile Search Container */
        .mobile-search-container {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 0.6rem 1rem;
        }
        
        .mobile-search-form .input-group {
            border-radius: 50px;
            overflow: hidden;
        }
        
        .mobile-search-form .form-control {
            border: 1px solid #e9ecef;
            border-right: none;
            padding: 0.45rem 1rem;
            font-size: 0.8rem;
        }
        
        .mobile-search-form .btn {
            background: #3498db;
            border: none;
            padding: 0.45rem 1rem;
        }
        
        /* ========================================
           RESPONSIVE BREAKPOINTS
           ======================================== */
        
        /* Desktop Large */
        @media (min-width: 1200px) {
            .navbar-nav {
                gap: 0.5rem;
            }
        }
        
        /* Desktop */
        @media (min-width: 992px) {
            .navbar-collapse {
                display: flex !important;
                justify-content: space-between;
            }
            
            .navbar-nav {
                margin: 0 auto;
                flex-direction: row;
            }
            
            .mobile-search-container {
                display: none;
            }
        }
        
        /* Tablet */
        @media (min-width: 768px) and (max-width: 991px) {
            .navbar-nav {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-link {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
            
            .navbar-right-section {
                margin-top: 0.5rem;
                justify-content: flex-end;
            }
        }
        
        /* Mobile */
        @media (max-width: 767px) {
            .navbar-brand {
                margin-right: 0;
            }
            
            .logo-image {
                height: 28px;
            }
            
            .logo-main {
                font-size: 0.85rem;
            }
            
            .logo-sub {
                font-size: 0.5rem;
            }
            
            .navbar-nav {
                flex-direction: column;
                width: 100%;
                margin: 0.5rem 0;
                gap: 0.25rem;
            }
            
            .nav-link {
                padding: 0.5rem 0.8rem;
                justify-content: flex-start;
                font-size: 0.8rem;
            }
            
            .search-form {
                display: none;
            }
            
            .navbar-right-section {
                width: 100%;
                margin-top: 0.5rem;
                padding-top: 0.5rem;
                border-top: 1px solid #e9ecef;
                justify-content: center;
            }
            
            .user-menu-btn, .login-btn {
                width: 100%;
                justify-content: center;
            }
            
            .dropdown-menu {
                width: 100%;
                position: static !important;
                transform: none !important;
                margin-top: 0.5rem;
            }
        }
        
        /* Small Mobile */
        @media (max-width: 480px) {
            .logo-image {
                height: 24px;
            }
            
            .logo-main {
                font-size: 0.75rem;
            }
            
            .logo-sub {
                font-size: 0.45rem;
            }
            
            .nav-link {
                font-size: 0.75rem;
                padding: 0.4rem 0.7rem;
            }
            
            .nav-link i {
                font-size: 0.8rem;
                width: 18px;
            }
        }
        
        /* Search Suggestions */
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 1000;
            display: none;
            max-height: 280px;
            overflow-y: auto;
            margin-top: 4px;
            border: 1px solid #e9ecef;
        }
        
        .search-suggestions.show {
            display: block;
        }
        
        .suggestions-header {
            padding: 6px 12px;
            background: #f8f9fa;
            font-size: 0.7rem;
            color: #7f8c8d;
            font-weight: 500;
            border-bottom: 1px solid #e9ecef;
        }
        
        .suggestion-item {
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.8rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        .suggestion-item:hover {
            background: #ebf5ff;
        }
        
        /* Loading Overlay */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            display: none;
        }
    </style>
</head>
<body>

<!-- Modern Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <!-- Logo/Brand -->
        <a class="navbar-brand" href="index.php">
            <div class="logo-wrapper">
                <img src="images/logo.png" alt="AnimeInfo Logo" class="logo-image" onerror="this.src='https://via.placeholder.com/35x35?text=🎌'">
                <div class="logo-text">
                    <span class="logo-main">Anime<span class="text-primary">Info</span></span>
                </div>
            </div>
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" 
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="fas fa-bars"></i>
            </span>
        </button>
        
        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Navigation Links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'airing.php' ? 'active' : ''; ?>" href="airing.php">
                        <i class="fas fa-play-circle"></i>
                        <span>Sedang Tayang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : ''; ?>" href="schedule.php">
                        <i class="fas fa-calendar-week"></i>
                        <span>Jadwal Episode</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'upcoming.php' ? 'active' : ''; ?>" href="upcoming.php">
                        <i class="fas fa-star"></i>
                        <span>Akan Datang</span>
                    </a>
                </li>
            </ul>
            
            <!-- Right Section: Search + User Menu -->
            <div class="navbar-right-section">
                <!-- Search Form Desktop -->
                <form class="search-form" action="search.php" method="GET" role="search" id="searchForm" autocomplete="off">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input class="search-input" type="search" name="q" id="searchInput" 
                               placeholder="Cari anime..." autocomplete="off" required>
                        <button class="search-btn" type="submit" id="searchButton">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </form>
                
                <!-- User Menu / Login Button -->
                <?php if ($isLoggedIn && $currentUser): ?>
                    <div class="dropdown">
                        <button class="user-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> 
                            <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="mylist.php">
                                <i class="fas fa-list"></i> My Anime List
                            </a></li>
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user"></i> Profil Saya
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="auth.php" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> 
                        <span>Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Search Bar (visible only on mobile) -->
<div class="mobile-search-container d-md-none">
    <form class="mobile-search-form" action="search.php" method="GET">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Cari anime..." required>
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" style="width: 2.5rem; height: 2.5rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h6 class="text-primary">Memuat data...</h6>
    </div>
</div>

<!-- Main Content Container -->
<main class="container mt-4 mb-5">

<script>
// Popular anime suggestions
const popularAnime = [
    'Naruto', 'One Piece', 'Attack on Titan', 'Demon Slayer', 
    'Jujutsu Kaisen', 'My Hero Academia', 'Death Note', 
    'Fullmetal Alchemist', 'Tokyo Ghoul', 'Sword Art Online',
    'Dragon Ball', 'Bleach', 'Hunter x Hunter', 'One Punch Man',
    'Spy x Family', 'Chainsaw Man'
];

// Search suggestions functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const suggestionsDiv = document.getElementById('searchSuggestions');
    
    if (searchInput && suggestionsDiv) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            
            if (query.length === 0) {
                suggestionsDiv.classList.remove('show');
                return;
            }
            
            const filtered = popularAnime.filter(anime => 
                anime.toLowerCase().includes(query)
            ).slice(0, 5);
            
            if (filtered.length > 0) {
                let html = '<div class="suggestions-header"><i class="fas fa-history"></i> Saran Pencarian</div>';
                filtered.forEach(anime => {
                    html += `<div class="suggestion-item" onclick="selectSuggestion('${anime}')">
                                <i class="fas fa-search text-primary me-2"></i>
                                <strong>${anime}</strong>
                             </div>`;
                });
                suggestionsDiv.innerHTML = html;
                suggestionsDiv.classList.add('show');
            } else {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.classList.remove('show');
            }
        });
    }
    
    // Prevent empty search
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const input = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            
            if (input.value.trim() === '') {
                e.preventDefault();
                input.classList.add('is-invalid');
                setTimeout(() => {
                    input.classList.remove('is-invalid');
                }, 2000);
            } else {
                if (searchButton) {
                    searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    searchButton.disabled = true;
                }
                showLoading();
            }
        });
    }
});

function selectSuggestion(anime) {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = anime;
        document.getElementById('searchSuggestions').classList.remove('show');
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            showLoading();
            searchForm.submit();
        }
    }
}

function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
    }
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

window.addEventListener('load', function() {
    hideLoading();
    const searchButton = document.getElementById('searchButton');
    if (searchButton) {
        searchButton.innerHTML = '<i class="fas fa-arrow-right"></i>';
        searchButton.disabled = false;
    }
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

// Keyboard shortcut Ctrl+K
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
});
</script>