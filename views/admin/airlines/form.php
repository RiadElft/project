<?php require_once 'views/admin/layout/header.php'; ?>



<?php displayFlashMessages(); // Will be defined later ?>

<div class="card">
    <form action="<?php echo htmlspecialchars($action); ?>" method="<?php echo htmlspecialchars($form_method); ?>" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Nom de la compagnie <span style="color:red;">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($airline['name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group mb-3">
            <label for="logo_file" class="form-label">Logo (.jpg, .png, .gif, .webp - max 2MB)</label>
            <input type="file" class="form-control" id="logo_file" name="logo_file" accept=".jpg,.jpeg,.png,.gif,.webp">
            <?php if (isset($airline['logo_image']) && !empty($airline['logo_image'])): ?>
                <div class="mt-2">
                    <small>Logo actuel:</small><br>
                    <img src="/public/images/airlines/<?php echo htmlspecialchars($airline['logo_image']); ?>" alt="Logo actuel" style="max-height: 50px; margin-top: 5px;">
                    <input type="hidden" name="current_logo_image" value="<?php echo htmlspecialchars($airline['logo_image']); ?>"> 
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($airline['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo isset($airline['id']) ? 'Mettre à jour' : 'Enregistrer'; ?></button>
            <a href="/admin/airlines" class="btn btn-secondary" style="margin-left: 10px;">Annuler</a>
        </div>
    </form>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 