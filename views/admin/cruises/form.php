<?php require_once 'views/admin/layout/header.php'; ?>



<?php displayFlashMessages(); ?>

<div class="card">
    <form action="<?php echo htmlspecialchars($action); ?>" method="<?php echo htmlspecialchars($form_method); ?>" enctype="multipart/form-data">
        
        <div class="form-group mb-3">
            <label for="name" class="form-label">Nom de la Croisière <span style="color:red;">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($cruise['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="cruise_line" class="form-label">Compagnie</label>
            <input type="text" class="form-control" id="cruise_line" name="cruise_line" value="<?php echo htmlspecialchars($cruise['cruise_line'] ?? ''); ?>">
        </div>

         <div class="form-group mb-3">
            <label for="ship_name" class="form-label">Nom du Navire</label>
            <input type="text" class="form-control" id="ship_name" name="ship_name" value="<?php echo htmlspecialchars($cruise['ship_name'] ?? ''); ?>">
        </div>

        <div class="form-group mb-3">
            <label for="departure_port" class="form-label">Port de Départ <span style="color:red;">*</span></label>
            <input type="text" class="form-control" id="departure_port" name="departure_port" value="<?php echo htmlspecialchars($cruise['departure_port'] ?? ''); ?>" required>
        </div>
        
         <div class="form-group mb-3">
            <label for="destination_ports" class="form-label">Ports de Destination (séparés par virgule)</label>
            <input type="text" class="form-control" id="destination_ports" name="destination_ports" value="<?php echo isset($cruise['destination_ports']) ? htmlspecialchars(is_array($cruise['destination_ports']) ? implode(',', $cruise['destination_ports']) : $cruise['destination_ports']) : ''; ?>">
        </div>

        <div class="form-group mb-3">
            <label for="duration_days" class="form-label">Durée (jours) <span style="color:red;">*</span></label>
            <input type="number" min="1" class="form-control" id="duration_days" name="duration_days" value="<?php echo htmlspecialchars($cruise['duration_days'] ?? '1'); ?>" required>
        </div>
        
        <div class="form-group mb-3">
            <label for="departure_date" class="form-label">Date de Départ</label>
            <input type="date" class="form-control" id="departure_date" name="departure_date" value="<?php echo isset($cruise['departure_date']) ? date('Y-m-d', strtotime($cruise['departure_date'])) : ''; ?>">
        </div>

        <div class="form-group mb-3">
            <label for="price" class="form-label">Prix (€) <span style="color:red;">*</span></label>
            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($cruise['price'] ?? '0.00'); ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="available_cabins" class="form-label">Cabines Disponibles</label>
            <input type="number" min="0" class="form-control" id="available_cabins" name="available_cabins" value="<?php echo htmlspecialchars($cruise['available_cabins'] ?? '0'); ?>">
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($cruise['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="image_file" class="form-label">Image (.jpg, .png, .gif, .webp - max 2MB)</label>
            <input type="file" class="form-control" id="image_file" name="image_file" accept=".jpg,.jpeg,.png,.gif,.webp">
            <?php if (isset($cruise['image']) && !empty($cruise['image'])): ?>
                <div class="mt-2">
                    <small>Image actuelle:</small><br>
                    <img src="/public/images/cruises/<?php echo htmlspecialchars($cruise['image']); ?>" alt="Image actuelle" style="max-height: 100px; margin-top: 5px;">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($cruise['image']); ?>"> 
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group mb-3">
            <label for="status" class="form-label">Statut</label>
            <select class="form-control" id="status" name="status">
                <option value="active" <?php echo (isset($cruise['status']) && $cruise['status'] === 'active') ? 'selected' : ''; ?>>Actif</option>
                <option value="inactive" <?php echo (isset($cruise['status']) && $cruise['status'] === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo isset($cruise['id']) ? 'Mettre à jour' : 'Enregistrer'; ?></button>
            <a href="/admin/cruises" class="btn btn-secondary" style="margin-left: 10px;">Annuler</a>
        </div>
    </form>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 