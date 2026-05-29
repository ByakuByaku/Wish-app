<?php

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Wishlist.php';
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Access.php';

function getLogs()
{
    Access::admin();
    try {
        $logsPath = __DIR__ . '/../../../logs/';
        $logs = [];

        if (is_dir($logsPath)) {
            $files = scandir($logsPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($logsPath . $file)) {
                    $logs[] = [
                        'file' => $file,
                        'path' => $logsPath . $file
                    ];
                }
            }
        }

        Response::success(
            'Logs retrieved successfully',
            $logs
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to fetch logs',
            $e->getMessage(),
            500
        );
    }
}

function getAllUsersAdmin()
{
    Access::admin();
    try {
        $users = User::getAll();
        foreach ($users as &$user) {
            unset($user['password_hash']);
        }
        unset($user);

        Response::success(
            'All users retrieved successfully',
            $users
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to fetch users',
            $e->getMessage(),
            500
        );
    }
}

function updateUserRole($matches)
{
    Access::admin();
    try {
        $userId = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['role'])) {
            Response::error('Missing required field: role', [], 400);
            return;
        }

        if (!User::getById($userId)) {
            Response::error('User not found', [], 404);
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$data['role'], $userId]);

        Response::success(
            'User role updated successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to update user role',
            $e->getMessage(),
            500
        );
    }
}

function deleteWishlistAdmin($matches)
{
    Access::admin();
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