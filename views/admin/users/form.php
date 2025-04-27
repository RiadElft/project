<?php require_once 'views/admin/layout/header.php'; ?>



<?php displayFlashMessages(); ?>

<div class="card">
    <form action="<?php echo htmlspecialchars($action); ?>" method="<?php echo htmlspecialchars($form_method); ?>">
        
        <div class="form-group mb-3">
            <label for="name" class="form-label">Nom <span style="color:red;">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="email" class="form-label">Email <span style="color:red;">*</span></label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group mb-3">
            <label for="role" class="form-label">Rôle <span style="color:red;">*</span></label>
            <select class="form-control" id="role" name="role" required>
                <option value="client" <?php echo (isset($user['role']) && $user['role'] === 'client') ? 'selected' : ''; ?>>Client</option>
                <option value="admin" <?php echo (isset($user['role']) && $user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <div class="alert alert-warning">
            Remarque: La modification du mot de passe doit être effectuée via une fonctionnalité dédiée (non implémentée ici).
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="/admin/users" class="btn btn-secondary" style="margin-left: 10px;">Annuler</a>
        </div>
    </form>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 