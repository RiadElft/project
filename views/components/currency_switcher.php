<?php
require_once 'helpers/currency.php';

$currentCurrency = getCurrentCurrency();
?>

<div class="currency-switcher">
    <form action="/switch-currency" method="POST" class="flex items-center">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
        <label for="currency" class="text-sm text-gray-600 mr-2">Currency:</label>
        <select name="currency" id="currency" class="form-input text-sm" onchange="this.form.submit()">
            <option value="DZD" <?php echo $currentCurrency === 'DZD' ? 'selected' : ''; ?>>DZD</option>
            <option value="EUR" <?php echo $currentCurrency === 'EUR' ? 'selected' : ''; ?>>EUR</option>
            <option value="USD" <?php echo $currentCurrency === 'USD' ? 'selected' : ''; ?>>USD</option>
        </select>
    </form>
</div>

<style>
.currency-switcher {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 50;
    background: white;
    padding: 0.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.currency-switcher select {
    padding: 0.25rem 1.5rem 0.25rem 0.5rem;
    border: 1px solid var(--gray-200);
    border-radius: 0.25rem;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.25rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
}
</style> 