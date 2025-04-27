<?php 
$pageTitle = 'Confirmer la Réservation';
require_once 'views/layout/header.php'; 
require_once 'helpers/functions.php';

// Determine the correct price field based on booking type
$displayPrice = 0;
if ($type === 'flight' && isset($item['price'])) {
    $displayPrice = $item['price'];
} elseif ($type === 'hotel' && isset($item['price_per_night'])) {
    $displayPrice = $item['price_per_night'];
} elseif ($type === 'cruise' && isset($item['price'])) {
    $displayPrice = $item['price'];
}
?>

<div class="container section">
    <h1 class="heading-2 mb-8">Confirmer votre Réservation</h1>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash <?php echo $_SESSION['flash']['type'] === 'danger' ? 'flash-error' : 'flash-success'; ?> mb-4">
            <?php echo $_SESSION['flash']['message']; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Booking Details Form -->
        <div class="md:col-span-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Informations de Contact</h2>
                    <form action="/booking/confirm" method="POST">
                        <input type="hidden" name="booking_type" value="<?php echo htmlspecialchars($type); ?>">
                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                        <div class="form-group">
                            <label for="contact_name" class="form-label">Nom Complet</label>
                            <input type="text" id="contact_name" name="contact_name" required class="form-input" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="contact_email" class="form-label">Email</label>
                            <input type="email" id="contact_email" name="contact_email" required class="form-input" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="contact_phone" class="form-label">Téléphone</label>
                            <input type="tel" id="contact_phone" name="contact_phone" class="form-input" placeholder="+33 6 12 34 56 78">
                        </div>
                        <div class="form-group">
                            <label for="passengers" class="form-label">Nombre de Voyageurs</label>
                            <select id="passengers" name="passengers" class="form-select" onchange="updateTotalPrice()">
                                <?php for($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <?php if ($type === 'flight'): ?>
                        <div class="form-group">
                            <h3 class="card-title mb-2">Détails du Vol</h3>
                            <div class="form-group">
                                <label class="form-label">Compagnie</label>
                                <p class="card-text"><?php echo htmlspecialchars($item['airline_name']); ?></p>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Numéro de Vol</label>
                                <p class="card-text"><?php echo htmlspecialchars($item['flight_number'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <h3 class="card-title mb-2">Prix Total</h3>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="card-text">Prix par personne:</p>
                                    <p class="heading-3" id="price-per-person">
                                        <?php echo formatPrice($displayPrice); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="card-text">Prix total:</p>
                                    <p class="heading-3 text-primary" id="total-price">
                                        <?php echo formatPrice($displayPrice); ?>
                                    </p>
                                </div>
                            </div>
                            <input type="hidden" name="total_price" id="total-price-input" value="<?php echo $displayPrice; ?>">
                        </div>
                        <div class="form-group" style="text-align: right;">
                            <a href="javascript:history.back()" class="btn btn-secondary" style="margin-right: 10px;">Retour</a>
                            <button type="submit" class="btn btn-primary">Confirmer la Réservation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Summary Card -->
        <div class="md:col-span-1">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Résumé</h2>
                    <?php if ($type === 'flight'): ?>
                    <div class="mb-2">
                        <?php if (!empty($item['logo_image'])): ?>
                        <img src="/public/images/airlines/<?php echo htmlspecialchars($item['logo_image']); ?>" alt="<?php echo htmlspecialchars($item['airline_name']); ?>" class="airline-logo mb-2">
                        <?php endif; ?>
                        <span class="card-title"><?php echo htmlspecialchars($item['airline_name']); ?></span>
                    </div>
                    <div class="card-text mb-2">
                        <span><?php echo htmlspecialchars($item['departure_city']); ?> &rarr; <?php echo htmlspecialchars($item['arrival_city']); ?></span><br>
                        <span>Départ: <?php echo date('H:i', strtotime($item['departure_time'])); ?> | Arrivée: <?php echo date('H:i', strtotime($item['arrival_time'])); ?></span>
                    </div>
                    <?php elseif ($type === 'hotel'): ?>
                    <div class="mb-2">
                        <span class="card-title"><?php echo htmlspecialchars($item['name']); ?></span>
                    </div>
                    <div class="card-text mb-2">
                        <span><?php echo htmlspecialchars($item['city']); ?>, <?php echo htmlspecialchars($item['country']); ?></span>
                    </div>
                    <div class="card-text mb-2">
                        <span>Équipements: <?php echo htmlspecialchars($item['amenities']); ?></span>
                    </div>
                    <?php elseif ($type === 'cruise'): ?>
                    <div class="mb-2">
                        <span class="card-title"><?php echo htmlspecialchars($item['name']); ?></span>
                    </div>
                    <div class="card-text mb-2">
                        <span><?php echo htmlspecialchars($item['itinerary']); ?></span>
                    </div>
                    <div class="card-text mb-2">
                        <span>Durée: <?php echo htmlspecialchars($item['duration']); ?> jours</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateTotalPrice() {
    const basePrice = <?php echo $displayPrice; ?>;
    const passengers = document.getElementById('passengers').value;
    const totalPrice = basePrice * passengers;
    document.getElementById('total-price').textContent = new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(totalPrice);
    document.getElementById('total-price-input').value = totalPrice;
}
</script>

<?php require_once 'views/layout/footer.php'; ?> 