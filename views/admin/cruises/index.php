<?php require_once 'views/admin/layout/header.php'; ?>

<?php displayFlashMessages(); ?>

<div class="admin-container">
    <div class="card">
        <div class="card-header">
            <h2>Liste des croisières</h2>
            <a href="/admin/cruises/create" class="btn-add">Ajouter</a>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Port Départ</th>
                    <th>Date Départ</th>
                    <th>Durée</th>
                    <th>Prix</th>
                    <th>Cabines</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($cruises) && !empty($cruises)):
                    foreach ($cruises as $cruise): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cruise['id']); ?></td>
                            <td>
                                <?php if (!empty($cruise['image'])): ?>
                                    <img src="/public/images/cruises/<?php echo htmlspecialchars($cruise['image']); ?>" 
                                         alt="Image de <?php echo htmlspecialchars($cruise['name']); ?>" 
                                         style="max-height: 40px; max-width: 60px; object-fit: cover;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($cruise['name']); ?></td>
                            <td><?php echo htmlspecialchars($cruise['departure_port']); ?></td>
                            <td><?php echo isset($cruise['departure_date']) ? htmlspecialchars(date('d/m/Y', strtotime($cruise['departure_date']))) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($cruise['duration_days']); ?> jours</td>
                            <td>€<?php echo htmlspecialchars(number_format($cruise['price'], 2, ',', ' ')); ?></td>
                            <td><?php echo htmlspecialchars($cruise['available_cabins']); ?></td>
                            <td class="table-actions">
                                <a href="/admin/cruises/edit/<?php echo $cruise['id']; ?>" class="edit-link">Modifier</a>
                                <form action="/admin/cruises/delete/<?php echo $cruise['id']; ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette croisière ?');">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                <?php 
                    endforeach;
                else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Aucune croisière trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?>

<script>
const userMenuButton = document.getElementById('user-menu-button');
const userMenu = document.getElementById('user-menu');
if (userMenuButton && userMenu) {
    userMenuButton.addEventListener('click', function() {
        userMenu.classList.toggle('show');
    });
    document.addEventListener('click', function(e) {
        if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.remove('show');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.search-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const type = form.querySelector('[name="type"]').value;
            const destination = form.querySelector('[name="destination"]').value;
            const date = form.querySelector('[name="date"]').value;
            let url = '/';
            if (type === 'flight') {
                url = `/flights?departure=${encodeURIComponent(destination)}${date ? '&date=' + encodeURIComponent(date) : ''}`;
            } else if (type === 'hotel') {
                url = `/hotels?destination=${encodeURIComponent(destination)}${date ? '&checkin=' + encodeURIComponent(date) : ''}`;
            } else if (type === 'cruise') {
                url = `/cruises?destination=${encodeURIComponent(destination)}${date ? '&date=' + encodeURIComponent(date) : ''}`;
            } else {
                return; // fallback to default
            }
            window.location.href = url;
            e.preventDefault();
        });
    }
});
</script> 

<style>
.user-menu-dropdown { display: none; position: absolute; /* ... */ }
.user-menu-dropdown.show { display: block; }
</style> 