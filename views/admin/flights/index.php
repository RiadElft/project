<?php require_once 'views/admin/layout/header.php'; ?>

<?php displayFlashMessages(); ?>

<div class="admin-container">
    <div class="card">
        <div class="card-header">
            <h2>Liste des vols</h2>
            <a href="/admin/flights/create" class="btn-add">Ajouter</a>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Compagnie</th>
                    <th>N° Vol</th>
                    <th>Départ</th>
                    <th>Arrivée</th>
                    <th>Date Départ</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($flights) && !empty($flights)): ?>
                    <?php foreach ($flights as $flight): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flight['id']); ?></td>
                            <td>
                                <?php if (!empty($flight['airline_logo'])): ?>
                                    <span style="font-size: 0.8em; display: inline-block; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 5px; vertical-align: middle;">
                                        <?php echo htmlspecialchars($flight['airline_logo']); ?>
                                    </span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($flight['airline_name'] ?? 'N/A'); ?>
                            </td>
                            <td><?php echo htmlspecialchars($flight['flight_number']); ?></td>
                            <td><?php echo htmlspecialchars($flight['departure_city']); ?></td>
                            <td><?php echo htmlspecialchars($flight['arrival_city']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($flight['departure_time']))); ?></td>
                            <td>€<?php echo htmlspecialchars(number_format($flight['price'], 2, ',', ' ')); ?></td>
                            <td class="table-actions">
                                <a href="/admin/flights/edit/<?php echo $flight['id']; ?>" class="edit-link">Modifier</a>
                                <form action="/admin/flights/delete/<?php echo $flight['id']; ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce vol ?');">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Aucun vol trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="card-footer">
            <div class="pagination-container">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 