<?php 
$pageTitle = 'Vols'; // Translated page title
require_once 'views/layout/header.php'; 
require_once 'helpers/currency.php';
?>

<div class="container section">
    <h1 class="heading-2 mb-8">Trouver un Vol</h1>

    <!-- Optional: Search/Filter Bar -->
    <div class="card mb-8">
        <div class="card-body">
            <form action="/flights" method="GET" class="search-grid">
                <div class="form-group">
                    <label for="departure" class="form-label">Ville de Départ</label>
                    <input type="text" id="departure" name="departure" class="form-input" placeholder="Ex: Paris">
                </div>
                <div class="form-group">
                    <label for="arrival" class="form-label">Ville d'Arrivée</label>
                    <input type="text" id="arrival" name="arrival" class="form-input" placeholder="Ex: New York">
                </div>
                <div class="form-group">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" id="date" name="date" class="form-input">
                </div>
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>
    </div>

    <!-- Flight Listings -->
    <div class="flight-listings">
        <?php if (isset($flights) && !empty($flights)): ?>
            <?php foreach ($flights as $flight): ?>
                <div class="card flight-card">
                    <div class="flight-info">
                        <div class="flight-header">
                            <div class="airline-info">
                                <?php if (!empty($flight['airline_logo'])): ?>
                                <img src="/public/images/airlines/<?php echo htmlspecialchars($flight['airline_logo']); ?>" 
                                     alt="<?php echo htmlspecialchars($flight['airline_name']); ?> Logo" 
                                     class="airline-logo"> 
                                <?php endif; ?>
                                <div>
                                    <h2 class="card-title"><?php echo htmlspecialchars($flight['airline_name']); ?></h2>
                                    <p class="flight-route">
                                        <?php echo htmlspecialchars($flight['departure_city']); ?> 
                                        <svg xmlns="http://www.w3.org/2000/svg" class="route-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                        <?php echo htmlspecialchars($flight['arrival_city']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <span class="flight-price">
                            <?php echo formatPriceWithCurrency($flight['price']); ?>
                        </span>
                        <div class="flight-details">
                            <p>Départ: <?php echo date('d/m/Y H:i', strtotime($flight['departure_time'])); ?></p>
                            <p>Arrivée: <?php echo date('d/m/Y H:i', strtotime($flight['arrival_time'])); ?></p>
                            <p>Escales: <?php echo $flight['stops'] == 0 ? 'Direct' : htmlspecialchars($flight['stops']); ?></p>
                        </div>
                    </div>
                    <div class="flight-action">
                        <a href="/book/flight/<?php echo $flight['id']; ?>" class="btn btn-primary">Sélectionner</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="card-text center">Aucun vol trouvé correspondant à vos critères.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?> 