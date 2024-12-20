<?php
require_once 'config.php';

// Überprüfen ob es sich um einen POST-Request handelt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance();
    
    // Validierung der Eingabefelder
    $errors = [];
    
    // Name validieren
    if (empty($_POST['customer_name'])) {
        $errors[] = $catering['validation']['name_required'];
    }
    
    // Flyer-Nummer validieren
    if (!isset($_POST['flyer_number']) || !is_numeric($_POST['flyer_number'])) {
        $errors[] = $catering['validation']['flyer_number_invalid'];
    }
    
    // Anzahl validieren
    if (!isset($_POST['quantity']) || !is_numeric($_POST['quantity']) || $_POST['quantity'] < 1) {
        $errors[] = $catering['validation']['quantity_invalid'];
    }
    
    // Größe validieren
    $allowedSizes = ['normal', 'small', 'big'];
    if (!isset($_POST['size']) || !in_array($_POST['size'], $allowedSizes)) {
        $errors[] = $catering['validation']['size_invalid'];
    }
    
    // Preis validieren
    if (!isset($_POST['price']) || !is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $errors[] = $catering['validation']['price_invalid'];
    }
	
	// Status validieren
	$validStatus = ['new', 'processing', 'completed'];
	if (isset($_POST['status']) && !in_array($_POST['status'], $validStatus)) {
		$errors[] = $catering['validation']['status_invalid'];
		$status = $_POST['status'];
	}
	else $status = 'new';
    
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
			
			$status = isset($_POST['status']) ? $_POST['status'] : 'new';

            // Bindung der Werte einschließlich der Zeit
            $stmt->bindValue(':name', strip_tags($_POST['customer_name']), SQLITE3_TEXT);
            $stmt->bindValue(':flyer', (int)$_POST['flyer_number'], SQLITE3_INTEGER);
            $stmt->bindValue(':quantity', (int)$_POST['quantity'], SQLITE3_INTEGER);
            $stmt->bindValue(':size', $_POST['size'], SQLITE3_TEXT);
            $stmt->bindValue(':notes', strip_tags($_POST['notes'] ?? ''), SQLITE3_TEXT);
            $stmt->bindValue(':price', (float)$_POST['price'], SQLITE3_FLOAT);
            $stmt->bindValue(':status', $status, SQLITE3_TEXT);
            $stmt->bindValue(':paid', 0, SQLITE3_INTEGER);
            $stmt->bindValue(':order_time', $currentTime, SQLITE3_TEXT);
            
            if ($stmt->execute()) {
                $response = [
                    'success' => true,
                    'message' => $catering['messages']['order_success'],
                    'orderId' => $db->lastInsertRowID()
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => $catering['messages']['save_error'],
                    'errors' => [$catering['messages']['database_error']]
                ];
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $catering['messages']['general_error'],
                'errors' => [$e->getMessage()]
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => $catering['messages']['validation_error'],
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
        'message' => $catering['messages']['invalid_request'],
        'errors' => [$catering['messages']['post_only']]
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
