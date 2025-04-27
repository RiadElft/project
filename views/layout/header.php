<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' . APP_NAME : APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/main.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <?php require_once 'views/components/currency_switcher.php'; ?>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="<?php echo isset($_SESSION['user_id']) ? '/dashboard' : '/'; ?>" class="logo-container">
                    <img src="/public/images/generated-image.png" alt="TravelEase Logo" class="logo-image">
                </a>
                <div class="navbar-links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/dashboard" class="nav-link">Tableau de bord</a>
                        <a href="/flights" class="nav-link">Vols</a>
                        <a href="/hotels" class="nav-link">Hôtels</a>
                        <a href="/cruises" class="nav-link">Croisières</a>
                        <a href="/bookings" class="nav-link">Mes Réservations</a>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="/admin/dashboard" class="nav-link nav-link-admin">Admin</a>
                        <?php endif; ?>
                        <div class="user-menu">
                            <button type="button" class="user-menu-btn" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                <span class="user-avatar">
                                    <svg class="user-avatar-icon" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                            </button>
                            <div class="user-menu-dropdown" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="user-menu">
                                <a href="/profile" class="user-menu-item" role="menuitem" tabindex="-1">Votre Profil</a>
                                <a href="/logout" class="user-menu-item user-menu-item-logout" role="menuitem" tabindex="-1">Déconnexion</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="nav-link">Connexion</a>
                        <a href="/register" class="btn btn-primary">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mt-6">
        <div class="flash <?php echo $_SESSION['flash_type'] === 'danger' ? 'flash-error' : 'flash-success'; ?>">
            <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
        </div>
    </div>
    <?php 
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
    endif; ?>
</body>
<script>
const userMenuButton = document.getElementById('user-menu-button');
const userMenu = document.getElementById('user-menu');
if (userMenuButton && userMenu) {
    userMenuButton.addEventListener('click', function(e) {
        e.stopPropagation();
        userMenu.classList.toggle('show');
    });
    document.addEventListener('click', function(e) {
        if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.remove('show');
        }
    });
}
</script>
<style>
.user-menu { position: relative; display: inline-block; }
.user-menu-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: #fff;
    border: 1px solid #ddd;
    min-width: 160px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    z-index: 1000;
}
.user-menu-dropdown.show { display: block; }
.user-menu-item {
    display: block;
    padding: 10px 16px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s;
}
.user-menu-item:hover { background: #f5f5f5; }
.user-menu-item-logout { color: #c00; }
</style>
</html>