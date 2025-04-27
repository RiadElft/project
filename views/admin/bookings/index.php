<?php require_once 'views/admin/layout/header.php'; ?>

<?php displayFlashMessages(); ?>

<div class="admin-container">
    <div class="card">
        <div class="card-header">
            <h2>Liste des réservations</h2>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Type</th>
                    <th>Détail Item</th>
                    <th>Prix (€)</th>
                    <th>Statut</th>
                    <th>Date Réservation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($bookings) && !empty($bookings)):
                    foreach ($bookings as $booking): 
                        $details = $booking['details']; // Extracted details (flight/hotel/cruise)
                        $itemName = 'N/A'; // Default item name
                        if ($details) {
                            switch ($booking['booking_type']) {
                                case 'flight':
                                    $itemName = htmlspecialchars($details['flight_number'] . ' (' . $details['departure_city'] . ' -> ' . $details['arrival_city'] . ')');
                                    break;
                                case 'hotel':
                                    $itemName = htmlspecialchars($details['name'] . ' (' . $details['city'] . ')');
                                    break;
                                case 'cruise':
                                    $itemName = htmlspecialchars($details['name'] . ' (' . $details['departure_port'] . ')');
                                    break;
                            }
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($booking['user_name']); ?><br>
                                <small><?php echo htmlspecialchars($booking['user_email']); ?></small>
                            </td>
                            <td><?php echo ucfirst(htmlspecialchars($booking['booking_type'])); ?></td>
                            <td><?php echo $itemName; ?></td>
                            <td><?php echo htmlspecialchars(number_format($booking['total_price'], 2, ',', ' ')); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                    <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($booking['created_at']))); ?></td>
                            <td class="table-actions">
                                <a href="#" class="edit-link disabled">Détails</a>
                                <?php if ($booking['status'] !== 'cancelled'): ?>
                                    <a href="/admin/bookings/cancel/<?php echo $booking['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Annuler cette réservation ?');">Annuler</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                <?php 
                    endforeach;
                else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Aucune réservation trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 