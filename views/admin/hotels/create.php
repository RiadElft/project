<?php require_once 'views/admin/layout/header.php'; ?>

<div class="admin-layout">
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <nav>
            <ul>
                <li><a href="/admin/dashboard" class="<?php echo isCurrentPage('/admin/dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="/admin/flights" class="<?php echo isCurrentPage('/admin/flights') ? 'active' : ''; ?>">Vols</a></li>
                <li><a href="/admin/hotels" class="<?php echo isCurrentPage('/admin/hotels') ? 'active' : ''; ?>">Hôtels</a></li>
                <li><a href="/admin/bookings" class="<?php echo isCurrentPage('/admin/bookings') ? 'active' : ''; ?>">Réservations</a></li>
                <li><a href="/admin/users" class="<?php echo isCurrentPage('/admin/users') ? 'active' : ''; ?>">Utilisateurs</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="admin-main">
        <?php displayFlashMessages(); ?>

        <div class="admin-content">
            <div class="content-header">
                <h2>Ajouter un hôtel</h2>
                <a href="/admin/hotels" class="btn-back">Retour à la liste</a>
            </div>

            <div class="content-body">
                <form action="/admin/hotels/store" method="POST" enctype="multipart/form-data" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label for="name">Nom de l'hôtel</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="location">Emplacement</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="price">Prix par nuit (€)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>

                        <div class="form-group half">
                            <label for="rating">Note</label>
                            <select class="form-control" id="rating" name="rating" required>
                                <option value="">Sélectionner une note</option>
                                <option value="1">1 étoile</option>
                                <option value="2">2 étoiles</option>
                                <option value="3">3 étoiles</option>
                                <option value="4">4 étoiles</option>
                                <option value="5">5 étoiles</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="available_rooms">Nombre de chambres disponibles</label>
                            <input type="number" class="form-control" id="available_rooms" name="available_rooms" min="0" required>
                        </div>

                        <div class="form-group half">
                            <label for="status">Statut</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="amenities">Équipements</label>
                            <textarea class="form-control" id="amenities" name="amenities" rows="3" placeholder="Entrez les équipements, un par ligne"></textarea>
                            <small class="form-help">Entrez chaque équipement sur une nouvelle ligne</small>
                        </div>

                        <div class="form-group half">
                            <label for="room_types">Types de chambres</label>
                            <textarea class="form-control" id="room_types" name="room_types" rows="3" placeholder="Entrez les types de chambres, un par ligne"></textarea>
                            <small class="form-help">Entrez chaque type de chambre sur une nouvelle ligne</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image_file">Image de l'hôtel</label>
                        <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*">
                        <small class="form-help">Taille recommandée : 800x600 pixels</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Créer l'hôtel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.admin-form {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group.half {
    flex: 1;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

textarea.form-control {
    resize: vertical;
}

.form-help {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.form-actions {
    margin-top: 30px;
    text-align: right;
}

.btn-back {
    padding: 8px 16px;
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.btn-back:hover {
    background-color: #5a6268;
}

.btn-submit {
    padding: 10px 20px;
    background-color: #2ecc71;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-submit:hover {
    background-color: #27ae60;
}
</style>

<?php require_once 'views/admin/layout/footer.php'; ?> 