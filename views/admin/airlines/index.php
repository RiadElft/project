<?php require_once 'views/admin/layout/header.php'; ?>

<?php displayFlashMessages(); ?>

<div class="admin-container">
    <div class="card">
        <div class="card-header">
            <h2>Liste des compagnies</h2>
            <a href="/admin/airlines/create" class="btn-add">Ajouter</a>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Logo</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($airlines)): ?>
                    <?php foreach ($airlines as $airline): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($airline['id']); ?></td>
                            <td><?php echo htmlspecialchars($airline['name']); ?></td>
                            <td>
                                <?php if (!empty($airline['logo_image'])): ?>
                                    <img src="/public/images/airlines/<?php echo htmlspecialchars($airline['logo_image']); ?>" 
                                         alt="Logo de <?php echo htmlspecialchars($airline['name']); ?>" 
                                         style="max-height: 30px; max-width: 100px;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo nl2br(htmlspecialchars(substr($airline['description'] ?? '', 0, 100))); ?>...</td>
                            <td class="table-actions">
                                <a href="/admin/airlines/edit/<?php echo $airline['id']; ?>" class="edit-link">Modifier</a>
                                <form action="/admin/airlines/delete/<?php echo $airline['id']; ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette compagnie ? Les vols associés ne seront plus liés.');">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucune compagnie aérienne trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 