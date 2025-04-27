<?php 
$pageTitle = 'Croisières'; // Translated page title
require_once 'views/layout/header.php'; 
require_once 'helpers/currency.php';
?>

<div class="container section">
    <h1 class="heading-2 mb-8">Trouver une Croisière</h1>

    <!-- Optional: Search/Filter Bar -->
    <div class="card mb-8">
        <div class="card-body">
            <form action="/cruises" method="GET" class="search-grid">
                <div class="form-group">
                    <label for="destination" class="form-label">Destination</label>
                    <input type="text" id="destination" name="destination" class="form-input">
                </div>
                <div class="form-group">
                    <label for="departure_port" class="form-label">Port de Départ</label>
                    <input type="text" id="departure_port" name="departure_port" class="form-input">
                </div>
                <div class="form-group">
                    <label for="date" class="form-label">Date de Départ (après)</label>
                    <input type="date" id="date" name="date" class="form-input">
                </div>
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>
    </div>

    <!-- Cruise Listings -->
    <div class="grid grid-cols-3">
        <?php if (isset($cruises) && !empty($cruises)): ?>
            <?php foreach ($cruises as $cruise): ?>
                <div class="card">
                    <img src="/public/images/cruises/<?php echo htmlspecialchars($cruise['image']); ?>" 
                         alt="<?php echo htmlspecialchars($cruise['name']); ?>" 
                         class="card-image">
                    <div class="card-body">
                        <h2 class="card-title mb-1"><?php echo htmlspecialchars($cruise['name']); ?></h2>
                        <p class="card-text mb-2">
                            <?php echo htmlspecialchars($cruise['cruise_line']); ?> - <?php echo htmlspecialchars($cruise['duration_days']); ?> jours
                        </p>
                        <p class="card-text mb-2">
                            Départ de: <?php echo htmlspecialchars($cruise['departure_port']); ?>
                        </p>
                        <span class="cruise-price">
                            À partir de <?php echo formatPriceWithCurrency($cruise['price']); ?>
                        </span>
                        <a href="/book/cruise/<?php echo $cruise['id']; ?>" class="btn btn-primary btn-full-mobile mt-6">Voir les détails</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="card-text center">Aucune croisière trouvée correspondant à vos critères.</p>
        <?php endif; ?>
    </div>

</div>

<?php require_once 'views/layout/footer.php'; ?> 