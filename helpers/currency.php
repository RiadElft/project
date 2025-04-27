<?php

/**
 * Currency conversion rates
 */
define('CURRENCY_RATES', [
    'EUR' => [
        'DZD' => 150,
        'USD' => 1.09
    ],
    'USD' => [
        'DZD' => 131,
        'EUR' => 0.92
    ],
    'DZD' => [
        'EUR' => 0.0067, // 1/150
        'USD' => 0.0076  // 1/131
    ]
]);

/**
 * Get the user's selected currency from session
 * @return string Currency code (DZD, EUR, USD)
 */
function getCurrentCurrency() {
    return $_SESSION['selected_currency'] ?? 'DZD';
}

/**
 * Set the user's selected currency
 * @param string $currency Currency code (DZD, EUR, USD)
 */
function setCurrentCurrency($currency) {
    if (in_array($currency, ['DZD', 'EUR', 'USD'])) {
        $_SESSION['selected_currency'] = $currency;
    }
}

/**
 * Convert amount from EUR to target currency
 * @param float $amount Amount in EUR
 * @param string $targetCurrency Target currency code
 * @return float Converted amount
 */
function convertFromEUR($amount, $targetCurrency = null) {
    if ($targetCurrency === null) {
        $targetCurrency = getCurrentCurrency();
    }
    
    if ($targetCurrency === 'EUR') {
        return $amount;
    }
    
    return $amount * CURRENCY_RATES['EUR'][$targetCurrency];
}

/**
 * Format price in current currency
 * @param float $amount Amount in EUR
 * @return string Formatted price with currency symbol
 */
function formatPriceWithCurrency($amount) {
    $currency = getCurrentCurrency();
    $convertedAmount = convertFromEUR($amount);
    
    switch ($currency) {
        case 'DZD':
            return number_format($convertedAmount, 2, ',', ' ') . ' DA';
        case 'USD':
            return '$' . number_format($convertedAmount, 2, '.', ',');
        case 'EUR':
        default:
            return '€' . number_format($convertedAmount, 2, ',', ' ');
    }
} 