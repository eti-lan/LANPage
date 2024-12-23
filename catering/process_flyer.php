<?php
require_once 'config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    $db = Database::getInstance();

    // Flyer hochladen
    if (isset($_FILES['flyer_file']) && isset($_POST['flyer_name'])) {
        $uploadDir = 'flyer/';
        $flyerName = strip_tags($_POST['flyer_name']);
        $fileName = time() . '_' . basename($_FILES['flyer_file']['name']);
        $targetPath = $uploadDir . $fileName;

        // Überprüfe Dateiformat
        $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        if ($fileType != "pdf") {
            throw new Exception("Nur PDF-Dateien sind erlaubt.");
        }

        // Überprüfe Dateigröße (max. 10MB)
        if ($_FILES['flyer_file']['size'] > 10000000) {
            throw new Exception("Die Datei ist zu groß (max. 10MB).");
        }

        if (move_uploaded_file($_FILES['flyer_file']['tmp_name'], $targetPath)) {
            $stmt = $db->prepare('INSERT INTO flyers (name, filename) VALUES (:name, :filename)');
            $stmt->bindValue(':name', $flyerName, SQLITE3_TEXT);
            $stmt->bindValue(':filename', $fileName, SQLITE3_TEXT);
            $stmt->execute();

            $response['success'] = true;
            $response['message'] = 'Flyer erfolgreich hochgeladen.';
        } else {
            throw new Exception("Fehler beim Hochladen der Datei.");
        }
    }

    // Flyer als aktiv setzen
    if (isset($_POST['set_active_flyer']) && isset($_POST['flyer_id'])) {
        $db->exec('UPDATE flyers SET is_active = 0'); // Alle deaktivieren
		$response['message'] = 'db update';
        $stmt = $db->prepare('UPDATE flyers SET is_active = 1 WHERE id = :id');
        $stmt->bindValue(':id', $_POST['flyer_id'], SQLITE3_INTEGER);
        $stmt->execute();

        $response['success'] = true;
        $response['message'] = 'Flyer-Status aktualisiert.';
    }

    // Flyer löschen
    if (isset($_POST['delete_flyer']) && isset($_POST['flyer_id'])) {
        // Zuerst Dateiname aus der Datenbank holen
        $stmt = $db->prepare('SELECT filename FROM flyers WHERE id = :id');
        $stmt->bindValue(':id', $_POST['flyer_id'], SQLITE3_INTEGER);
        $result = $stmt->execute()->fetchArray();
        
        if ($result) {
            $filePath = 'flyer/' . $result['filename'];
            
            // Datei löschen, falls sie existiert
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Datenbankeintrag löschen
            $stmt = $db->prepare('DELETE FROM flyers WHERE id = :id');
            $stmt->bindValue(':id', $_POST['flyer_id'], SQLITE3_INTEGER);
            $stmt->execute();

            $response['success'] = true;
            $response['message'] = 'Flyer erfolgreich gelöscht.';
        } else {
            throw new Exception("Flyer nicht gefunden.");
        }
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
