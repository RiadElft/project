<?php require_once 'views/layout/header.php'; ?>

<div class="container mx-auto p-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-primary p-4">
            <h2 class="text-2xl font-bold text-white">Créez Votre Compte</h2>
        </div>
        
        <div class="p-6">
            <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
                <div class="flash-message danger mb-4" role="alert">
                    <ul class="list-disc pl-5">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <form action="/register" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="mb-4">
                    <label for="name" class="form-label">Nom Complet</label>
                    <input type="text" id="name" name="name" class="form-input" value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                    <p class="text-xs text-gray-500 mt-1">Le mot de passe doit contenir au moins 8 caractères.</p>
                </div>
                
                <div class="mb-4">
                    <label for="password_confirm" class="form-label">Confirmer le Mot de passe</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" required>
                </div>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="terms" name="terms" class="h-4 w-4 form-input" required>
                    <label for="terms" class="ml-2 text-sm text-gray-700">
                        J'accepte les <a href="/terms" class="text-primary hover:text-primary-dark">Conditions d'Utilisation</a> et la <a href="/privacy" class="text-primary hover:text-primary-dark">Politique de Confidentialité</a>
                    </label>
                </div>
                
                <div>
                    <button type="submit" class="btn btn-primary w-full">S'inscrire</button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Vous avez déjà un compte ? 
                    <a href="/login" class="text-primary hover:text-primary-dark">Connectez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
<?php unset($_SESSION['form_data']); ?>