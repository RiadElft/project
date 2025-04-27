<?php

require_once 'helpers/currency.php';

class CurrencyController {
    public function switchCurrency() {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Invalid request.'
            ];
            header('Location: /');
            exit;
        }

        // Get the new currency
        $currency = $_POST['currency'] ?? 'DZD';
        
        // Set the new currency
        setCurrentCurrency($currency);
        
        // Redirect back to the previous page
        $redirectUrl = $_POST['redirect_url'] ?? '/';
        header('Location: ' . $redirectUrl);
        exit;
    }
} 