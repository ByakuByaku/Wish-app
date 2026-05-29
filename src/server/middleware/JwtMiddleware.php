<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once __DIR__ . '/../utils/Logger.php';
require_once __DIR__ . '/../api/core/Response.php';

class JwtMiddleware {
    public static function generateToken(array $user) {
    $secret = $_ENV['JWT_SECRET'] ?? 'fallback_secret_change_me';
        $expiry = (int)($_ENV['JWT_EXPIRY'] ?? 3600);

        $payload = [
            'iss'     => 'wishlist-app',
            'iat'     => time(),
            'exp'     => time() + $expiry,
            'user_id' => $user['id'],
            'email'   => $user['email'],
            'role'    => $user['role'],
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }
    public static function requireAuth(): array
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (!str_starts_with($authHeader, 'Bearer ')) {
            Logger::warning('Token was not provided', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            Response::error('Token was not provided', [], 401);
            exit;
        }
        $token = substr($authHeader, 7);
        try {
            $secret  = $_ENV['JWT_SECRET'] ?? 'fallback_secret_change_me';
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            Logger::info('Token verified successfully', ['user_id' => $decoded->user_id]);
            return (array) $decoded;
        } catch (Exception $e) {
            Logger::error('Invalid or expired token', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            Response::error('Token is invalid or expired', [], 401);
            exit;
        }
    }
    public static function requireAdmin(): array
    {
        $user = self::requireAuth();
        if ($user['role'] != 1) {
            Logger::warning('Attempt to access admin resource without permissions', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_id' => $user['user_id']
            ]);
            Response::error('Access denied', [], 403);
            exit;
        }
        
        return $user;
    }
}
?>
