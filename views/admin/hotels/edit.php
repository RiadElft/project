<?php
// Set up variables for form.php
$pageTitle = 'Modifier l\'hôtel';
$action = '/admin/hotels/update.php?id=' . urlencode($hotel['id']);
$form_method = 'post';
require __DIR__ . '/form.php'; 