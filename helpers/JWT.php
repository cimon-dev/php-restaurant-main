<?php
/**
 * JWT Helper Class
 * Xử lý tạo và verify JWT tokens
 */

class JWT {
    
    /**
     * Tạo JWT token
     */
    public static function encode($payload) {
        $header = [
            'typ' => 'JWT',
            'alg' => JWT_ALGORITHM
        ];
        
        // Add issued at and expiration
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRATION;
        $payload['iss'] = JWT_ISSUER;
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET_KEY, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    /**
     * Verify và decode JWT token
     */
    public static function decode($token) {
        try {
            $parts = explode('.', $token);
            
            if (count($parts) !== 3) {
                return false;
            }
            
            list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
            
            // Verify signature
            $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, JWT_SECRET_KEY, true);
            $signatureCheck = self::base64UrlEncode($signature);
            
            if ($signatureEncoded !== $signatureCheck) {
                return false;
            }
            
            $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
            
            // Check expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false; // Token expired
            }
            
            return $payload;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    /**
     * Get token from request header
     */
    public static function getTokenFromHeader() {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
    
    /**
     * Get current user from token
     */
    public static function getCurrentUser() {
        $token = self::getTokenFromHeader();
        
        if (!$token) {
            // Try from cookie
            if (isset($_COOKIE['jwt_token'])) {
                $token = $_COOKIE['jwt_token'];
            }
        }
        
        if ($token) {
            $payload = self::decode($token);
            if ($payload) {
                return $payload;
            }
        }
        
        return null;
    }
}
