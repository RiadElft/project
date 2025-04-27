<?php require_once 'helpers/currency.php'; ?>
<?php require_once 'views/admin/layout/header.php'; ?>

<?php displayFlashMessages(); ?>

<div class="admin-container">
    <div class="card">
        <div class="card-header">
            <h2>Liste des hôtels</h2>
            <a href="/admin/hotels/create" class="btn-add">Ajouter</a>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Ville</th>
                    <th>Pays</th>
                    <th>Prix/Nuit</th>
                    <th>Note</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($hotels) && !empty($hotels)):
                    foreach ($hotels as $hotel): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($hotel['id']); ?></td>
                            <td>
                                <?php if (!empty($hotel['image'])): ?>
                                    <img src="/public/images/hotels/<?php echo htmlspecialchars($hotel['image']); ?>" 
                                         alt="Image de <?php echo htmlspecialchars($hotel['name']); ?>" 
                                         style="max-height: 40px; max-width: 60px; object-fit: cover;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($hotel['name']); ?></td>
                            <td><?php echo htmlspecialchars($hotel['city']); ?></td>
                            <td><?php echo htmlspecialchars($hotel['country']); ?></td>
                            <td><?php echo formatPriceWithCurrency($hotel['price_per_night']); ?></td>
                            <td><?php echo htmlspecialchars($hotel['rating']); ?> ⭐</td>
                            <td>
                                 <span class="badge badge-<?php echo $hotel['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($hotel['status'])); ?>
                                </span>
                            </td>
                            <td class="table-actions">
                                <a href="/admin/hotels/edit/<?php echo $hotel['id']; ?>" class="edit-link">Modifier</a>
                                <form action="/admin/hotels/delete/<?php echo $hotel['id']; ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet hôtel ?');">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                <?php 
                    endforeach;
                else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Aucun hôtel trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 