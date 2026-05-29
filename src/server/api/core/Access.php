<?php

require_once __DIR__ . '/../../middleware/JwtMiddleware.php';
require_once __DIR__ . '/../models/Wishlist.php';
require_once __DIR__ . '/../models/Friend.php';
require_once __DIR__ . '/Response.php';

class Access
{
    public static function user(): array
    {
        return JwtMiddleware::requireAuth();
    }

    public static function self(int $userId): array
    {
        $auth = self::user();

        if ((int) $auth['user_id'] !== $userId && (int) $auth['role'] !== 1) {
            Response::error('Access denied', [], 403);
            exit;
        }

        return $auth;
    }

    public static function selfOrFriend(int $userId): array
    {
        $auth = self::user();

        if ((int) $auth['user_id'] === $userId) {
            return $auth;
        }

        if (Friend::areFriends((int) $auth['user_id'], $userId)) {
            return $auth;
        }

        if ((int) $auth['role'] === 1) {
            return $auth;
        }

        Response::error('Access denied', [], 403);
        exit;
    }

    public static function admin(): array
    {
        return JwtMiddleware::requireAdmin();
    }

    public static function wishlistRead(int $wishlistId): array
    {
        $auth = self::user();
        $wishlist = Wishlist::getWishlist($wishlistId);

        if (!$wishlist) {
            Response::error('Wishlist not found', [], 404);
            exit;
        }

        $isOwner = (int) $wishlist['user_id'] === (int) $auth['user_id'];
        $isPublic = (int) ($wishlist['is_public'] ?? 0) === 1;
        $isFriend = Friend::areFriends((int) $auth['user_id'], (int) $wishlist['user_id']);

        if (!$isOwner && !$isPublic && !$isFriend && (int) $auth['role'] !== 1) {
            Response::error('Access denied', [], 403);
            exit;
        }

        return $auth;
    }

    public static function wishlistWrite(int $wishlistId): array
    {
        $auth = self::user();
        $wishlist = Wishlist::getWishlist($wishlistId);

        if (!$wishlist) {
            Response::error('Wishlist not found', [], 404);
            exit;
        }

        if ((int) $wishlist['user_id'] !== (int) $auth['user_id'] && (int) $auth['role'] !== 1) {
            Response::error('Access denied', [], 403);
            exit;
        }

        return $auth;
    }
}
