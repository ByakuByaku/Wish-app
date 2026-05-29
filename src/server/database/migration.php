<?php
require_once __DIR__ . '/../api/core/Database.php';
$db = Database::connect();
$db->exec("
    CREATE TABLE IF NOT EXISTS friend_invites (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT UNIQUE NOT NULL,
        user_id INTEGER NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )
");
echo "Migration done\n";