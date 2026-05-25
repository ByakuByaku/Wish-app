<?php

require_once __DIR__ . '/../../models/WishlistItem.php';
require_once __DIR__ . '/../../models/Wishlist.php';
require_once __DIR__ . '/../../core/Response.php';

function getItems($matches)
{
    try {
        $wishlistId = $matches[1];

        if (!Wishlist::getWishlist($wishlistId)) {
            Response::error('Wishlist not found', [], 404);
            return;
        }

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
        $wishlistId = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'])) {
            Response::error('Missing required field: name', [], 400);
            return;
        }

        if (!Wishlist::getWishlist($wishlistId)) {
            Response::error('Wishlist not found', [], 404);
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

function deleteItem($matches)
{
    try {
        $wishlistId = $matches[1];
        $itemId = $matches[2];

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
        $wishlistId = $matches[1];
        $itemId = $matches[2];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'])) {
            Response::error('Missing required field: user_id', [], 400);
            return;
        }

        $item = WishlistItem::getById($itemId);

        if (!$item || $item['wishlist_id'] != $wishlistId) {
            Response::error('Item not found', [], 404);
            return;
        }

        WishlistItem::reserve($itemId, $data['user_id']);

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
        $wishlistId = $matches[1];
        $itemId = $matches[2];

        $item = WishlistItem::getById($itemId);

        if (!$item || $item['wishlist_id'] != $wishlistId) {
            Response::error('Item not found', [], 404);
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