<?php
/**
 * Generate JWT token
 * @param array $payload Data to encode in token
 * @return string JWT token
 */
function generateJWT($payload) {
    // Header
    $header = json_encode([
        'typ' => 'JWT',
        'alg' => 'HS256'
    ]);
    
    // Add issued at and expiration time to payload
    $payload['iat'] = time();
    $payload['exp'] = time() + JWT_EXPIRY;
    
    // Encode Header and Payload
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
    
    // Create Signature
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    // Create JWT
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    
    return $jwt;
}

/**
 * Validate JWT token
 * @param string $token JWT token to validate
 * @return mixed Payload if valid, false otherwise
 */
function validateJWT($token) {
    // Split the token
    $tokenParts = explode('.', $token);
    
    if (count($tokenParts) != 3) {
        return false;
    }
    
    $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[0]));
    $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
    $signatureProvided = $tokenParts[2];
    
    // Check the expiration time
    $decodedPayload = json_decode($payload, true);
    if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time()) {
        return false;
    }
    
    // Verify signature
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    if ($base64UrlSignature !== $signatureProvided) {
        return false;
    }
    
    return $decodedPayload;
}

/**
 * Get user data from JWT token
 * @param string $token JWT token
 * @return mixed User data if valid, false otherwise
 */
function getUserFromJWT($token) {
    $payload = validateJWT($token);
    if (!$payload) {
        return false;
    }
    
    return isset($payload['user_id']) ? $payload['user_id'] : false;
}
?>