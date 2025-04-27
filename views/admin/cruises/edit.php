<?php require_once 'views/admin/layout/header.php'; ?>

<h1><?php echo htmlspecialchars($pageTitle); ?></h1>

<?php displayFlashMessages(); ?>

<div class="card">
    <form action="<?php echo htmlspecialchars($action); ?>" method="<?php echo htmlspecialchars($form_method); ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="form-group">
            <label for="name" class="form-label">Nom de la Croisière <span style="color:red;">*</span></label>
            <input type="text" id="name" name="name" value="<?php echo isset($cruise['name']) ? htmlspecialchars($cruise['name']) : ''; ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="departure_port" class="form-label">Port de Départ <span style="color:red;">*</span></label>
            <input type="text" id="departure_port" name="departure_port" value="<?php echo isset($cruise['departure_port']) ? htmlspecialchars($cruise['departure_port']) : ''; ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="4" class="form-control"><?php echo isset($cruise['description']) ? htmlspecialchars($cruise['description']) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="departure_date" class="form-label">Date de Départ</label>
            <input type="date" id="departure_date" name="departure_date" value="<?php echo isset($cruise['departure_date']) ? date('Y-m-d', strtotime($cruise['departure_date'])) : ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="return_date" class="form-label">Date de Retour</label>
            <input type="date" id="return_date" name="return_date" value="<?php echo isset($cruise['return_date']) ? date('Y-m-d', strtotime($cruise['return_date'])) : ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="price" class="form-label">Prix par Personne (DA) <span style="color:red;">*</span></label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo isset($cruise['price']) ? htmlspecialchars(number_format($cruise['price'] * CURRENCY_RATES['EUR']['DZD'], 2, '.', '')) : '0.00'; ?>" class="form-control" required>
            <input type="hidden" id="price_eur" name="price_eur" value="<?php echo isset($cruise['price']) ? htmlspecialchars($cruise['price']) : '0.00'; ?>">
            <small class="form-text text-muted">Le prix affiché est en dinars algériens (DA). Il sera converti en euros lors de l'enregistrement.</small>
        </div>
        <div class="form-group">
            <label for="capacity" class="form-label">Capacité</label>
            <input type="number" id="capacity" name="capacity" min="1" value="<?php echo isset($cruise['capacity']) ? htmlspecialchars($cruise['capacity']) : ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="destination_ports" class="form-label">Ports de Destination</label>
            <textarea id="destination_ports" name="destination_ports" rows="3" class="form-control"><?php echo isset($cruise['destination_ports']) ? htmlspecialchars($cruise['destination_ports']) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="amenities" class="form-label">Équipements</label>
            <textarea id="amenities" name="amenities" rows="3" class="form-control"><?php echo isset($cruise['amenities']) ? htmlspecialchars($cruise['amenities']) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="ship_details" class="form-label">Détails du Navire</label>
            <textarea id="ship_details" name="ship_details" rows="3" class="form-control"><?php echo isset($cruise['ship_details']) ? htmlspecialchars($cruise['ship_details']) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="image" class="form-label">Image de la Croisière</label>
            <?php if (!empty($cruise['image_url'])): ?>
                <div class="mb-2">
                    <img src="<?php echo $cruise['image_url']; ?>" alt="Image actuelle de la croisière" style="max-width: 200px; height: auto;">
                    <p class="text-muted mt-1">Image actuelle</p>
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" class="form-control-file" accept="image/*">
        </div>
        <div class="form-group">
            <label for="status" class="form-label">Statut</label>
            <select id="status" name="status" class="form-control">
                <option value="active" <?php echo (isset($cruise['status']) && $cruise['status'] === 'active') ? 'selected' : ''; ?>>Actif</option>
                <option value="inactive" <?php echo (isset($cruise['status']) && $cruise['status'] === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
            </select>
        </div>
        <div class="mt-4" style="text-align: right;">
            <a href="/admin/cruises" class="btn btn-secondary" style="margin-right: 10px;">Annuler</a>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </div>
    </form>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 