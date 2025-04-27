<?php
// Determine the current page to set the active class
$currentPage = $current_page ?? ''; 

// Get base path for URLs
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'dashboard') ? 'active' : ''; ?>" aria-current="page" href="/admin/dashboard">
                    <span data-feather="home"></span>
                    Tableau de Bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'flights') ? 'active' : ''; ?>" href="/admin/flights">
                    <span data-feather="send"></span> 
                    Gérer les Vols
                </a>
            </li>
            <!-- Add Airlines Link Here -->
             <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'airlines') ? 'active' : ''; ?>" href="/admin/airlines">
                     <span data-feather="navigation"></span> <!-- Example icon -->
                    Gérer les Compagnies
                </a>
            </li>
            <!-- TODO: Add other management links (Hotels, Cruises, Users) -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'users') ? 'active' : ''; ?>" href="#">
                    <span data-feather="users"></span>
                    Gérer les Utilisateurs
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'hotels') ? 'active' : ''; ?>" href="#">
                    <span data-feather="briefcase"></span> <!-- Example icon -->
                    Gérer les Hôtels
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage === 'cruises') ? 'active' : ''; ?>" href="#">
                     <span data-feather="anchor"></span> <!-- Example icon -->
                    Gérer les Croisières
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Autres Actions</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="/logout">
                    <span data-feather="log-out"></span>
                    Déconnexion
                </a>
            </li>
            <li class="nav-item">
                 <a class="nav-link" href="/dashboard">
                    <span data-feather="arrow-left"></span>
                    Retour au site public
                </a>
            </li>
        </ul>
    </div>
</nav> 