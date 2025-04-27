<?php
$pageTitle = 'Page Not Found';
require_once 'views/layout/header.php';
?>

<div class="container mx-auto px-4 py-16">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-600 mb-8">Page Not Found</h2>
        <p class="text-gray-500 mb-8">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="/" class="btn btn-primary">Return to Homepage</a>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?> 