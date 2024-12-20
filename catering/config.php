<?php

/* Load LANPage Config */

if (file_exists(stream_resolve_include_path('../config.php'))) {
	include_once('../config.php');
} else if (file_exists(stream_resolve_include_path('../config.sample.php'))) {
	include_once('../config.sample.php');
} else {

    /* load default settings */

    $lang = "german";
	$timezone="Europe/Berlin";
}

/* Load local Catering Language File */

if ($lang == "german") {
	if (file_exists(stream_resolve_include_path('../lang.de.php'))) {
		include_once('../lang.de.php');
	} else {

		/* load default settings */
		
        $nav['catering'] = "Catering";
	}
    require('lang.de.php');
} else {
	if (file_exists(stream_resolve_include_path('../lang.en.php'))) {
		include_once('../lang.en.php');
	} else {

		/* load default settings */
		
        $nav['catering'] = "Catering";
	}
    require('lang.en.php');
}

date_default_timezone_set($timezone); // Setzt die Zeitzone für PHP

if (isset($catering_admin_password)) $admin_password = $catering_admin_password;
else $admin_password = "lanfood"; // Standardpasswort für den Adminbereich wenn nicht inder LANPage Config definiert

/* local SQLIte3 Database */
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
                price DECIMAL(10,2) NOT NULL,
                status TEXT DEFAULT "new",
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