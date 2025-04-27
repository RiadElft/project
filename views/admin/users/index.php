<?php require_once 'views/admin/layout/header.php'; ?>

<?php displayFlashMessages(); ?>

<div class="admin-container">
    <div class="card">
        <div class="card-header">
            <h2>Liste des utilisateurs</h2>
            <a href="/admin/users/create" class="btn-add">Ajouter</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($users) && !empty($users)):
                    foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $user['role'] === 'admin' ? 'danger' : 'info'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['created_at']))); ?></td>
                            <td class="table-actions">
                                <a href="/admin/users/edit/<?php echo $user['id']; ?>" class="edit-link">Modifier</a>
                                <?php if (!isset($_SESSION['user_id']) || $user['id'] != $_SESSION['user_id']): // Prevent self-delete button ?>
                                <form action="/admin/users/delete/<?php echo $user['id']; ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');">
                                    <button type="submit">Supprimer</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                <?php 
                    endforeach;
                else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun utilisateur trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 