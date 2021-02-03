<?php
/**
 * LANPage - A simple website template for your LAN Party that integrates with LAN Launcher
 *
 * @package  lanpage
 * @author   eti and various anonymous
 *
 * Things required:
 * Webserver and PHP5 or newer with SQLite extension enabled
 *
 * Installation:
 * Copy or rename this config.sample.php to config.php and change values
 * chmod 0755 db/*.db
 */


/* Main configuration */
$lan_title = 'LAN Party';
$lang = 'german';

$logo = "./logo.png";

$enable_chat = false; /* not yet implemented */
$enable_stats = true;
$enable_downloads = true;
$enable_serverlist = true;

$enable_competition = true; /* be sure that the webserver user has write access to the folder /competition/data */
$competition_edit_password = "lan";

$enable_faq = true; /* insert questions directly into the database for now */


/* Show the top X of all games */
$stats_show_topgames = 12;

/* Time span from which statistical data is to be displayed, in hours */
$stats_expire = 48; 

/* How long a player is listed as 'online' after last message from LAN Launcher, in minutes */
$stats_playerstatus_timespan = 15;

/* Chat configuration */
$deleteOldComments = true;
$deleteOldCommentsAfter = '-24 hours';

/* Debugging */
$show_errors = true;