<?php require_once 'views/admin/layout/header.php'; ?>

<h1><?php echo htmlspecialchars($pageTitle); ?></h1>

<?php displayFlashMessages(); ?>

<div class="card">
    <form action="<?php echo htmlspecialchars($action); ?>" method="<?php echo htmlspecialchars($form_method); ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <input type="hidden" name="location" value="<?php echo isset($hotel['city']) ? htmlspecialchars($hotel['city']) : ''; ?>">
        <div class="form-group">
            <label for="name" class="form-label">Nom de l'Hôtel <span style="color:red;">*</span></label>
            <input type="text" id="name" name="name" value="<?php echo isset($hotel['name']) ? htmlspecialchars($hotel['name']) : ''; ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="city" class="form-label">Ville <span style="color:red;">*</span></label>
            <input type="text" id="city" name="city" value="<?php echo isset($hotel['city']) ? htmlspecialchars($hotel['city']) : ''; ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="country" class="form-label">Pays <span style="color:red;">*</span></label>
            <input type="text" id="country" name="country" value="<?php echo isset($hotel['country']) ? htmlspecialchars($hotel['country']) : ''; ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="4" class="form-control"><?php echo isset($hotel['description']) ? htmlspecialchars($hotel['description']) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="price_per_night" class="form-label">Prix par nuit (€) <span style="color:red;">*</span></label>
            <input type="number" step="0.01" id="price_per_night" name="price_per_night" value="<?php echo isset($hotel['price_per_night']) ? htmlspecialchars($hotel['price_per_night']) : '0.00'; ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="rating" class="form-label">Note</label>
            <select id="rating" name="rating" class="form-control">
                <option value="">Sélectionner une note</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo (isset($hotel['rating']) && $hotel['rating'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?> étoile<?php echo $i > 1 ? 's' : ''; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="available_rooms" class="form-label">Nombre de chambres disponibles</label>
            <input type="number" id="available_rooms" name="available_rooms" min="0" value="<?php echo isset($hotel['available_rooms']) ? htmlspecialchars($hotel['available_rooms']) : '0'; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="status" class="form-label">Statut</label>
            <select id="status" name="status" class="form-control">
                <option value="active" <?php echo (!isset($hotel['status']) || $hotel['status'] === 'active') ? 'selected' : ''; ?>>Actif</option>
                <option value="inactive" <?php echo (isset($hotel['status']) && $hotel['status'] === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
            </select>
        </div>
        <div class="form-group">
            <label for="amenities" class="form-label">Équipements</label>
            <textarea id="amenities" name="amenities" rows="3" class="form-control"><?php echo isset($hotel['amenities']) ? htmlspecialchars($hotel['amenities']) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="room_types" class="form-label">Types de chambres</label>
            <textarea id="room_types" name="room_types" rows="3" class="form-control"><?php echo isset($hotel['room_types']) ? htmlspecialchars($hotel['room_types']) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="image_file" class="form-label">Image de l'Hôtel</label>
            <?php if (isset($hotel['image']) && !empty($hotel['image'])): ?>
                <div class="mb-2">
                    <img src="/public/images/hotels/<?php echo htmlspecialchars($hotel['image']); ?>" alt="Image actuelle de l'hôtel" style="max-width: 200px; height: auto;">
                    <p class="text-muted mt-1">Image actuelle</p>
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($hotel['image']); ?>">
                </div>
            <?php endif; ?>
            <input type="file" id="image_file" name="image_file" class="form-control-file" accept="image/*">
        </div>
        <div class="mt-4" style="text-align: right;">
            <a href="/admin/hotels" class="btn btn-secondary" style="margin-right: 10px;">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <?php echo isset($hotel['id']) ? 'Mettre à jour' : 'Créer l\'hôtel'; ?>
            </button>
        </div>
    </form>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 