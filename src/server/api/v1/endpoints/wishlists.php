<?php

require_once __DIR__ . '/../../models/Wishlist.php';
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../../utils/Logger.php';
require_once __DIR__ . '/../../../middleware/JwtMiddleware.php';
require_once __DIR__ . '/../../core/Access.php';

function getWishlist($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        Access::wishlistRead($wishlistId);

        $wishlist = Wishlist::getWishlist($wishlistId);

        if (!$wishlist) {
            Response::error('Wishlist not found', [], 404);
            return;
        }

        Response::success(
            'Wishlist retrieved successfully',
            $wishlist
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to fetch wishlist',
            $e->getMessage(),
            500
        );
    }
}

function getUserWishlists($matches)
{
    try {
        $userId = (int) $matches[1];
        Access::selfOrFriend($userId);

        $wishlists = Wishlist::getByUserID($userId);

        Response::success(
            'User wishlists retrieved successfully',
            $wishlists
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to fetch user wishlists',
            $e->getMessage(),
            500
        );
    }
}

function createWishlist()
{
    try {
        $auth = Access::user();
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'])) {
            Response::error('Missing required field: name', [], 400);
            return;
        }

        $data['user_id'] = $auth['user_id'];

        $wishlistId = Wishlist::createWishlist($data);

        Response::success(
            'Wishlist created successfully',
            ['id' => $wishlistId],
            201
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to create wishlist',
            $e->getMessage(),
            500
        );
    }
}

function updateWishlist($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        Access::wishlistWrite($wishlistId);
        $data = json_decode(file_get_contents('php://input'), true);

        if (!Wishlist::getWishlist($wishlistId)) {
            Response::error('Wishlist not found', [], 404);
            return;
        }

        Wishlist::updateWishlist($wishlistId, $data);

        Response::success(
            'Wishlist updated successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to update wishlist',
            $e->getMessage(),
            500
        );
    }
}

function deleteWishlist($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        Access::wishlistWrite($wishlistId);

        if (!Wishlist::getWishlist($wishlistId)) {
            Response::error('Wishlist not found', [], 404);
            return;
        }

        Wishlist::deleteWishlist($wishlistId);

        Response::success(
            'Wishlist deleted successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to delete wishlist',
            $e->getMessage(),
            500
        );
    }
}
