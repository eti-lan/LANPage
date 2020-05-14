<?php
mb_internal_encoding("UTF-8");

if (file_exists(stream_resolve_include_path('config.php'))) {
	include('config.php');
}
else if (file_exists(stream_resolve_include_path('config.sample.php'))) {
	include('config.sample.php');
}

// Database Connections
if ($enable_stats == true) { 
$stats_db = new SQLite3('db/lan.db');
};
 
if ($enable_competition == true) { 
$competition_db = new SQLite3('db/competition.db');
};

?>