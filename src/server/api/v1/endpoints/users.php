<?php

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../../middleware/JwtMiddleware.php';

function getAll()
{
    JwtMiddleware::requireAuth();
    try {
        $users = User::getAll();

        Response::success(
            'Users retrieved successfully',
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

function getUserById($matches){
    JwtMiddleware::requireAuth();
    try {
        $id = $matches[1];

        $user = User::getById($id);

        if (!$user) {
            Response::error('User not found', [], 404);
            return;
        }

        Response::success(
            'User retrieved successfully',
            $user
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
    JwtMiddleware::requireAuth();
    try
    {
        $id = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['password'])) {
            Response::error('Missing required field: password', [], 400);
            return;
        }

        if (!User::getById($id)) {
            Response::error('User not found', [], 404);
            return;
        }

        User::updatePassword($id, $data['password']);

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
    JwtMiddleware::requireAuth();
    try
    {
        $id = $matches[1];

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

    // Валидация
    if (!$name || !$email || !$password) {
        Response::error('Имя, email и пароль обязательны', [], 400);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Response::error('Некорректный email', [], 400);
        return;
    }

    if (strlen($password) < 8) {
        Response::error('Пароль минимум 8 символов', [], 400);
        return;
    }

    // Проверяем что email не занят
    if (User::getByEmail($email)) {
        Response::error('Email уже занят', [], 409);
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

    Response::success('Регистрация успешна', ['token' => $token], 201);
}

function loginUser() {
    $data = json_decode(file_get_contents('php://input'), true);

    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        Response::error('Email и пароль обязательны', [], 400);
        return;
    }

    $user = User::login($email, $password);

    if (!$user) {
        Response::error('Неверный email или пароль', [], 401);
        return;
    }

    $token = JwtMiddleware::generateToken($user);

    Response::success('Авторизация успешна', ['token' => $token]);
}