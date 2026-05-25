<?php

class Database
{
    private static $connection;

    public static function connect()
    {
        if (self::$connection === null) {
            try {
                $dbPath = __DIR__ . '/../../database/database.sqlite';

                self::$connection = new PDO("sqlite:$dbPath");
                self::$connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$connection->exec('PRAGMA foreign_keys = ON;');
            } catch (PDOException $e) {
                die("Database error: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}