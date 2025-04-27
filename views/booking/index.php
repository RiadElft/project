<?php require_once 'views/layout/header.php'; ?>

<div class="container section">
    <h1 class="heading-2 mb-8">Mes Réservations</h1>
    <div class="card">
        <div class="card-body">
            <?php if (!empty($bookings)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Détail</th>
                            <th>Prix (€)</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($booking['booking_type'])); ?></td>
                                <td>
                                    <?php if ($booking['booking_type'] === 'flight' && !empty($booking['details'])): ?>
                                        Vol <?php echo htmlspecialchars($booking['details']['flight_number'] ?? ''); ?> (<?php echo htmlspecialchars($booking['details']['departure_city'] ?? ''); ?> → <?php echo htmlspecialchars($booking['details']['arrival_city'] ?? ''); ?>)
                                    <?php elseif ($booking['booking_type'] === 'hotel' && !empty($booking['details'])): ?>
                                        Hôtel <?php echo htmlspecialchars($booking['details']['name'] ?? ''); ?> (<?php echo htmlspecialchars($booking['details']['city'] ?? ''); ?>)
                                    <?php elseif ($booking['booking_type'] === 'cruise' && !empty($booking['details'])): ?>
                                        Croisière <?php echo htmlspecialchars($booking['details']['name'] ?? ''); ?> (<?php echo htmlspecialchars($booking['details']['departure_port'] ?? ''); ?>)
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars(number_format($booking['total_price'], 2, ',', ' ')); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                        <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($booking['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="card-text center">Aucune réservation trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?> 