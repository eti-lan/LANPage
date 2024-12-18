<?php
require_once 'config.php';

// Überprüfen ob es sich um einen POST-Request handelt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance();
    
    // Validierung der Eingabefelder
    $errors = [];
    
    // Name validieren
    if (empty($_POST['customer_name'])) {
        $errors[] = 'Bitte einen Namen angeben';
    }
    
    // Flyer-Nummer validieren
    if (!isset($_POST['flyer_number']) || !is_numeric($_POST['flyer_number'])) {
        $errors[] = 'Bitte eine gültige Flyer-Nummer angeben';
    }
    
    // Anzahl validieren
    if (!isset($_POST['quantity']) || !is_numeric($_POST['quantity']) || $_POST['quantity'] < 1) {
        $errors[] = 'Bitte eine gültige Anzahl angeben';
    }
    
    // Größe validieren
    $allowedSizes = ['Normal', 'Klein', 'Groß'];
    if (!isset($_POST['size']) || !in_array($_POST['size'], $allowedSizes)) {
        $errors[] = 'Bitte eine gültige Größe auswählen';
    }
    
    // Preis validieren
    if (!isset($_POST['price']) || !is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $errors[] = 'Bitte einen gültigen Preis angeben';
    }
    
    // Wenn keine Fehler aufgetreten sind, speichern wir die Bestellung
    if (empty($errors)) {
        try {
            $stmt = $db->prepare('
				INSERT INTO orders 
				(customer_name, flyer_number, quantity, size, notes, price, status, paid, order_time) 
				VALUES 
				(:name, :flyer, :quantity, :size, :notes, :price, :status, :paid, :order_time)
			');

			// Aktuelle Zeit im korrekten Format
			$currentTime = date('Y-m-d H:i:s');

			// Bindung der Werte einschließlich der Zeit
			$stmt->bindValue(':name', strip_tags($_POST['customer_name']), SQLITE3_TEXT);
			$stmt->bindValue(':flyer', (int)$_POST['flyer_number'], SQLITE3_INTEGER);
			$stmt->bindValue(':quantity', (int)$_POST['quantity'], SQLITE3_INTEGER);
			$stmt->bindValue(':size', $_POST['size'], SQLITE3_TEXT);
			$stmt->bindValue(':notes', strip_tags($_POST['notes'] ?? ''), SQLITE3_TEXT);
			$stmt->bindValue(':price', (float)$_POST['price'], SQLITE3_FLOAT);
			$stmt->bindValue(':status', 'Neu', SQLITE3_TEXT);
			$stmt->bindValue(':paid', 0, SQLITE3_INTEGER);
			$stmt->bindValue(':order_time', $currentTime, SQLITE3_TEXT);
            
            if ($stmt->execute()) {
                $response = [
                    'success' => true,
                    'message' => 'Bestellung erfolgreich aufgegeben',
                    'orderId' => $db->lastInsertRowID()
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Speichern der Bestellung',
                    'errors' => ['Datenbankfehler']
                ];
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Ein Fehler ist aufgetreten',
                'errors' => [$e->getMessage()]
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Validierungsfehler',
            'errors' => $errors
        ];
    }
    
    // Sende JSON-Response zurück
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // Wenn kein POST-Request, zurück zur Bestellseite
    $response = [
        'success' => false,
        'message' => 'Ungültige Anfrage-Methode',
        'errors' => ['Nur POST-Requests sind erlaubt']
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
