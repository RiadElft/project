<?php require_once 'views/layout/header.php'; ?>

<div class="container mx-auto p-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-primary p-4">
            <h2 class="text-2xl font-bold text-white">Connectez-vous à Votre Compte</h2>
        </div>
        
        <div class="p-6">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="flash-message danger mb-4" role="alert">
                    <span class="block"><?php echo htmlspecialchars($_SESSION['error']); ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="flash-message success mb-4" role="alert">
                    <span class="block"><?php echo htmlspecialchars($_SESSION['success']); ?></span>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <form action="/login" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 form-input">
                        <label for="remember" class="ml-2 text-sm text-gray-700">Se souvenir de moi</label>
                    </div>
                    
                    <a href="#" class="text-sm text-primary hover:text-primary-dark">Mot de passe oublié ?</a>
                </div>
                
                <div>
                    <button type="submit" class="btn btn-primary w-full">Connexion</button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Vous n'avez pas de compte ? 
                    <a href="/register" class="text-primary hover:text-primary-dark">Inscrivez-vous maintenant</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>