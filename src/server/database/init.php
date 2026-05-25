<?php

require_once __DIR__ . '/../api/core/Database.php';

try {
    $db = Database::connect();
    
    $sql = file_get_contents(__DIR__ . '/init.sql');
    $db->exec($sql);
    
    echo "Database initialized successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
