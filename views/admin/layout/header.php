<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : APP_NAME; ?></title>
    <!-- Link to your custom admin CSS file -->
    <link rel="stylesheet" href="/public/css/admin_style.css">
    <!-- You might want to add other meta tags or common head elements here -->
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="/admin/dashboard" class="brand-link">Admin <?php echo APP_NAME; ?></a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="/admin/dashboard" class="nav-link <?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>">Tableau de bord</a></li>
                <li><a href="/admin/flights" class="nav-link <?php echo ($current_page ?? '') === 'flights' ? 'active' : ''; ?>">Gérer les Vols</a></li>
                <li><a href="/admin/airlines" class="nav-link <?php echo ($current_page ?? '') === 'airlines' ? 'active' : ''; ?>">Gérer les Compagnies</a></li>
                <li><a href="/admin/hotels" class="nav-link <?php echo ($current_page ?? '') === 'hotels' ? 'active' : ''; ?>">Gérer les Hôtels</a></li>
                <li><a href="/admin/cruises" class="nav-link <?php echo ($current_page ?? '') === 'cruises' ? 'active' : ''; ?>">Gérer les Croisières</a></li>
                <li><a href="/admin/bookings" class="nav-link <?php echo ($current_page ?? '') === 'bookings' ? 'active' : ''; ?>">Gérer les Réservations</a></li>
                <li><a href="/admin/users" class="nav-link <?php echo ($current_page ?? '') === 'users' ? 'active' : ''; ?>">Gérer les Utilisateurs</a></li>
                <!-- Add other admin links as needed -->
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="/dashboard" class="action-link view-site-link">Voir le Site Public</a>
            <a href="/logout" class="action-link logout-link">Déconnexion</a>
        </div>
    </aside>

    <!-- Main Content Area -->
   
</body>
</html> 