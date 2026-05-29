<?php

require_once __DIR__ . '/../../models/Friend.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../core/Access.php';

function getFriends($matches)
{
    try {
        $userId = (int) $matches[1];
        Access::self($userId);

        if (!User::getById($userId)) {
            Response::error('User not found', [], 404);
            return;
        }

        $friends = Friend::getFriends($userId);

        Response::success(
            'Friends retrieved successfully',
            $friends
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to fetch friends',
            $e->getMessage(),
            500
        );
    }
}

function addFriend($matches)
{
    try {
        $userId = (int) $matches[1];
        Access::self($userId);
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['friend_id'])) {
            Response::error('Missing required field: friend_id', [], 400);
            return;
        }

        if (!User::getById($userId)) {
            Response::error('User not found', [], 404);
            return;
        }

        if (!User::getById($data['friend_id'])) {
            Response::error('Friend not found', [], 404);
            return;
        }

        Friend::addFriend($userId, $data['friend_id']);

        Response::success(
            'Friend added successfully',
            null,
            201
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to add friend',
            $e->getMessage(),
            500
        );
    }
}

function removeFriend($matches)
{
    try {
        $userId = (int) $matches[1];
        $friendId = (int) $matches[2];
        Access::self($userId);

        if (!User::getById($userId)) {
            Response::error('User not found', [], 404);
            return;
        }

        Friend::removeFriend($userId, $friendId);

        Response::success(
            'Friend removed successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to remove friend',
            $e->getMessage(),
            500
        );
    }
}

function createInvite($matches)
{
    try {
        $userId = (int) $matches[1];
        Access::self($userId);

        $code = Friend::createInvite($userId);

        Response::success('Invite created', ['code' => $code], 201);
    } catch (Exception $e) {
        Response::error('Failed to create invite', $e->getMessage(), 500);
    }
}

function useInvite($matches)
{
    try {
        $userId = (int) $matches[1];
        Access::self($userId);

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['code'])) {
            Response::error('Missing required field: code', [], 400);
            return;
        }

        $result = Friend::useInvite($data['code'], $userId);

        if (!$result) {
            Response::error('Invalid code or you are the owner', [], 400);
            return;
        }

        Response::success('Friend added successfully', null);
    } catch (Exception $e) {
        Response::error('Failed to use invite', $e->getMessage(), 500);
    }
}
