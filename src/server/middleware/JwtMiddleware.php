<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
            Response::error('Токен не передан', [], 401);
            exit;
        }

        $token = substr($authHeader, 7);

        try {
            $secret  = $_ENV['JWT_SECRET'] ?? 'fallback_secret_change_me';
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            Response::error('Токен недействителен или истёк', [], 401);
            exit;
        }
    }

    public static function requireAdmin(): array
    {
        $user = self::requireAuth();
        
        if ($user['role'] != 1) {
            Response::error('Доступ запрещён', [], 403);
            exit;
        }
        
        return $user;
    }
}
?>