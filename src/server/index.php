<?php
header('Content-Type: application/json');

require_once __DIR__ . '/utils/Logger.php';
Logger::init(__DIR__ . '/logs');

require_once __DIR__ . '/api/index.php';

?>
