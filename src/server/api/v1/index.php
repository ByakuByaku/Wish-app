<?php

require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

$router = new Router();

require_once __DIR__ . '/routes.php';

?>