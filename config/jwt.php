<?php
/**
 * File cấu hình JWT
 */

// JWT Secret Key - Nên thay đổi trong production
define('JWT_SECRET_KEY', 'your-secret-key-change-this-in-production-2025');

// JWT Algorithm
define('JWT_ALGORITHM', 'HS256');

// JWT Expiration time (in seconds)
define('JWT_EXPIRATION', 86400); // 24 hours

// JWT Issuer
define('JWT_ISSUER', 'restaurant-management-system');
