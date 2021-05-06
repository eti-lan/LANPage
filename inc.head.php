<?php session_start();

if (file_exists(stream_resolve_include_path('config.php'))) {
    include('config.php');
} else if (file_exists(stream_resolve_include_path('config.sample.php'))) {
    include('config.sample.php');
} else {
    die;
}

if($show_errors == true){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
?>

<?php
if ($lang == "german") {
    require('lang.de.php');
} else {
    require('lang.en.php');
}
?>

<!DOCTYPE html>
<?php
if ($lang == "german") {
    echo '<html lang="de">';
} else {
    echo '<html lang="en">';
}
?>

<?php require "db/db.php"; ?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
    <meta name="description" content="end of the internet">
    <meta name="author" content="eti">
    <title><?php echo $lan_title; ?></title>
    <script src="assets/jquery.js"></script>
    <script src="assets/jquery.json.js"></script>
    <script src="assets/jquery.bracket.js"></script>
    <script src="assets/jquery.datatables.js"></script>
    <script src="assets/bootstrap.js"></script>
    <script src="assets/bootstrap.datatables.js"></script>
	<link href="assets/bootstrap.css" rel="stylesheet">
	<link href="assets/main.css" rel="stylesheet">
    <link href="assets/custom.css" rel="stylesheet">
	
    <?php if ($enable_stats == true) {
		echo ('<script src="assets/stats.js"></script>');
    }; ?>
    <?php if ($enable_chat == true) {
        echo ('<script src="assets/chat.js"></script>');
    }; ?>
</head>