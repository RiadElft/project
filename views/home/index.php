<?php 
$pageTitle = 'Accueil';
require_once 'views/layout/header.php'; 
?>

<div class="hero" style="background-image: url('/public/images/hero-bg.jpg');">
    <div class="main-container">
        <div class="hero-content">
            <?php if (isset($_SESSION['user_name'])): ?>
                <h1 class="hero-title">Bon retour, <?php echo htmlspecialchars($_SESSION['user_name']); ?> !</h1>
                <p class="hero-subtitle">Prêt(e) à planifier votre prochain voyage ?</p>
            <?php else: ?>
                <h1 class="hero-title">Bienvenue sur Voyage</h1>
                <p class="hero-subtitle">Trouvez et réservez vos vols, hôtels et croisières facilement.</p>
            <?php endif; ?>

            <div class="search-panel">
                <form action="/search" method="GET" class="search-form space-y">
                    <div class="grid-container grid-1-col grid-md-3-col">
                        <div class="form-group">
                            <label class="form-label">Type de Voyage</label>
                            <select name="type" class="form-control">
                                <option value="flight">Vol</option>
                                <option value="hotel">Hôtel</option>
                                <option value="cruise">Croisière</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Où aller ?</label>
                            <input type="text" name="destination" class="form-control" placeholder="Entrez la destination">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Quand ?</label>
                            <input type="date" name="date" class="form-control">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <span>Rechercher</span>
                            <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="main-container page-section">
    <section class="featured-section">
        <h2 class="section-title">Destinations à la Une</h2>

        <div class="grid-container grid-1-col grid-md-3-col">
            <?php if (isset($featured_destinations) && !empty($featured_destinations)): ?>
                <?php foreach ($featured_destinations as $destination): ?>
                    <div class="card destination-card">
                        <div class="destination-image">
                            <img src="<?php echo htmlspecialchars($destination['image']); ?>"
                                 alt="<?php echo htmlspecialchars($destination['name']); ?>"
                                 class="img-cover">
                        </div>
                        <div class="destination-content">
                            <h3 class="destination-name">
                                <?php echo htmlspecialchars($destination['name']); ?>
                            </h3>
                            <p class="destination-description">
                                <?php echo htmlspecialchars($destination['description_fr']); ?>
                            </p>
                            <a href="/destination/<?php echo $destination['id']; ?>" class="btn btn-primary">
                                Découvrir
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-results">Aucune destination à la une disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="features-section grid-container grid-1-col grid-md-3-col">
        <div class="card feature-card">
            <h3 class="feature-title">Meilleurs Prix</h3>
            <p class="feature-description">Nous garantissons les meilleurs tarifs pour vos besoins de voyage</p>
        </div>

        <div class="card feature-card">
            <h3 class="feature-title">Support 24/7</h3>
            <p class="feature-description">Notre service client est toujours là pour vous aider</p>
        </div>

        <div class="card feature-card">
            <h3 class="feature-title">Réservation Facile</h3>
            <p class="feature-description">Processus de réservation simple et sécurisé</p>
        </div>
    </section>
</div>

<?php require_once 'views/layout/footer.php'; ?>