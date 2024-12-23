<?php
require_once 'config.php';
require_once 'admin_auth.php';

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
		$stmt = $db->prepare('
			UPDATE orders 
			SET paid = NOT paid,
				status = CASE 
					WHEN NOT paid = 1 THEN "completed" 
					ELSE "processing" 
				END
			WHERE id = :id
		');
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
            (:name, :flyer, :quantity, :size, :notes, CAST(:price AS DECIMAL(10,2)), :status, :paid)
        ');
        
        $stmt->bindValue(':name', $_POST['customer_name'], SQLITE3_TEXT);
        $stmt->bindValue(':flyer', $_POST['flyer_number'], SQLITE3_INTEGER);
        $stmt->bindValue(':quantity', $_POST['quantity'], SQLITE3_INTEGER);
        $stmt->bindValue(':size', $_POST['size'], SQLITE3_TEXT);
        $stmt->bindValue(':notes', $_POST['notes'], SQLITE3_TEXT);
        $stmt->bindValue(':price', floatval($_POST['price']), SQLITE3_FLOAT);
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
				price = CAST(:price AS DECIMAL(10,2)),
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
		$stmt->bindValue(':price', floatval($_POST['price']), SQLITE3_FLOAT);
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
            echo $catering['flyer']['upload']['error'];
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
    $stmt = $db->prepare('SELECT *, CAST(price AS DECIMAL(10,2)) as price FROM orders WHERE id = :id');
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
<html lang="<?= $lang == 'german' ? 'de' : 'en' ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($catering['admin']['title']) ?></title>
	<!--<link href="../assets/bootstrap.css" rel="stylesheet">-->
    <link href="admin_custom.css" rel="stylesheet">
</head>
<body>
    <div class="container">
		<a href="logout.php" class="logout-btn">Logout</a>
		<div class="action-buttons" style="margin: 20px 0;">
			<button type="button" onclick="toggleFlyerManagement()" class="toggle-flyer-btn">
				<?= htmlspecialchars($catering['flyer']['toggle']) ?>
			</button>
		</div>

		<!--// Flyer -->

		<div id="flyerManagement" class="flyer-management" style="display: none;">
			<div class="section-header">
				<h2><?= htmlspecialchars($catering['flyer']['management']) ?></h2>
				<button type="button" onclick="toggleFlyerManagement()" class="close-button" 
						title="<?= htmlspecialchars($catering['flyer']['actions']['close']) ?>">×</button>
			</div>
			
			<form id="uploadForm" class="upload-form">
				<div class="form-group">
					<label><?= htmlspecialchars($catering['flyer']['upload']['name']) ?>:</label>
					<input type="text" name="flyer_name" required>
				</div>
				<div class="form-group">
					<label><?= htmlspecialchars($catering['flyer']['upload']['file']) ?>:</label>
					<input type="file" name="flyer_file" accept=".pdf" required>
				</div>
				<button type="button" onclick="uploadFlyer()">
					<?= htmlspecialchars($catering['flyer']['upload']['button']) ?>
				</button>
			</form>

			<table class="flyers-table">
				<thead>
					<tr>
						<th><?= htmlspecialchars($catering['flyer']['list']['name']) ?></th>
						<th><?= htmlspecialchars($catering['flyer']['list']['filename']) ?></th>
						<th><?= htmlspecialchars($catering['flyer']['list']['upload_date']) ?></th>
						<th><?= htmlspecialchars($catering['flyer']['list']['status']) ?></th>
						<th><?= htmlspecialchars($catering['flyer']['list']['actions']) ?></th>
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
								<?= htmlspecialchars($flyer['is_active'] ? 
									$catering['flyer']['status']['active'] : 
									$catering['flyer']['status']['inactive']) ?>
							</button>
						</td>
						<td>
							<a href="flyer/<?= htmlspecialchars($flyer['filename']) ?>" 
							   target="_blank" class="view-button">
								<?= htmlspecialchars($catering['flyer']['actions']['view']) ?>
							</a>
							<button type="button" 
									onclick="deleteFlyer(<?= $flyer['id'] ?>)"
									class="delete-button">
								<?= htmlspecialchars($catering['flyer']['actions']['delete']) ?>
							</button>
						</td>
					</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		
        <h1><?= htmlspecialchars($catering['admin']['title']) ?></h1>

        <!-- Statistik-Dashboard -->
        <div class="statistics">
            <div class="stat-box">
                <h3><?= htmlspecialchars($catering['admin']['stats']['total_orders']) ?></h3>
                <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
            </div>
            <div class="stat-box">
                <h3><?= htmlspecialchars($catering['admin']['stats']['total_revenue']) ?></h3>
                <div class="stat-value"><?= number_format($stats['total_revenue'], 2) ?> <?= html_entity_decode($catering['currency']) ?></div>
            </div>
            <div class="stat-box">
                <h3><?= htmlspecialchars($catering['admin']['stats']['paid_revenue']) ?></h3>
                <div class="stat-value"><?= number_format($stats['paid_revenue'], 2) ?> <?= html_entity_decode($catering['currency']) ?></div>
            </div>
            <div class="stat-box">
                <h3><?= htmlspecialchars($catering['admin']['stats']['unpaid_revenue']) ?></h3>
                <div class="stat-value"><?= number_format($stats['unpaid_revenue'], 2) ?> <?= html_entity_decode($catering['currency']) ?></div>
            </div>
        </div>
		
		<?php 
			// Edit Form
			if (isset($order)): 
		?>
			<div class="edit-form">
				<h2><?= htmlspecialchars($catering['admin']['edit_order']) ?> #<?= $order['id'] ?></h2>
				<form method="post">
					<input type="hidden" name="order_id" value="<?= $order['id'] ?>">
					<table class="form-table">
						<tr>
							<td><?= htmlspecialchars($catering['name']) ?>:</td>
							<td>
								<input type="text" name="customer_name" 
									   value="<?= htmlspecialchars($order['customer_name']) ?>" required>
							</td>
						</tr>
						<tr>
							<td><?= htmlspecialchars($catering['flyer_number']) ?>:</td>
							<td>
								<input type="number" name="flyer_number" 
									   value="<?= $order['flyer_number'] ?>" required>
							</td>
						</tr>
						<tr>
							<td><?= htmlspecialchars($catering['quantity']) ?>:</td>
							<td>
								<input type="number" name="quantity" 
									   value="<?= $order['quantity'] ?>" min="1" required>
							</td>
						</tr>
						<tr>
							<td><?= htmlspecialchars($catering['size']) ?>:</td>
							<td>
								<select name="size" required>
									<option value="normal" <?= $order['size'] == 'normal' ? 'selected' : '' ?>>
										<?= htmlspecialchars($catering['size_normal']) ?>
									</option>
									<option value="small" <?= $order['size'] == 'small' ? 'selected' : '' ?>>
										<?= htmlspecialchars($catering['size_small']) ?>
									</option>
									<option value="big" <?= $order['size'] == 'big' ? 'selected' : '' ?>>
										<?= htmlspecialchars($catering['size_big']) ?>
									</option>
								</select>
							</td>
						</tr>
						<tr>
							<td><?= htmlspecialchars($catering['comments']) ?>:</td>
							<td>
								<textarea name="notes" rows="3"><?= htmlspecialchars($order['notes']) ?></textarea>
							</td>
						</tr>
						<tr>
							<td><?= htmlspecialchars($catering['price']) ?>:</td>
							<td>
								<input type="number" step="0.01" name="price" value="<?= number_format((float)$order['price'], 2, '.', '') ?>" required onchange="calculateTotal(this)">
								<div class="total-price">
									<?= htmlspecialchars($catering['total_sum']) ?>: <?= number_format($order['price'] * $order['quantity'], 2) ?> <?= html_entity_decode($catering['currency']) ?>
								</div>
							</td>
						</tr>
						<tr>
							<td><?= htmlspecialchars($catering['status']) ?>:</td>
							<td>
								<select name="status">
									<option value="new" <?= $order['status'] == 'new' ? 'selected' : '' ?>>
										<?= htmlspecialchars($catering['status_new']) ?>
									</option>
									<option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>
										<?= htmlspecialchars($catering['status_processing']) ?>
									</option>
									<option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>
										<?= htmlspecialchars($catering['status_completed']) ?>
									</option>
								</select>
							</td>
						</tr>
						<tr>
							<td><?= htmlspecialchars($catering['admin']['paid_status']) ?>:</td>
							<td>
								<label>
									<input type="checkbox" name="paid" <?= $order['paid'] ? 'checked' : '' ?>>
									<?= htmlspecialchars($catering['admin']['paid_status']) ?>
								</label>
							</td>
						</tr>
					</table>
					<div class="button-group">
						<button type="submit" name="update_order"><?= htmlspecialchars($catering['admin']['save_changes']) ?></button>
						<button type="button" onclick="window.location.href='admin.php'"><?= htmlspecialchars($catering['admin']['cancel']) ?></button>
					</div>
				</form>
			</div>
		<?php endif; ?>

        <!-- Neue Bestellung hinzufügen -->
		<button onclick="toggleAddForm()"><?= htmlspecialchars($catering['admin']['add_order']) ?></button>
		
        <div id="addOrderForm" class="add-form" style="display:none;">
			<h2><?= htmlspecialchars($catering['admin']['add_order']) ?></h2>
			<form method="post">
				<table class="form-table">
					<tr>
						<td><?= htmlspecialchars($catering['name']) ?>:</td>
						<td><input type="text" name="customer_name" required></td>
					</tr>
					<tr>
						<td><?= htmlspecialchars($catering['flyer_number']) ?>:</td>
						<td><input type="number" name="flyer_number" required></td>
					</tr>
					<tr>
						<td><?= htmlspecialchars($catering['quantity']) ?>:</td>
						<td><input type="number" name="quantity" min="1" required></td>
					</tr>
					<tr>
						<td><?= htmlspecialchars($catering['size']) ?>:</td>
						<td>
							<select name="size" required>
								<option value="normal"><?= htmlspecialchars($catering['size_normal']) ?></option>
								<option value="small"><?= htmlspecialchars($catering['size_small']) ?></option>
								<option value="big"><?= htmlspecialchars($catering['size_big']) ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?= htmlspecialchars($catering['comments']) ?>:</td>
						<td><textarea name="notes" rows="3"></textarea></td>
					</tr>
					<tr>
						<td><?= htmlspecialchars($catering['price']) ?>:</td>
						<td>
							<input type="number" step="0.01" name="price" required onchange="calculateTotal(this)">
							<div class="total-price"></div>
						</td>
					</tr>
					<tr>
						<td><?= htmlspecialchars($catering['status']) ?>:</td>
						<td>
							<select name="status">
								<option value="new"><?= htmlspecialchars($catering['status_new']) ?></option>
								<option value="processing"><?= htmlspecialchars($catering['status_processing']) ?></option>
								<option value="completed"><?= htmlspecialchars($catering['status_completed']) ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?= htmlspecialchars($catering['admin']['paid_status']) ?>:</td>
						<td>
							<label>
								<input type="checkbox" name="paid">
								<?= htmlspecialchars($catering['admin']['paid_status']) ?>
							</label>
						</td>
					</tr>
				</table>
				<div class="button-group">
					<button type="submit" name="add_manual_order"><?= htmlspecialchars($catering['admin']['add_button']) ?></button>
					<button type="button" onclick="toggleAddForm()"><?= htmlspecialchars($catering['admin']['cancel']) ?></button>
				</div>
			</form>
		</div>

        <!-- Alle Einträge löschen -->
		<form method="post" class="delete-all" onsubmit="return confirm('<?= htmlspecialchars($catering['admin']['confirm_delete']) ?>');">
			<label>
				<input type="checkbox" name="confirm_delete_all" required>
				<?= htmlspecialchars($catering['admin']['confirm_delete_checkbox']) ?>
			</label>
			<button type="submit" name="delete_all"><?= htmlspecialchars($catering['admin']['delete_all']) ?></button>
		</form>

        <!-- Bestellungen Tabelle -->
		<table class="orders-table">
			<thead>
				<tr>
					<th><?= htmlspecialchars($catering['time']) ?></th>
					<th><?= htmlspecialchars($catering['name']) ?></th>
					<th><?= htmlspecialchars($catering['flyer_number']) ?></th>
					<th><?= htmlspecialchars($catering['quantity']) ?></th>
					<th><?= htmlspecialchars($catering['size']) ?></th>
					<th><?= htmlspecialchars($catering['comments']) ?></th>
					<th><?= htmlspecialchars($catering['price']) ?></th>
					<th><?= htmlspecialchars($catering['status']) ?></th>
					<th><?= htmlspecialchars($catering['admin']['paid_status']) ?></th>
					<th><?= htmlspecialchars($catering['admin']['actions']) ?></th>
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
					echo "<td colspan='6'>" . htmlspecialchars($catering['total_summary']) . " " . $currentDate . ":</td>";
					echo "<td colspan='4'>" . number_format($dayTotal, 2) . " " . html_entity_decode($catering['currency']) . "</td>";
					echo "</tr>";
					$dayTotal = 0;
				}
				
				$currentDate = $orderDate;
				$dayTotal += ($row['price'] * $row['quantity']);
				$size='size_'.$row['size'];
			?>
			<tr class="status-<?= htmlspecialchars($row['status']) ?>">
				<td><?= htmlspecialchars($row['order_time']) ?></td>
				<td><?= htmlspecialchars($row['customer_name']) ?></td>
				<td><?= htmlspecialchars($row['flyer_number']) ?></td>
				<td>
					<?= htmlspecialchars($row['quantity']) ?>
					<?php if ($row['quantity'] > 1): ?>
						<?= htmlspecialchars($catering['quantity_times']) ?>
					<?php endif; ?>
				</td>
				<td><?= htmlspecialchars($catering[$size]) ?></td>
				<td><?= htmlspecialchars($row['notes']) ?></td>
				<td>
					<?= number_format($row['price'], 2) ?> <?= html_entity_decode($catering['currency']) ?>
					<?php if ($row['quantity'] > 1): ?>
						<br>
						<small><?= htmlspecialchars($catering['total']) ?>: <?= number_format($row['price'] * $row['quantity'], 2) ?> <?= html_entity_decode($catering['currency']) ?></small>
					<?php endif; ?>
				</td>
				<td>
					<form method="post" style="display: inline;">
						<input type="hidden" name="order_id" value="<?= $row['id'] ?>">
						<select name="status" onchange="this.form.submit()">
							<option value="new" <?= $row['status'] == 'new' ? 'selected' : '' ?>><?= htmlspecialchars($catering['status_new']) ?></option>
							<option value="processing" <?= $row['status'] == 'processing' ? 'selected' : '' ?>><?= htmlspecialchars($catering['status_processing']) ?></option>
							<option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>><?= htmlspecialchars($catering['status_completed']) ?></option>
						</select>
						<input type="hidden" name="update_status" value="1">
					</form>
				</td>
				<td class="<?= $row['paid'] ? 'paid' : 'unpaid' ?>">
					<form method="post" style="display: inline;">
						<input type="hidden" name="order_id" value="<?= $row['id'] ?>">
						<button type="submit" name="toggle_paid">
							<?= $row['paid'] ? htmlspecialchars($catering['admin']['paid_status']) . ' ✓' : htmlspecialchars($catering['admin']['unpaid_status']) . ' ✕' ?>
						</button>
					</form>
				</td>
				<td>
					<div class="action-buttons">
						<a href="admin.php?edit=<?= $row['id'] ?>">
							<button type="button"><?= htmlspecialchars($catering['admin']['edit']) ?></button>
						</a>
						<form method="post" style="display: inline;" onsubmit="return confirm('<?= htmlspecialchars($catering['admin']['confirm_delete_single']) ?>');">
							<input type="hidden" name="order_id" value="<?= $row['id'] ?>">
							<button type="submit" name="delete_entry"><?= htmlspecialchars($catering['admin']['delete']) ?></button>
						</form>
					</div>
				</td>
			</tr>
			<?php endwhile; ?>
			</tbody>
			<tbody id="newOrdersTableBody"></tbody>
		</table>
    </div>

    <script>
		function toggleAddForm() {
			const form = document.getElementById('addOrderForm');
			if (form) {
				form.style.display = form.style.display === 'none' ? 'block' : 'none';
			}
		}
		
		// Letzte bekannte Bestellungs-ID
		let lastKnownOrderId = <?php
			$result = $db->query('SELECT MAX(id) as max_id FROM orders');
			$row = $result->fetchArray(SQLITE3_ASSOC);
			echo $row['max_id'] ?? 0;
		?>;
		
		// Übersetzungen für JavaScript
		const translations = {
			new: <?= json_encode($catering['status_new']) ?>,
			processing: <?= json_encode($catering['status_processing']) ?>,
			completed: <?= json_encode($catering['status_completed']) ?>,
			paid: <?= json_encode($catering['admin']['paid_status']) ?>,
			unpaid: <?= json_encode($catering['admin']['unpaid_status']) ?>,
			edit: <?= json_encode($catering['admin']['edit']) ?>,
			delete: <?= json_encode($catering['admin']['delete']) ?>,
			total: <?= json_encode($catering['total']) ?>,
			confirmDelete: <?= json_encode($catering['admin']['confirm_delete_single']) ?>,
			totalSum: <?= json_encode($catering['total']) ?>,
			currency: <?= html_entity_decode(json_encode($catering['currency'])) ?>
		};

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
									${formatPrice(order.price)} ${translations.currency}
									${order.quantity > 1 ? `<br><small>${translations.total}: ${formatPrice(order.price * order.quantity)} ${translations.currency}</small>` : ''}
								</td>
								<td>
									<form method="post" style="display: inline;">
										<input type="hidden" name="order_id" value="${order.id}">
										<select name="status" onchange="this.form.submit()">
											<option value="new" ${order.status === 'new' ? 'selected' : ''}>${translations.new}</option>
											<option value="processing" ${order.status === 'processing' ? 'selected' : ''}>${translations.processing}</option>
											<option value="completed" ${order.status === 'completed' ? 'selected' : ''}>${translations.completed}</option>
										</select>
										<input type="hidden" name="update_status" value="1">
									</form>
								</td>
								<td class="${order.paid ? 'paid' : 'unpaid'}">
									<form method="post" style="display: inline;">
										<input type="hidden" name="order_id" value="${order.id}">
										<button type="submit" name="toggle_paid">
											${order.paid ? `${translations.paid} ✓` : `${translations.unpaid} ✕`}
										</button>
									</form>
								</td>
								<td>
									<div class="action-buttons">
										<a href="admin.php?edit=${order.id}">
											<button type="button">${translations.edit}</button>
										</a>
										<form method="post" style="display: inline;" onsubmit="return confirm('${translations.confirmDelete}');">
											<input type="hidden" name="order_id" value="${order.id}">
											<button type="submit" name="delete_entry">${translations.delete}</button>
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
			const price = parseFloat(form.querySelector('input[name="price"]').value) || 0;
			const total = (price * quantity).toFixed(2);
			
			const totalDiv = form.querySelector('.total-price');
			if (totalDiv) {
				totalDiv.textContent = `${translations.totalSum}: ${total} ${translations.currency}`;
			}
		}
		// Event Listener hinzufügen und Overlay erstellen
		document.addEventListener('DOMContentLoaded', function() {
			// Initial alle Preise berechnen
			document.querySelectorAll('input[name="price"]').forEach(function(input) {
				calculateTotal(input);
			});

			// Event Listener für Preis-Änderungen
			document.querySelectorAll('input[name="price"]').forEach(function(input) {
				input.addEventListener('input', function() {
					calculateTotal(this);
				});
			});

			// Event Listener für Mengen-Änderungen
			document.querySelectorAll('input[name="quantity"]').forEach(function(input) {
				input.addEventListener('input', function() {
					const priceInput = this.closest('form').querySelector('input[name="price"]');
					calculateTotal(priceInput);
				});
			});

			// Overlay erstellen
			const overlay = document.createElement('div');
			overlay.className = 'overlay';
			document.body.appendChild(overlay);
		});
		
		
		const flyerTranslations = {
			messages: {
				updateSuccess: <?= json_encode($catering['flyer']['messages']['update_success']) ?>,
				deleteConfirm: <?= json_encode($catering['flyer']['messages']['delete_confirm']) ?>,
				deleteSuccess: <?= json_encode($catering['flyer']['messages']['delete_success']) ?>,
				uploadSuccess: <?= json_encode($catering['flyer']['messages']['upload_success']) ?>,
				defaultError: <?= json_encode($catering['flyer']['messages']['default_error']) ?>,
				tableError: <?= json_encode($catering['flyer']['messages']['table_error']) ?>
			}
		};

		function toggleFlyerManagement() {
			const managementDiv = document.getElementById('flyerManagement');
			const overlay = document.querySelector('.overlay');
			const toggleBtn = document.querySelector('.toggle-flyer-btn');
			
			if (managementDiv.style.display === 'none') {
				overlay.style.display = 'block';
				managementDiv.style.display = 'block';
				toggleBtn.style.display = 'none';
				
				setTimeout(() => {
					managementDiv.style.opacity = '1';
				}, 10);
			} else {
				overlay.style.display = 'none';
				managementDiv.style.display = 'none';
				managementDiv.style.opacity = '0';
				toggleBtn.style.display = 'block';
			}
		}
		
		// Flyer-Management Funktionen
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
					showMessage(flyerTranslations.messages.updateSuccess, 'success');
				} else {
					showMessage(data.message || flyerTranslations.messages.defaultError, 'error');
				}
			})
			.catch(error => {
				showMessage(flyerTranslations.messages.defaultError, 'error');
				console.error('Error:', error);
			});
		}

		function deleteFlyer(flyerId) {
			if (confirm(flyerTranslations.messages.deleteConfirm)) {
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
						showMessage(flyerTranslations.messages.deleteSuccess, 'success');
					} else {
						showMessage(data.message || flyerTranslations.messages.defaultError, 'error');
					}
				})
				.catch(error => {
					showMessage(flyerTranslations.messages.defaultError, 'error');
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
					showMessage(flyerTranslations.messages.tableError, 'error');
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
					form.reset();
					updateFlyerTable();
					showMessage(flyerTranslations.messages.uploadSuccess, 'success');
				} else {
					showMessage(data.message || flyerTranslations.messages.defaultError, 'error');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				showMessage(flyerTranslations.messages.defaultError, 'error');
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
