<?php require_once 'views/admin/layout/header.php'; ?>

<?php displayFlashMessages(); // Ensure this helper outputs compatible HTML (see Step 5) ?>

<!-- Centering Container -->
<div class="grid-container">
    <!-- Stats Grid -->
    <div class="stats-grid mb-4">
        <div class="card">
            <h2 class="card-title">Compagnies</h2>
            <p class="card-stat"><?php echo $stats['airlines']; ?></p>
            <a href="/admin/airlines" class="card-link btn btn-sm btn-secondary">Gérer</a>
        </div>
        <div class="card">
            <h2 class="card-title">Vols</h2>
            <p class="card-stat"><?php echo $stats['flights']; ?></p>
            <a href="/admin/flights" class="card-link btn btn-sm btn-secondary">Gérer</a>
        </div>
        <div class="card">
            <h2 class="card-title">Hôtels</h2>
            <p class="card-stat"><?php echo $stats['hotels']; ?></p>
            <a href="#" class="card-link btn btn-sm btn-secondary disabled">Gérer</a>
        </div>
        <div class="card">
            <h2 class="card-title">Croisières</h2>
            <p class="card-stat"><?php echo $stats['cruises']; ?></p>
            <a href="#" class="card-link btn btn-sm btn-secondary disabled">Gérer</a>
        </div>
        <div class="card">
            <h2 class="card-title">Réservations</h2>
            <p class="card-stat"><?php echo $stats['bookings']; // Placeholder ?></p>
            <a href="#" class="card-link btn btn-sm btn-secondary disabled">Voir</a>
        </div>
        <div class="card">
            <h2 class="card-title">Utilisateurs</h2>
            <p class="card-stat"><?php echo $stats['users']; ?></p>
            <a href="#" class="card-link btn btn-sm btn-secondary disabled">Gérer)</a>
        </div>
    </div>
</div> <!-- End Centering Container -->

<?php require_once 'views/admin/layout/footer.php'; ?> 