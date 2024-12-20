<?php
// Session starten (notwendig um sie zu zerstören)
session_start();

// Alle Session-Variablen löschen
$_SESSION = array();

// Session-Cookie löschen
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Session zerstören
session_destroy();

// Zur index.php weiterleiten
header('Location: index.php');
exit;
?>