<?php 
$pageTitle = 'Tableau de bord'; // Translated page title
require_once 'views/layout/header.php'; 
?>

<div class="hero" style="background-image: url('/public/images/ccc.jpg');">
    <div class="container py-12">
        <div class="hero-content">
            <h1 class="heading-1 mb-4">Bon retour, <?php echo htmlspecialchars($_SESSION['user_name']); ?> !</h1>
            <p class="heading-2 mb-8">Prêt(e) à planifier votre prochain voyage ?</p>
            
            <!-- Search Form -->
            <div class="search-panel">
                <form action="/search" method="GET" class="search-form">
                    <div class="search-grid">
                        <div class="form-group">
                            <label class="form-label">Type de Voyage</label>
                            <select name="type" class="form-select">
                                <option value="">Sélectionnez le Type de Voyage</option>
                                <option value="flight">Vol</option>
                                <option value="hotel">Hôtel</option>
                                <option value="cruise">Croisière</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Où aller ?</label>
                            <input type="text" name="destination" placeholder="Entrez la destination" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quand ?</label>
                            <input type="date" name="date" class="form-input">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full-mobile md:w-auto">
                        Rechercher des Voyages
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container py-12">
    <!-- Featured Destinations -->
    <section class="mb-12">
        <h2 class="heading-2 mb-8">Destinations à la Une</h2>
        <?php if (isset($featured_destinations) && !empty($featured_destinations)): ?>
        <div class="grid grid-cols-3">
            <?php foreach ($featured_destinations as $destination): ?>
            <div class="card">
                <img src="/public/images/destinations/<?php echo htmlspecialchars($destination['image']); ?>" 
                     alt="<?php echo htmlspecialchars($destination['city']); ?>" 
                     class="card-image">
                <div class="card-body">
                    <h3 class="card-title">
                        <?php echo htmlspecialchars($destination['city']); ?>, 
                        <?php echo htmlspecialchars($destination['country']); ?>
                    </h3>
                    <p class="card-text"><?php echo htmlspecialchars($destination['description_fr']); ?></p>
                    <a href="/book/<?php echo strtolower(htmlspecialchars($destination['city'])); ?>" 
                       class="btn btn-primary">
                        Réserver Maintenant
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p class="card-text">Aucune destination à la une disponible pour le moment.</p>
        <?php endif; ?>
    </section>

    <!-- Why Choose Us -->
    <section class="grid grid-cols-3">
        <div class="card">
            <div class="card-body">
                <h3 class="heading-3 mb-4">Meilleurs Prix</h3>
                <p class="card-text">Nous garantissons les meilleurs tarifs pour vos besoins de voyage</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3 class="heading-3 mb-4">Support 24/7</h3>
                <p class="card-text">Notre service client est toujours là pour vous aider</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3 class="heading-3 mb-4">Réservation Facile</h3>
                <p class="card-text">Processus de réservation simple et sécurisé</p>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.search-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const type = form.querySelector('[name="type"]').value;
            const destination = form.querySelector('[name="destination"]').value;
            const date = form.querySelector('[name="date"]').value;
            let url = '/';
            if (type === 'flight') {
                url = `/flights?departure=${encodeURIComponent(destination)}${date ? '&date=' + encodeURIComponent(date) : ''}`;
            } else if (type === 'hotel') {
                url = `/hotels?destination=${encodeURIComponent(destination)}${date ? '&checkin=' + encodeURIComponent(date) : ''}`;
            } else if (type === 'cruise') {
                url = `/cruises?destination=${encodeURIComponent(destination)}${date ? '&date=' + encodeURIComponent(date) : ''}`;
            } else {
                return; // fallback to default
            }
            window.location.href = url;
            e.preventDefault();
        });
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?> 