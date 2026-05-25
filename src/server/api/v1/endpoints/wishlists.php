<?php

require_once __DIR__ . '/../../models/Wishlist.php';
require_once __DIR__ . '/../../core/Response.php';

function getWishlist($matches)
{
    try {
        $wishlistId = $matches[1];

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
        $userId = $matches[1];

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
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['name'])) {
            Response::error('Missing required fields: user_id, name', [], 400);
            return;
        }

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
        $wishlistId = $matches[1];
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
        $wishlistId = $matches[1];

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
