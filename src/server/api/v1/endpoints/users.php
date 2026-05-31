<?php

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../../utils/Logger.php';
require_once __DIR__ . '/../../../middleware/JwtMiddleware.php';
require_once __DIR__ . '/../../core/Access.php';

function sanitizeUser(array $user): array
{
    unset($user['password_hash']);
    return $user;
}

function getAll()
{
    Access::admin();
    try {
        $users = User::getAll();

        Response::success(
            'Users retrieved successfully',
            array_map('sanitizeUser', $users)
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to fetch users',
            $e->getMessage(),
            500
        );
    }
}

function getUserById($matches){
    try {
        $id = (int) $matches[1];
        Access::selfOrFriend($id);

        $user = User::getById($id);

        if (!$user) {
            Response::error('User not found', [], 404);
            return;
        }

        Response::success(
            'User retrieved successfully',
            sanitizeUser($user)
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to fetch user',
            $e->getMessage(),
            500
        );
    }
}

function createUser()
{
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'], $data['email'], $data['password'])) {
            Response::error('Missing required fields: name, email, password', [], 400);
            return;
        }

        if (User::getByEmail($data['email'])) {
            Response::error('Email already exists', [], 400);
            return;
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);

        User::register($data);

        Response::success(
            'User created successfully',
            null,
            201
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to create user',
            $e->getMessage(),
            500
        );
    }
}

function updateUser($matches){
    try
    {
        $id = (int) $matches[1];
        Access::self($id);
        $data = json_decode(file_get_contents('php://input'), true);

        $user = User::getById($id);
        if (!$user) {
            Response::error('User not found', [], 404);
            return;
        }

        $hasProfileFields = isset($data['name']) || isset($data['email']) || array_key_exists('age', $data);
        $hasPassword = isset($data['password']) && $data['password'] !== '';

        if (!$hasProfileFields && !$hasPassword) {
            Response::error('Nothing to update', [], 400);
            return;
        }

        if ($hasProfileFields) {
            $name = trim($data['name'] ?? $user['name']);
            $email = trim($data['email'] ?? $user['email']);
            $age = array_key_exists('age', $data) && $data['age'] !== '' ? $data['age'] : null;

            if (!$name || !$email) {
                Response::error('Name and email are required', [], 400);
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Response::error('Invalid email', [], 400);
                return;
            }

            $existingUser = User::getByEmail($email);
            if ($existingUser && (int) $existingUser['id'] !== $id) {
                Response::error('Email is already taken', [], 409);
                return;
            }

            User::updateProfile($id, [
                'name' => $name,
                'email' => $email,
                'age' => $age,
            ]);
        }

        if ($hasPassword) {
            if (strlen($data['password']) < 8) {
                Response::error('Password must be at least 8 characters', [], 400);
                return;
            }

            User::updatePassword($id, $data['password']);
        }

        Response::success(
            'User updated successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to update user',
            $e->getMessage(),
            500
        );
    }
}

function deleteUser($matches){
    try
    {
        $id = (int) $matches[1];
        Access::self($id);

        if (!User::getById($id)) {
            Response::error('User not found', [], 404);
            return;
        }

        User::delete($id);

        Response::success(
            'User deleted successfully',
            null
        );
    } catch (Exception $e) {
        Response::error(
            'Failed to delete user',
            $e->getMessage(),
            500
        );
    }
}


function registerUser() {
    $data = json_decode(file_get_contents('php://input'), true);
    $name     = trim($data['name'] ?? '');
    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $age      = $data['age'] ?? null;
    if (!$name || !$email || !$password) {
        Response::error('Name, email and password are required', [], 400);
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Response::error('Invalid email', [], 400);
        return;
    }
    if (strlen($password) < 8) {
        Response::error('Password must be at least 8 characters', [], 400);
        return;
    }
    if (User::getByEmail($email)) {
        Response::error('Email is already taken', [], 409);
        return;
    }
    User::register([
        'name'          => $name,
        'email'         => $email,
        'age'           => $age,
        'password_hash' => password_hash($password, PASSWORD_BCRYPT),
    ]);
    $user  = User::getByEmail($email);
    $token = JwtMiddleware::generateToken($user);

    Response::success('Registration completed successfully', ['token' => $token], 201);
}
function loginUser() {
    $data = json_decode(file_get_contents('php://input'), true);
    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    if (!$email || !$password) {
        Response::error('Email and password are required', [], 400);
        return;
    }
    $user = User::login($email, $password);
    if (!$user) {
        Response::error('Invalid email or password', [], 401);
        return;
    }
    $token = JwtMiddleware::generateToken($user);
    Response::success('Login completed successfully', ['token' => $token]);
}

function logoutUser() {
    try {
        $user = JwtMiddleware::requireAuth();
        Logger::info('User logged out', [
            'user_id' => $user['user_id'],
            'action' => 'logout'
        ]);
        Response::success('Logout completed successfully', null);
    } catch (Exception $e) {
        Response::error('Logout failed', $e->getMessage(), 400);
    }
}
