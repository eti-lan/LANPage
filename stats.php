<?php
	header('Content-type: text/plain; charset=utf-8');
	include "db/db.php";
	
	$hostname = mb_convert_encoding($_GET["hostname"], 'UTF-8', 'ISO-8859-15');
	$macaddr1 = mb_convert_encoding($_GET["macaddr1"], 'UTF-8', 'ISO-8859-15');
	$macaddr2 = mb_convert_encoding($_GET["macaddr2"], 'UTF-8', 'ISO-8859-15');
	//$ipv4addr = $_GET["ipv4addr"];
	$ipv4addr = mb_convert_encoding($_SERVER['REMOTE_ADDR'], 'UTF-8', 'ISO-8859-15');
	$board_manufacturer = mb_convert_encoding($_GET["board_manufacturer"], 'UTF-8', 'ISO-8859-15');
	$baseboard = mb_convert_encoding($_GET["baseboard"], 'UTF-8', 'ISO-8859-15');
	$system_product_name = mb_convert_encoding($_GET["system_product_name"], 'UTF-8', 'ISO-8859-15');
	$bios_release = mb_convert_encoding($_GET["bios_release"], 'UTF-8', 'ISO-8859-15');	
	$cpu = mb_convert_encoding($_GET["cpu"], 'UTF-8', 'ISO-8859-15');	
	$gpu = mb_convert_encoding($_GET["gpu"], 'UTF-8', 'ISO-8859-15');	
	$windows_edition = mb_convert_encoding($_GET["windows_edition"], 'UTF-8', 'ISO-8859-15');
	$player_name = mb_convert_encoding($_GET["player_name"], 'UTF-8', 'ISO-8859-15');		
	$current_game = mb_convert_encoding($_GET["current_game"], 'UTF-8', 'ISO-8859-15');
	$timestamp = time();
	
	if (empty($_GET["macaddr1"]) ){
		die;
	}
	
	$query = "SELECT hostname, macaddr1 FROM assets where macaddr1='$macaddr1'";
	$result = $stats_db->query($query);
	$result = $result->fetchArray();

	if ($result['macaddr1'] == $macaddr1) {

	// Update if entry exists
	$query = "UPDATE assets set hostname='$hostname', macaddr1='$macaddr1', macaddr2='$macaddr2', ipv4addr='$ipv4addr', board_manufacturer='$board_manufacturer', baseboard='$baseboard', system_product_name='$system_product_name', bios_release='$bios_release', cpu='$cpu', gpu='$gpu', windows_edition='$windows_edition', player_name='$player_name', current_game='$current_game', timestamp='$timestamp' WHERE macaddr1='$macaddr1'";

	if( $stats_db->exec($query) ){
		$message = "ok";
	}else{
		$message = "error";
	}

	$counter = "";
	
	if (!empty ($_GET["current_game"])) {
	$counter = $stats_db->querySingle("SELECT counter FROM gamestats WHERE player='$player_name' AND game_id='$current_game'");

	if (!empty ($counter)) {
			$counter = $counter + 1;
			$query_gamestats = "UPDATE gamestats set counter='$counter' WHERE player='$player_name' AND game_id='$current_game'";
			$result = $stats_db->query($query_gamestats);
		}
		else {
			$counter = 1;
			$query_gamestats = "INSERT INTO gamestats (counter, game_id, player) VALUES ('$counter', '$current_game', '$player_name')";
			$result = $stats_db->query($query_gamestats);
		}
		
	}else {
	}
	}

	
	else {
	// Create new PC
	$query = "INSERT INTO assets (hostname, macaddr1, macaddr2, ipv4addr, board_manufacturer, baseboard, system_product_name, bios_release, cpu, gpu, windows_edition, player_name, current_game, timestamp) VALUES ('$hostname', '$macaddr1', '$macaddr2', '$ipv4addr', '$board_manufacturer', '$baseboard', '$system_product_name', '$bios_release', '$cpu', '$gpu', '$windows_edition', '$player_name', '$current_game', '$timestamp')";

	if( $stats_db->exec($query) ){
		$message = "ok";
	}else{
		$message = "error";
	}
}
	echo $message;

?>