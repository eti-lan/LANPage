<?php
// config.php

date_default_timezone_set('Europe/Berlin'); // Setzt die Zeitzone für PHP

class Database {
    private static $db = null;
    
    public static function getInstance() {
        if (self::$db === null) {
            self::$db = new SQLite3('orders.db');
            self::initDatabase();
        }
        return self::$db;
    }
    
    private static function initDatabase() {
        $db = self::$db;
        $db->exec('
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_name TEXT NOT NULL,
                flyer_number INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                size TEXT NOT NULL,
                notes TEXT,
                price REAL NOT NULL,
                status TEXT DEFAULT "neu",
                paid INTEGER DEFAULT 0,
                order_time DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
		$db->exec('
            CREATE TABLE IF NOT EXISTS flyers (
				id INTEGER PRIMARY KEY AUTOINCREMENT,
				name TEXT NOT NULL,
				filename TEXT NOT NULL,
				upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
				is_active INTEGER DEFAULT 0
			)
		');
    }
}
?>