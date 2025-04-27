<?php 
$pageTitle = 'Hôtels'; // Translated page title
require_once 'views/layout/header.php'; 
require_once 'helpers/currency.php';
?>

<div class="container section">
    <h1 class="heading-2 mb-8">Trouver un Hôtel</h1>

    <!-- Optional: Search/Filter Bar -->
    <div class="card mb-8">
        <div class="card-body">
            <form action="/hotels" method="GET" class="search-grid">
                <div class="form-group">
                    <label for="destination" class="form-label">Destination</label>
                    <input type="text" id="destination" name="destination" class="form-input">
                </div>
                <div class="form-group">
                    <label for="checkin" class="form-label">Arrivée</label>
                    <input type="date" id="checkin" name="checkin" class="form-input">
                </div>
                <div class="form-group">
                    <label for="checkout" class="form-label">Départ</label>
                    <input type="date" id="checkout" name="checkout" class="form-input">
                </div>
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>
    </div>

    <!-- Hotel Listings -->
    <div class="grid grid-cols-3">
        <?php if (isset($hotels) && !empty($hotels)): ?>
            <?php foreach ($hotels as $hotel): ?>
                <div class="card">
                    <img src="/public/images/hotels/<?php echo htmlspecialchars($hotel['image']); ?>" 
                         alt="<?php echo htmlspecialchars($hotel['name']); ?>" 
                         class="card-image">
                    <div class="card-body">
                        <h2 class="card-title mb-1"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                        <p class="card-text mb-2">
                            <?php echo htmlspecialchars($hotel['city']); ?>, 
                            <?php echo htmlspecialchars($hotel['country']); ?>
                        </p>
                        <div class="hotel-rating mb-2">
                            <?php for($i = 0; $i < 5; $i++): ?>
                                <svg class="hotel-star <?php echo $i < $hotel['rating'] ? 'hotel-star-filled' : 'hotel-star-empty'; ?>" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            <?php endfor; ?>
                            <span class="hotel-rating-label">(<?php echo htmlspecialchars($hotel['rating']); ?> étoiles)</span>
                        </div>
                        <span class="hotel-price">
                            <?php echo formatPriceWithCurrency($hotel['price_per_night']); ?> <span class="hotel-price-unit">/ nuit</span>
                        </span>
                        <a href="/book/hotel/<?php echo $hotel['id']; ?>" class="btn btn-primary btn-full-mobile mt-6">Voir les disponibilités</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="card-text center">Aucun hôtel trouvé correspondant à vos critères.</p>
        <?php endif; ?>
    </div>

</div>

<?php require_once 'views/layout/footer.php'; ?> 