<?php
require_once 'config.php';
require_once '../config.php'; // ETI-LanPage Config
session_start();

// Einfache Authentifizierung
if (isset($order_admin_password)) $admin_password = $order_admin_password;
else $admin_password = "lanfood";

if (!isset($_SESSION['admin']) && (!isset($_POST['password']) || $_POST['password'] !== $admin_password)) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .login-form {
                max-width: 300px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            input, button {
                width: 100%;
                padding: 8px;
                margin: 5px 0;
            }
            button { 
                background: #4CAF50;
                color: white;
                border: none;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Admin Login</h2>
            <form method="post">
                <input type="password" name="password" placeholder="Admin-Passwort" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$_SESSION['admin'] = true;
$db = Database::getInstance();

// Aktionen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Status Update
    if (isset($_POST['update_status'])) {
        $stmt = $db->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->bindValue(':status', $_POST['status'], SQLITE3_TEXT);
        $stmt->bindValue(':id', $_POST['order_id'], SQLITE3_INTEGER);
        $stmt->execute();
    }
    
    // Bezahlt Status ändern
    if (isset($_POST['toggle_paid'])) {
        $stmt = $db->prepare('UPDATE orders SET paid = NOT paid WHERE id = :id');
        $stmt->bindValue(':id', $_POST['order_id'], SQLITE3_INTEGER);
        $stmt->execute();
    }
    
    // Einzelnen Eintrag löschen
    if (isset($_POST['delete_entry'])) {
        $stmt = $db->prepare('DELETE FROM orders WHERE id = :id');
        $stmt->bindValue(':id', $_POST['order_id'], SQLITE3_INTEGER);
        $stmt->execute();
    }
    
    // Alle Einträge löschen
    if (isset($_POST['delete_all']) && isset($_POST['confirm_delete_all'])) {
        $db->exec('DELETE FROM orders');
    }
    
    // Neue Bestellung manuell hinzufügen
    if (isset($_POST['add_manual_order'])) {
        $stmt = $db->prepare('
            INSERT INTO orders 
            (customer_name, flyer_number, quantity, size, notes, price, status, paid) 
            VALUES 
            (:name, :flyer, :quantity, :size, :notes, :price, :status, :paid)
        ');
        
        $stmt->bindValue(':name', $_POST['customer_name'], SQLITE3_TEXT);
        $stmt->bindValue(':flyer', $_POST['flyer_number'], SQLITE3_INTEGER);
        $stmt->bindValue(':quantity', $_POST['quantity'], SQLITE3_INTEGER);
        $stmt->bindValue(':size', $_POST['size'], SQLITE3_TEXT);
        $stmt->bindValue(':notes', $_POST['notes'], SQLITE3_TEXT);
        $stmt->bindValue(':price', $_POST['price'], SQLITE3_FLOAT);
        $stmt->bindValue(':status', $_POST['status'], SQLITE3_TEXT);
        $stmt->bindValue(':paid', isset($_POST['paid']) ? 1 : 0, SQLITE3_INTEGER);
        
        $stmt->execute();
    }
	
	// Bestellung bearbeiten
	if (isset($_POST['update_order'])) {
		$stmt = $db->prepare('
			UPDATE orders 
			SET customer_name = :name,
				flyer_number = :flyer,
				quantity = :quantity,
				size = :size,
				notes = :notes,
				price = :price,
				status = :status,
				paid = :paid
			WHERE id = :id
		');
		
		$stmt->bindValue(':id', $_POST['order_id'], SQLITE3_INTEGER);
		$stmt->bindValue(':name', $_POST['customer_name'], SQLITE3_TEXT);
		$stmt->bindValue(':flyer', $_POST['flyer_number'], SQLITE3_INTEGER);
		$stmt->bindValue(':quantity', $_POST['quantity'], SQLITE3_INTEGER);
		$stmt->bindValue(':size', $_POST['size'], SQLITE3_TEXT);
		$stmt->bindValue(':notes', $_POST['notes'], SQLITE3_TEXT);
		$stmt->bindValue(':price', $_POST['price'], SQLITE3_FLOAT);
		$stmt->bindValue(':status', $_POST['status'], SQLITE3_TEXT);
		$stmt->bindValue(':paid', isset($_POST['paid']) ? 1 : 0, SQLITE3_INTEGER);
		
		$stmt->execute();
		header('Location: admin.php');
		exit;
	}
	// Flyer hochladen
	if (isset($_POST['upload_flyer'])) {
		$uploadDir = 'flyer/';
		$flyerName = $_POST['flyer_name'];
		$fileName = time() . '_' . basename($_FILES['flyer_file']['name']);
		$targetPath = $uploadDir . $fileName;

		// Überprüfe Dateiformat
		$fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
		if ($fileType != "pdf") {
			echo "Nur PDF-Dateien sind erlaubt.";
			exit;
		}

		if (move_uploaded_file($_FILES['flyer_file']['tmp_name'], $targetPath)) {
			$stmt = $db->prepare('INSERT INTO flyers (name, filename) VALUES (:name, :filename)');
			$stmt->bindValue(':name', $flyerName, SQLITE3_TEXT);
			$stmt->bindValue(':filename', $fileName, SQLITE3_TEXT);
			$stmt->execute();
		}
	}

	// Flyer als aktiv setzen
	if (isset($_POST['set_active_flyer'])) {
		$db->exec('UPDATE flyers SET is_active = 0'); // Alle deaktivieren
		$stmt = $db->prepare('UPDATE flyers SET is_active = 1 WHERE id = :id');
		$stmt->bindValue(':id', $_POST['flyer_id'], SQLITE3_INTEGER);
		$stmt->execute();
	}

	// Flyer löschen
	if (isset($_POST['delete_flyer'])) {
		$stmt = $db->prepare('SELECT filename FROM flyers WHERE id = :id');
		$stmt->bindValue(':id', $_POST['flyer_id'], SQLITE3_INTEGER);
		$result = $stmt->execute()->fetchArray();
		
		if ($result) {
			unlink('flyer/' . $result['filename']); // Datei löschen
			$stmt = $db->prepare('DELETE FROM flyers WHERE id = :id');
			$stmt->bindValue(':id', $_POST['flyer_id'], SQLITE3_INTEGER);
			$stmt->execute();
		}
	}
}

// Bestellung zum Bearbeiten laden
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = :id');
    $stmt->bindValue(':id', $_GET['edit'], SQLITE3_INTEGER);
    $order = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
}

// Bestellungen und Statistiken abrufen
$results = $db->query('SELECT * FROM orders ORDER BY order_time DESC');

// Statistiken berechnen
$stats = $db->query('
    SELECT 
        COUNT(*) as total_orders,
        SUM(price * quantity) as total_revenue,
        SUM(CASE WHEN paid = 1 THEN price * quantity ELSE 0 END) as paid_revenue,
        SUM(CASE WHEN paid = 0 THEN price * quantity ELSE 0 END) as unpaid_revenue
    FROM orders
')->fetchArray(SQLITE3_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Pizza-Bestellungen Admin</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th, td { 
            padding: 8px; 
            border: 1px solid #ddd; 
            text-align: left;
        }
        th { 
            background: #f5f5f5; 
        }
        .status-Neu { background: #ffe6e6; }
        .status-In-Bearbeitung { background: #fff3e6; }
        .status-Fertig { background: #e6ffe6; }
        .paid { background: #e6ffe6; }
        .unpaid { background: #ffe6e6; }
        .action-buttons { 
            display: flex; 
            gap: 5px; 
        }
        .action-buttons button { 
            padding: 5px; 
        }
        .delete-all { 
            background: #ff4444; 
            color: white; 
            padding: 10px; 
            margin: 10px 0; 
        }
        .add-form { 
            background: #f8f8f8; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 5px;
        }
        .add-form div { 
            margin-bottom: 10px; 
        }
        .statistics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f8f8;
            border-radius: 5px;
        }
        .stat-box {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-box h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .stat-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #4CAF50;
        }
        button {
            cursor: pointer;
        }
        .logout-btn {
            float: right;
            padding: 10px 20px;
            background: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
		.form-table {
			width: 100%;
			margin-bottom: 20px;
		}
		
		.form-table td {
			padding: 8px;
			vertical-align: top;
		}
		
		.form-table td:first-child {
			width: 150px;
			font-weight: bold;
		}
		
		.form-table input[type="text"],
		.form-table input[type="number"],
		.form-table select,
		.form-table textarea {
			width: 100%;
			padding: 8px;
			border: 1px solid #ddd;
			border-radius: 4px;
		}
		
		.edit-form {
			background: #f9f9f9;
			padding: 20px;
			margin: 20px 0;
			border-radius: 5px;
			border: 1px solid #ddd;
		}
		
		.button-group {
			margin-top: 15px;
			text-align: right;
		}
		
		.button-group button {
			margin-left: 10px;
			padding: 8px 15px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}
		
		.button-group button[type="submit"] {
			background: #4CAF50;
			color: white;
		}
		
		.button-group button[type="button"] {
			background: #f44336;
			color: white;
		}
		.message {
			padding: 10px;
			margin-bottom: 10px;
			border-radius: 4px;
			text-align: center;
		}

		.message.success {
			background-color: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}

		.message.error {
			background-color: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}

		/* Anpassung der Modal-Position */
		.flyer-management {
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 90%;
			max-width: 1200px;
			max-height: 90vh;
			overflow-y: auto;
			background: #fff;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 0 20px rgba(0,0,0,0.3);
			z-index: 1000;
		}

		.close-button {
			position: absolute;
			top: 10px;
			right: 10px;
			background: none;
			border: none;
			font-size: 32px;
			cursor: pointer;
			padding: 5px 10px;
			color: #666;
			transition: color 0.3s ease;
		}

		.close-button:hover {
			color: #000;
		}
		.section-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
		}

		.toggle-flyer-btn {
			background: #4CAF50;
			color: white;
			border: none;
			padding: 10px 20px;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
		}

		.toggle-flyer-btn:hover {
			background: #45a049;
		}

		.overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0,0,0,0.5);
			z-index: 999;
		}

		.upload-form {
			margin-bottom: 20px;
			padding: 15px;
			background: #f9f9f9;
			border-radius: 4px;
		}

		.form-group {
			margin-bottom: 15px;
		}

		.form-group label {
			display: block;
			margin-bottom: 5px;
		}

		.flyers-table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
		}

		.flyers-table th,
		.flyers-table td {
			padding: 10px;
			border: 1px solid #ddd;
			text-align: left;
		}

		.status-button {
			padding: 5px 10px;
			border: none;
			border-radius: 3px;
			cursor: pointer;
		}

		.status-button.active {
			background: #4CAF50;
			color: white;
		}

		.view-button,
		.delete-button {
			padding: 5px 10px;
			margin: 0 5px;
			border: none;
			border-radius: 3px;
			cursor: pointer;
			text-decoration: none;
			display: inline-block;
		}

		.view-button {
			background: #2196F3;
			color: white;
		}

		.delete-button {
			background: #f44336;
			color: white;
		}
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-btn">Logout</a>
		<div class="action-buttons" style="margin: 20px 0;">
			<button type="button" onclick="toggleFlyerManagement()" class="toggle-flyer-btn">
				Flyer-Verwaltung anzeigen
			</button>
		</div>

		<!-- Flyer-Verwaltungsbereich mit display: none -->
		<div id="flyerManagement" class="flyer-management" style="display: none;">
			<div class="section-header">
				<h2>Flyer-Verwaltung</h2>
				<button type="button" onclick="toggleFlyerManagement()" class="close-button">x</button>
			</div>
			
			<!-- Upload-Formular -->
			<form id="uploadForm" class="upload-form">
				<div class="form-group">
					<label>Flyer-Name:</label>
					<input type="text" name="flyer_name" required>
				</div>
				<div class="form-group">
					<label>PDF-Datei:</label>
					<input type="file" name="flyer_file" accept=".pdf" required>
				</div>
				<button type="button" onclick="uploadFlyer()">Flyer hochladen</button>
			</form>

			<!-- Flyer-Liste -->
			<table class="flyers-table">
				<thead>
					<tr>
						<th>Name</th>
						<th>Dateiname</th>
						<th>Upload-Datum</th>
						<th>Status</th>
						<th>Aktionen</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$flyers = $db->query('SELECT * FROM flyers ORDER BY upload_date DESC');
					while ($flyer = $flyers->fetchArray(SQLITE3_ASSOC)):
					?>
					<tr>
						<td><?= htmlspecialchars($flyer['name']) ?></td>
						<td><?= htmlspecialchars($flyer['filename']) ?></td>
						<td><?= date('d.m.Y H:i', strtotime($flyer['upload_date'])) ?></td>
						<td>
							<button type="button" 
									onclick="updateFlyerStatus(<?= $flyer['id'] ?>)"
									class="status-button <?= $flyer['is_active'] ? 'active' : '' ?>">
								<?= $flyer['is_active'] ? 'Aktiv' : 'Inaktiv' ?>
							</button>
						</td>
						<td>
							<a href="flyer/<?= htmlspecialchars($flyer['filename']) ?>" 
							   target="_blank" class="view-button">Ansehen</a>
							<button type="button" 
									onclick="deleteFlyer(<?= $flyer['id'] ?>)"
									class="delete-button">Löschen</button>
						</td>
					</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	
        <h1>Bestellungen Verwalten</h1>

        <!-- Statistik-Dashboard -->
        <div class="statistics">
            <div class="stat-box">
                <h3>Gesamtbestellungen</h3>
                <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
            </div>
            <div class="stat-box">
                <h3>Gesamtumsatz</h3>
                <div class="stat-value"><?= number_format($stats['total_revenue'], 2) ?> €</div>
            </div>
            <div class="stat-box">
                <h3>Bezahlter Umsatz</h3>
                <div class="stat-value"><?= number_format($stats['paid_revenue'], 2) ?> €</div>
            </div>
            <div class="stat-box">
                <h3>Offene Zahlungen</h3>
                <div class="stat-value"><?= number_format($stats['unpaid_revenue'], 2) ?> €</div>
            </div>
        </div>
		
		<?php if (isset($order)): ?>
		<div class="edit-form">
			<h2>Bestellung #<?= $order['id'] ?> bearbeiten</h2>
			<form method="post">
				<input type="hidden" name="order_id" value="<?= $order['id'] ?>">
				<table class="form-table">
					<tr>
						<td>Name:</td>
						<td>
							<input type="text" name="customer_name" 
								   value="<?= htmlspecialchars($order['customer_name']) ?>" required>
						</td>
					</tr>
					<tr>
						<td>Flyer-Nummer:</td>
						<td>
							<input type="number" name="flyer_number" 
								   value="<?= $order['flyer_number'] ?>" required>
						</td>
					</tr>
					<tr>
						<td>Anzahl:</td>
						<td>
							<input type="number" name="quantity" 
								   value="<?= $order['quantity'] ?>" min="1" required>
						</td>
					</tr>
					<tr>
						<td>Größe:</td>
						<td>
							<select name="size" required>
								<option value="Normal" <?= $order['size'] == 'Normal' ? 'selected' : '' ?>>
									Normal
								</option>
								<option value="Klein" <?= $order['size'] == 'Klein' ? 'selected' : '' ?>>
									Klein
								</option>
								<option value="Groß" <?= $order['size'] == 'Groß' ? 'selected' : '' ?>>
									Groß
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Bemerkungen:</td>
						<td>
							<textarea name="notes" rows="3"><?= htmlspecialchars($order['notes']) ?></textarea>
						</td>
					</tr>
					<tr>
						<td>Einzelpreis inkl. Lieferung:</td>
						<td>
							<input type="number" step="0.01" name="price" 
								   value="<?= $order['price'] ?>" required onchange="calculateTotal(this)">
							<div class="total-price">
								Gesamtsumme: <?= number_format($order['price'] * $order['quantity'], 2) ?> €
							</div>
						</td>
					</tr>
					<tr>
						<td>Status:</td>
						<td>
							<select name="status">
								<option value="Neu" <?= $order['status'] == 'Neu' ? 'selected' : '' ?>>
									Neu
								</option>
								<option value="In-Bearbeitung" <?= $order['status'] == 'In-Bearbeitung' ? 'selected' : '' ?>>
									In-Bearbeitung
								</option>
								<option value="Fertig" <?= $order['status'] == 'Fertig' ? 'selected' : '' ?>>
									Fertig
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Bezahlstatus:</td>
						<td>
							<label>
								<input type="checkbox" name="paid" <?= $order['paid'] ? 'checked' : '' ?>>
								Bezahlt
							</label>
						</td>
					</tr>
				</table>
				<div class="button-group">
					<button type="submit" name="update_order">Änderungen speichern</button>
					<button type="button" onclick="window.location.href='admin.php'">Abbrechen</button>
				</div>
			</form>
		</div>
	<?php endif; ?>

        <!-- Neue Bestellung hinzufügen -->
        <button onclick="toggleAddForm()">Neue Bestellung hinzufügen</button>
        <div id="addOrderForm" class="add-form" style="display:none;">
			<h2>Neue Bestellung hinzufügen</h2>
			<form method="post">
				<table class="form-table">
					<tr>
						<td>Name:</td>
						<td><input type="text" name="customer_name" required></td>
					</tr>
					<tr>
						<td>Flyer-Nummer:</td>
						<td><input type="number" name="flyer_number" required></td>
					</tr>
					<tr>
						<td>Anzahl:</td>
						<td><input type="number" name="quantity" min="1" required></td>
					</tr>
					<tr>
						<td>Größe:</td>
						<td>
							<select name="size" required>
								<option value="Normal">Normal (32cm)</option>
								<option value="Klein">Klein (26cm)</option>
								<option value="Groß">Groß (38cm)</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Bemerkungen:</td>
						<td><textarea name="notes" rows="3"></textarea></td>
					</tr>
					<tr>
						<td>Einzelpreis inkl. Lieferung:</td>
						<td>
							<input type="number" step="0.01" name="price" required onchange="calculateTotal(this)">
							<div class="total-price"></div>
						</td>
					</tr>
					<tr>
						<td>Status:</td>
						<td>
							<select name="status">
								<option value="Neu">Neu</option>
								<option value="In-Bearbeitung">In-Bearbeitung</option>
								<option value="Fertig">Fertig</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Bezahlstatus:</td>
						<td>
							<label>
								<input type="checkbox" name="paid">
								Bereits bezahlt
							</label>
						</td>
					</tr>
				</table>
				<div class="button-group">
					<button type="submit" name="add_manual_order">Bestellung hinzufügen</button>
					<button type="button" onclick="toggleAddForm()">Abbrechen</button>
				</div>
			</form>
		</div>

        <!-- Alle Einträge löschen -->
        <form method="post" class="delete-all" onsubmit="return confirm('Wirklich ALLE Einträge löschen?');">
            <label>
                <input type="checkbox" name="confirm_delete_all" required>
                Ich bestätige, dass ich alle Einträge löschen möchte
            </label>
            <button type="submit" name="delete_all">Alle Einträge löschen</button>
        </form>

        <!-- Bestellungen Tabelle -->
        <table class="orders-table">
            <thead>
				<tr>
					<th>Zeit</th>
					<th>Name</th>
					<th>Flyer-Nr</th>
					<th>Anzahl</th>
					<th>Größe</th>
					<th>Bemerkungen</th>
					<th>Preis</th>
					<th>Status</th>
					<th>Bezahlt</th>
					<th>Aktionen</th>
				</tr>
			</thead>
			<tbody id="ordersTableBody">
            <?php 
            $dayTotal = 0;
            $currentDate = '';
            while ($row = $results->fetchArray(SQLITE3_ASSOC)): 
				$orderDate = date('Y-m-d', strtotime($row['order_time']));
				
				if ($currentDate !== '' && $currentDate !== $orderDate && $dayTotal > 0) {
					echo "<tr style='background-color: #e0e0e0; font-weight: bold;'>";
					echo "<td colspan='6'>Tagessumme für $currentDate:</td>";
					echo "<td colspan='4'>" . number_format($dayTotal, 2) . " €</td>";
					echo "</tr>";
					$dayTotal = 0;
				}
				
				$currentDate = $orderDate;
				$dayTotal += ($row['price'] * $row['quantity']);
            ?>
            <tr class="status-<?= htmlspecialchars($row['status']) ?>">
                <td><?= htmlspecialchars($row['order_time']) ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['flyer_number']) ?></td>
                <td><?= htmlspecialchars($row['quantity']) ?></td>
                <td><?= htmlspecialchars($row['size']) ?></td>
                <td><?= htmlspecialchars($row['notes']) ?></td>
                <td>
					<?= number_format($row['price'], 2) ?> € 
					<?php if ($row['quantity'] > 1): ?>
						<br>
						<small>Gesamt: <?= number_format($row['price'] * $row['quantity'], 2) ?> €</small>
					<?php endif; ?>
				</td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="Neu" <?= $row['status'] == 'Neu' ? 'selected' : '' ?>>Neu</option>
                            <option value="In-Bearbeitung" <?= $row['status'] == 'In-Bearbeitung' ? 'selected' : '' ?>>In-Bearbeitung</option>
                            <option value="Fertig" <?= $row['status'] == 'Fertig' ? 'selected' : '' ?>>Fertig</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
                <td class="<?= $row['paid'] ? 'paid' : 'unpaid' ?>">
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="toggle_paid">
                            <?= $row['paid'] ? 'Bezahlt ✓' : 'Nicht bezahlt ✕' ?>
                        </button>
                    </form>
                </td>
				<td>
					<div class="action-buttons">
						<a href="admin.php?edit=<?= $row['id'] ?>">
							<button type="button">Bearbeiten</button>
						</a>
						<form method="post" style="display: inline;" onsubmit="return confirm('Bestellung wirklich löschen?');">
							<input type="hidden" name="order_id" value="<?= $row['id'] ?>">
							<button type="submit" name="delete_entry">Löschen</button>
						</form>
					</div>
				</td>
            </tr>
            <?php endwhile; 
            
            // Zeige die letzte Tagessumme
            if ($dayTotal > 0) {
				echo "<tr style='background-color: #e0e0e0; font-weight: bold;'>";
				echo "<td colspan='6'>Tagessumme für $currentDate:</td>";
				echo "<td colspan='4'>" . number_format($dayTotal, 2) . " €</td>";
				echo "</tr>";
			}
            ?>
			</tbody>
			<!-- Neue separate Tabelle für neue Einträge -->
			<tbody id="newOrdersTableBody"></tbody>
        </table>
    </div>

    <script>
        function toggleAddForm() {
            var form = document.getElementById('addOrderForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
		// Letzte bekannte Bestellungs-ID
		let lastKnownOrderId = <?php
			$result = $db->query('SELECT MAX(id) as max_id FROM orders');
			$row = $result->fetchArray(SQLITE3_ASSOC);
			echo $row['max_id'] ?? 0;
		?>;

		// Funktion zum Formatieren der Zeit
		function formatTime(timestamp) {
			return new Date(timestamp).toLocaleTimeString('de-DE', {
				hour: '2-digit',
				minute: '2-digit'
			});
		}

		// Funktion zum Formatieren des Preises
		function formatPrice(price) {
			return new Intl.NumberFormat('de-DE', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			}).format(price);
		}

		// Funktion zum Prüfen auf neue Bestellungen
		function checkForNewOrders() {
			fetch(`check_new_orders.php?last_id=${lastKnownOrderId}`)
				.then(response => response.json())
				.then(data => {
					if (data.orders && data.orders.length > 0) {
						const newOrdersBody = document.getElementById('newOrdersTableBody');
						
						data.orders.forEach(order => {
							// Neue Zeile erstellen
							const row = document.createElement('tr');
							row.className = `status-${order.status.replace(' ', '-')}`;
							row.innerHTML = `
								<td>${formatTime(order.order_time)}</td>
								<td>${escapeHtml(order.customer_name)}</td>
								<td>${escapeHtml(order.flyer_number)}</td>
								<td>${order.quantity}</td>
								<td>${escapeHtml(order.size)}</td>
								<td>${escapeHtml(order.notes || '')}</td>
								<td>
									${formatPrice(order.price)} € 
									${order.quantity > 1 ? `<br><small>Gesamt: ${formatPrice(order.price * order.quantity)} €</small>` : ''}
								</td>
								<td>
									<form method="post" style="display: inline;">
										<input type="hidden" name="order_id" value="${order.id}">
										<select name="status" onchange="this.form.submit()">
											<option value="Neu" ${order.status === 'Neu' ? 'selected' : ''}>Neu</option>
											<option value="In-Bearbeitung" ${order.status === 'In-Bearbeitung' ? 'selected' : ''}>In-Bearbeitung</option>
											<option value="Fertig" ${order.status === 'Fertig' ? 'selected' : ''}>Fertig</option>
										</select>
										<input type="hidden" name="update_status" value="1">
									</form>
								</td>
								<td class="${order.paid ? 'paid' : 'unpaid'}">
									<form method="post" style="display: inline;">
										<input type="hidden" name="order_id" value="${order.id}">
										<button type="submit" name="toggle_paid">
											${order.paid ? 'Bezahlt ✓' : 'Nicht bezahlt ✕'}
										</button>
									</form>
								</td>
								<td>
									<div class="action-buttons">
										<a href="admin.php?edit=${order.id}">
											<button type="button">Bearbeiten</button>
										</a>
										<form method="post" style="display: inline;" onsubmit="return confirm('Bestellung wirklich löschen?');">
											<input type="hidden" name="order_id" value="${order.id}">
											<button type="submit" name="delete_entry">Löschen</button>
										</form>
									</div>
								</td>
							`;
							
							// Animation für neue Einträge
							row.style.animation = 'newOrder 2s';
							newOrdersBody.insertBefore(row, newOrdersBody.firstChild);
							
							// Ton abspielen bei neuer Bestellung
							playNotificationSound();
						});

						// Update der letzten bekannten ID
						lastKnownOrderId = Math.max(...data.orders.map(order => order.id));
					}
				})
				.catch(error => console.error('Error:', error));
		}

		// Funktion zum Abspielen des Benachrichtigungstons
		function playNotificationSound() {
			const audio = new Audio('notification.mp3'); // Stelle sicher, dass diese Datei existiert
			audio.play().catch(e => console.log('Audio konnte nicht abgespielt werden:', e));
		}

		// Hilfsfunktion zum Escapen von HTML
		function escapeHtml(unsafe) {
			return unsafe
				? unsafe.toString()
					.replace(/&/g, "&amp;")
					.replace(/</g, "&lt;")
					.replace(/>/g, "&gt;")
					.replace(/"/g, "&quot;")
					.replace(/'/g, "&#039;")
				: '';
		}

		// Regelmäßige Überprüfung auf neue Bestellungen
		setInterval(checkForNewOrders, 10000); // Alle 10 Sekunden

		// Initiales CSS für die Animation
		const style = document.createElement('style');
		style.textContent = `
			@keyframes newOrder {
				from { background-color: #ffd700; }
				to { background-color: transparent; }
			}
		`;
		document.head.appendChild(style);
		
		function calculateTotal(input) {
			const form = input.closest('form');
			const quantity = parseInt(form.querySelector('input[name="quantity"]').value) || 1;
			const price = parseFloat(input.value) || 0;
			const total = (price * quantity).toFixed(2);
			
			const totalDiv = input.parentNode.querySelector('.total-price');
			totalDiv.textContent = `Gesamtsumme: ${total} €`;
		}
		
		// Vorhandene Preise initial berechnen
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('input[name="price"]').forEach(function(input) {
				calculateTotal(input);
			});
		});

		// Event-Listener für Mengenänderungen
		document.querySelectorAll('input[name="quantity"]').forEach(function(input) {
			input.addEventListener('change', function() {
				const priceInput = this.closest('form').querySelector('input[name="price"]');
				calculateTotal(priceInput);
			});
		});
		
		document.addEventListener('DOMContentLoaded', function() {
			const overlay = document.createElement('div');
			overlay.className = 'overlay';
			document.body.appendChild(overlay);
		});

		function toggleFlyerManagement() {
			const managementDiv = document.getElementById('flyerManagement');
			const overlay = document.querySelector('.overlay');
			const toggleBtn = document.querySelector('.toggle-flyer-btn');
			
			if (managementDiv.style.display === 'none') {
				overlay.style.display = 'block';
				managementDiv.style.display = 'block';
				toggleBtn.style.display = 'none'; // Toggle-Button ausblenden
				
				// Sanfte Einblendung
				setTimeout(() => {
					managementDiv.style.opacity = '1';
				}, 10);
			} else {
				overlay.style.display = 'none';
				managementDiv.style.display = 'none';
				managementDiv.style.opacity = '0';
				toggleBtn.style.display = 'block'; // Toggle-Button wieder einblenden
			}
		}
		function updateFlyerStatus(flyerId) {
			const formData = new FormData();
			formData.append('set_active_flyer', '1');
			formData.append('flyer_id', flyerId);
			
			fetch('process_flyer.php', {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					updateFlyerTable();
					showMessage('Status erfolgreich aktualisiert', 'success');
				} else {
					showMessage(data.message || 'Ein Fehler ist aufgetreten', 'error');
				}
			})
			.catch(error => {
				showMessage('Ein Fehler ist aufgetreten', 'error');
				console.error('Error:', error);
			});
		}

		function deleteFlyer(flyerId) {
			if (confirm('Flyer wirklich löschen?')) {
				const formData = new FormData();
				formData.append('delete_flyer', '1');
				formData.append('flyer_id', flyerId);
				
				fetch('process_flyer.php', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						updateFlyerTable();
						showMessage('Flyer erfolgreich gelöscht', 'success');
					} else {
						showMessage(data.message || 'Ein Fehler ist aufgetreten', 'error');
					}
				})
				.catch(error => {
					showMessage('Ein Fehler ist aufgetreten', 'error');
					console.error('Error:', error);
				});
			}
		}

		function updateFlyerTable() {
			fetch('get_flyers.php')
				.then(response => response.json())
				.then(data => {
					const tableBody = document.querySelector('.flyers-table tbody');
					tableBody.innerHTML = data.html;
				})
				.catch(error => {
					console.error('Error:', error);
					showMessage('Fehler beim Aktualisieren der Tabelle', 'error');
				});
		}
		
		function uploadFlyer() {
			const form = document.getElementById('uploadForm');
			const formData = new FormData(form);
			formData.append('upload_flyer', '1');

			fetch('process_flyer.php', {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					form.reset(); // Formular zurücksetzen
					updateFlyerTable();
					showMessage('Flyer erfolgreich hochgeladen', 'success');
				} else {
					showMessage(data.message || 'Ein Fehler ist aufgetreten', 'error');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				showMessage('Ein Fehler ist aufgetreten', 'error');
			});
		}

		function showMessage(message, type) {
			const messageDiv = document.createElement('div');
			messageDiv.className = `message ${type}`;
			messageDiv.textContent = message;
			
			const managementDiv = document.getElementById('flyerManagement');
			managementDiv.insertBefore(messageDiv, managementDiv.firstChild);
			
			setTimeout(() => {
				messageDiv.remove();
			}, 3000);
		}
    </script>
</body>
</html>
