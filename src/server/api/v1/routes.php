<?php

require_once __DIR__ . '/endpoints/users.php';
require_once __DIR__ . '/endpoints/wishlists.php';
require_once __DIR__ . '/endpoints/items.php';
require_once __DIR__ . '/endpoints/friends.php';
require_once __DIR__ . '/endpoints/admin.php';

$router->get('/users', 'getAll');
$router->get('/users/(\d+)', 'getUserById');
$router->post('/auth/register', 'registerUser');
$router->post('/auth/login', 'loginUser');
$router->put('/users/(\d+)', 'updateUser');
$router->delete('/users/(\d+)', 'deleteUser');

$router->get('/wishlists/(\d+)', 'getWishlist');
$router->get('/users/(\d+)/wishlists', 'getUserWishlists');
$router->post('/wishlists', 'createWishlist');
$router->put('/wishlists/(\d+)', 'updateWishlist');
$router->delete('/wishlists/(\d+)', 'deleteWishlist');

$router->get('/wishlists/(\d+)/items', 'getItems');
$router->post('/wishlists/(\d+)/items', 'addItem');
$router->put('/wishlists/(\d+)/items/(\d+)', 'updateItem');
$router->delete('/wishlists/(\d+)/items/(\d+)', 'deleteItem');
$router->post('/wishlists/(\d+)/items/(\d+)/reserve', 'reserveItem');
$router->delete('/wishlists/(\d+)/items/(\d+)/reserve', 'unreserveItem');
$router->put('/wishlists/(\d+)/items/(\d+)', 'updateItem');


$router->get('/users/(\d+)/friends', 'getFriends');
$router->post('/users/(\d+)/friends', 'addFriend');
$router->delete('/users/(\d+)/friends/(\d+)', 'removeFriend');


$router->get('/admin/logs', 'getLogs');
$router->get('/admin/users', 'getAllUsersAdmin');
$router->put('/admin/users/(\d+)/role', 'updateUserRole');
$router->delete('/admin/wishlists/(\d+)', 'deleteWishlistAdmin');

$router->dispatch();

?>
