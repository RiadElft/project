    </div> <!-- End of container -->
    
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title">À Propos de <?php echo APP_NAME; ?></h3>
                    <p class="footer-text">Votre partenaire de confiance pour tous vos besoins de voyage. Réservez vols, hôtels et croisières en toute confiance.</p>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Liens Rapides</h3>
                    <ul class="footer-list">
                        <li><a href="/flights" class="footer-link">Vols</a></li>
                        <li><a href="/hotels" class="footer-link">Hôtels</a></li>
                        <li><a href="/cruises" class="footer-link">Croisières</a></li>
                        <li><a href="/contact" class="footer-link">Nous Contacter</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Support</h3>
                    <ul class="footer-list">
                        <li><a href="/faq" class="footer-link">FAQ</a></li>
                        <li><a href="/terms" class="footer-link">Conditions Générales</a></li>
                        <li><a href="/privacy" class="footer-link">Politique de Confidentialité</a></li>
                        <li><a href="/help" class="footer-link">Centre d'Aide</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Contact</h3>
                    <ul class="footer-contact">
                        <li>Email: support@<?php echo strtolower(str_replace(' ', '', APP_NAME)); ?>.com</li>
                        <li>Téléphone: 1-800-VOYAGE</li>
                        <li>Adresse: 123 Rue du Voyage, Ville, Pays</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Basic mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                const isOpen = mobileMenu.classList.contains('hidden');
                mobileMenu.classList.toggle('hidden', !isOpen);
                mobileMenuButton.setAttribute('aria-expanded', isOpen);
                // Toggle icons
                mobileMenuButton.querySelectorAll('svg').forEach(icon => icon.classList.toggle('hidden'));
            });
        }

        // Basic user dropdown toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', (event) => {
                const isOpen = !userMenu.classList.contains('opacity-0');
                userMenu.classList.toggle('opacity-0', isOpen);
                userMenu.classList.toggle('scale-95', isOpen);
                userMenu.classList.toggle('opacity-100', !isOpen);
                userMenu.classList.toggle('scale-100', !isOpen);
                userMenuButton.setAttribute('aria-expanded', !isOpen);
                event.stopPropagation(); // Prevent click from closing menu immediately
            });
            // Close dropdown if clicked outside
            document.addEventListener('click', (event) => {
                if (!userMenu.contains(event.target) && !userMenuButton.contains(event.target)) {
                    userMenu.classList.add('opacity-0', 'scale-95');
                    userMenu.classList.remove('opacity-100', 'scale-100');
                    userMenuButton.setAttribute('aria-expanded', 'false');
                }
            });
        }
    </script>
    <script src="/public/js/main.js"></script>
</body>
</html>