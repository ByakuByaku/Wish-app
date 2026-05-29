<?php

require_once __DIR__ . '/../../models/WishlistItem.php';
require_once __DIR__ . '/../../models/Wishlist.php';
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Access.php';

function getItems($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        Access::wishlistRead($wishlistId);

        $items = WishlistItem::getByWishlistId($wishlistId);

        Response::success(
            'Items retrieved successfully',
            $items
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to fetch items',
            $e->getMessage(),
            500
        );
    }
}

function addItem($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        Access::wishlistWrite($wishlistId);
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'])) {
            Response::error('Missing required field: name', [], 400);
            return;
        }

        $data['wishlist_id'] = $wishlistId;
        $itemId = WishlistItem::createItem($data);

        Response::success(
            'Item added successfully',
            ['id' => $itemId],
            201
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to add item',
            $e->getMessage(),
            500
        );
    }
}

function updateItem($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        $itemId = (int) $matches[2];
        Access::wishlistWrite($wishlistId);
        $data = json_decode(file_get_contents('php://input'), true);

        $item = WishlistItem::getById($itemId);

        if (!$item || $item['wishlist_id'] != $wishlistId) {
            Response::error('Item not found', [], 404);
            return;
        }

        WishlistItem::updateItem($itemId, $data);

        Response::success(
            'Item updated successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to update item',
            $e->getMessage(),
            500
        );
    }
}

function deleteItem($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        $itemId = (int) $matches[2];
        Access::wishlistWrite($wishlistId);

        $item = WishlistItem::getById($itemId);

        if (!$item || $item['wishlist_id'] != $wishlistId) {
            Response::error('Item not found', [], 404);
            return;
        }

        WishlistItem::delete($itemId);

        Response::success(
            'Item deleted successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to delete item',
            $e->getMessage(),
            500
        );
    }
}

function reserveItem($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        $itemId = (int) $matches[2];
        $auth = Access::wishlistRead($wishlistId);

        $item = WishlistItem::getById($itemId);

        if (!$item || $item['wishlist_id'] != $wishlistId) {
            Response::error('Item not found', [], 404);
            return;
        }

        if (!empty($item['reserved_by'])) {
            Response::error('Item is already reserved', [], 409);
            return;
        }

        WishlistItem::reserve($itemId, $auth['user_id']);

        Response::success(
            'Item reserved successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to reserve item',
            $e->getMessage(),
            500
        );
    }
}

function unreserveItem($matches)
{
    try {
        $wishlistId = (int) $matches[1];
        $itemId = (int) $matches[2];
        $auth = Access::wishlistRead($wishlistId);

        $item = WishlistItem::getById($itemId);

        if (!$item || $item['wishlist_id'] != $wishlistId) {
            Response::error('Item not found', [], 404);
            return;
        }

        $wishlist = Wishlist::getWishlist($wishlistId);
        $isReserver = (int) ($item['reserved_by'] ?? 0) === (int) $auth['user_id'];
        $isOwner = $wishlist && (int) $wishlist['user_id'] === (int) $auth['user_id'];

        if (!$isReserver && !$isOwner) {
            Response::error('Only the user who reserved this item can remove the reservation', [], 403);
            return;
        }

        WishlistItem::unreserve($itemId);

        Response::success(
            'Item unreserved successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to unreserve item',
            $e->getMessage(),
            500
        );
    }
}

function editItem($itemId, $data){
    try {
        if (!WishlistItem::getById($itemId)) {
            Response::error('Item not found', [], 404);
            return;
        }

        WishlistItem::updateItem($itemId, $data);

        Response::success(
            'Item updated successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to update item',
            $e->getMessage(),
            500
        );
    }
}
