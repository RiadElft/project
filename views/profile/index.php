<?php 
$pageTitle = 'Mon Profil';
require_once 'views/layout/header.php'; 
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Mon Profil</h1>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="mb-4 p-4 rounded <?php echo $_SESSION['flash']['type'] === 'danger' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
            <?php echo $_SESSION['flash']['message']; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Informations Personnelles</h2>
                <form action="/profile/update" method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" 
                               class="form-input mt-1 block w-full" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                               class="form-input mt-1 block w-full" required>
                    </div>
                    <div class="pt-4 border-t">
                        <h3 class="text-lg font-medium mb-4">Changer le mot de passe</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                                <input type="password" id="current_password" name="current_password" 
                                       class="form-input mt-1 block w-full">
                            </div>
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="form-input mt-1 block w-full">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Booking Statistics -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Statistiques</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Total des réservations</p>
                        <p class="text-2xl font-bold"><?php echo number_format($stats['total_bookings']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Montant total dépensé</p>
                        <p class="text-2xl font-bold"><?php echo formatPriceWithCurrency($stats['total_spent']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-semibold mb-6">Réservations Récentes</h2>
                <?php if (!empty($stats['recent_bookings'])): ?>
                    <div class="space-y-4">
                        <?php foreach ($stats['recent_bookings'] as $booking): ?>
                            <div class="border-b pb-4 last:border-b-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium"><?php echo htmlspecialchars($booking['item_name']); ?></p>
                                        <p class="text-sm text-gray-600">
                                            <?php echo date('d/m/Y', strtotime($booking['created_at'])); ?>
                                        </p>
                                    </div>
                                    <span class="text-primary font-semibold">
                                        €<?php echo formatPriceWithCurrency($booking['total_price']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center">Aucune réservation récente</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?> 