<?php require_once 'views/admin/layout/header.php'; ?>

<h1><?php echo htmlspecialchars($pageTitle); ?></h1>

<?php displayFlashMessages(); ?>

<div class="card">
    <form action="<?php echo htmlspecialchars($action); ?>" method="<?php echo htmlspecialchars($form_method); ?>">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); // Assumes function exists ?>">
        
        <!-- Grid simulation using form groups (or update CSS for grid if needed) -->
        <div class="form-group">
            <label for="airline_id" class="form-label">Compagnie Aérienne <span style="color:red;">*</span></label>
            <select id="airline_id" name="airline_id" class="form-control" required>
                <option value="">-- Sélectionnez une compagnie --</option>
                <?php if (isset($airlines) && !empty($airlines)): ?>
                    <?php foreach ($airlines as $airline): ?>
                        <option value="<?php echo $airline['id']; ?>" <?php echo (isset($flight['airline_id']) && $flight['airline_id'] == $airline['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($airline['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="flight_number" class="form-label">Numéro de Vol <span style="color:red;">*</span></label>
            <input type="text" id="flight_number" name="flight_number" value="<?php echo isset($flight['flight_number']) ? htmlspecialchars($flight['flight_number']) : ''; ?>" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="departure_city" class="form-label">Ville de Départ <span style="color:red;">*</span></label>
            <input type="text" id="departure_city" name="departure_city" value="<?php echo isset($flight['departure_city']) ? htmlspecialchars($flight['departure_city']) : ''; ?>" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="arrival_city" class="form-label">Ville d'Arrivée <span style="color:red;">*</span></label>
            <input type="text" id="arrival_city" name="arrival_city" value="<?php echo isset($flight['arrival_city']) ? htmlspecialchars($flight['arrival_city']) : ''; ?>" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="departure_time" class="form-label">Date/Heure de Départ</label>
            <input type="datetime-local" id="departure_time" name="departure_time" value="<?php echo isset($flight['departure_time']) ? date('Y-m-d\TH:i', strtotime($flight['departure_time'])) : ''; ?>" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="arrival_time" class="form-label">Date/Heure d'Arrivée</label>
            <input type="datetime-local" id="arrival_time" name="arrival_time" value="<?php echo isset($flight['arrival_time']) ? date('Y-m-d\TH:i', strtotime($flight['arrival_time'])) : ''; ?>" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="price" class="form-label">Prix (€)</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo isset($flight['price']) ? htmlspecialchars($flight['price']) : '0.00'; ?>" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="stops" class="form-label">Escales</label>
            <input type="number" id="stops" name="stops" value="<?php echo isset($flight['stops']) ? htmlspecialchars($flight['stops']) : '0'; ?>" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="available_seats" class="form-label">Sièges Disponibles</label>
            <input type="number" id="available_seats" name="available_seats" value="<?php echo isset($flight['available_seats']) ? htmlspecialchars($flight['available_seats']) : '0'; ?>" class="form-control">
        </div>
        
        <div class="mt-4" style="text-align: right;"> <!-- Aligns buttons to the right -->
            <a href="/admin/flights" class="btn btn-secondary" style="margin-right: 10px;">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <?php echo isset($flight['id']) ? 'Mettre à jour' : 'Enregistrer'; ?>
            </button>
        </div>
    </form>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?> 