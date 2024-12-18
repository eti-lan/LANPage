<?php
require_once 'config.php';
$db = Database::getInstance();

$db->exec("UPDATE orders SET status = 'In-Bearbeitung' WHERE status = 'In Bearbeitung'");

// Hole alle Bestellungen des aktuellen Tages
$today = date('Y-m-d');
$stmt = $db->prepare('
    SELECT * FROM orders 
    WHERE date(order_time) = :today 
    ORDER BY order_time DESC
');
$stmt->bindValue(':today', $today, SQLITE3_TEXT);
$results = $stmt->execute();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza Bestellen</title>
    <link href="../assets/bootstrap.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">

        <div id="message" class="mt-3" style="display: none;"></div>
        
        <div class="row">
            <!-- Bestellformular -->
            <div class="col-md-6">
                <h2>Neue Bestellung aufgeben</h2>
                <form id="orderForm">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" class="form-control" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label>Nummer auf Flyer:</label>
                        <input type="number" class="form-control" name="flyer_number" required>
                    </div>
                    <div class="form-group">
                        <label>Anzahl:</label>
                        <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Größe:</label>
                        <select class="form-control" name="size" required>
                            <option value="Normal">Normal</option>
                            <option value="Klein">Klein</option>
                            <option value="Groß">Groß</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Bemerkungen:</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Einzelpreis inkl. Lieferung:</label>
                        <input type="number" class="form-control" name="price" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-success">Bestellen</button>
                </form>
            </div>

            <!-- Bestellübersicht -->
            <div class="col-md-6">
                <h2>Aktuelle Bestellungen heute</h2>
                <div style="overflow-x: auto;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Zeit</th>
                                <th>Name</th>
                                <th>Pizza</th>
                                <th>Größe</th>
                                <th>Preis</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                        <?php 
                        $totalOrders = 0;
                        $totalRevenue = 0;
                        while ($row = $results->fetchArray(SQLITE3_ASSOC)): 
                            $totalOrders++;
                            // Berechne den Gesamtpreis für diese Bestellung (Preis × Menge)
                            $orderTotal = $row['price'] * $row['quantity'];
                            $totalRevenue += $orderTotal;
                        ?>
                            <tr>
                                <td><?= date('H:i', strtotime($row['order_time'])) ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td>
                                    <?= htmlspecialchars($row['flyer_number']) ?>
                                    <?php if ($row['quantity'] > 1): ?>
                                        (<?= $row['quantity'] ?>x)
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['size']) ?></td>
                                <td>
                                    <?= number_format($row['price'], 2) ?> € 
                                    <?php if ($row['quantity'] > 1): ?>
                                        <br>
                                        <small>Gesamt: <?= number_format($orderTotal, 2) ?> €</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $row['status'] === 'Neu' ? 'badge-light' : ($row['status'] === 'In-Bearbeitung' ? 'badge-warning' : 'badge-success') ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <!-- Zusammenfassung -->
                            <tr class="font-weight-bold bg-light">
                                <td colspan="4">Gesamt heute:</td>
                                <td><?= number_format($totalRevenue, 2) ?> €</td>
                                <td><?= $totalOrders ?> Bestellungen</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Legende -->
                <div class="mt-3" style="font-size: 0.9em;">
                    <p>Status-Legende:</p>
                    <span class="badge badge-light">Neu</span>
                    <span class="badge badge-warning">In-Bearbeitung</span>
                    <span class="badge badge-success">Fertig</span>
                </div>
                <?php
                $activeFlyer = $db->query('SELECT * FROM flyers WHERE is_active = 1 LIMIT 1')
                                  ->fetchArray(SQLITE3_ASSOC);
                if ($activeFlyer): 
                ?>
                <div class="mt-3">
					<br />
                    <span>
						<a href="flyer/<?= htmlspecialchars($activeFlyer['filename']) ?>" 
							target="_blank" class="btn btn-primary">
							Aktuellen Flyer "<?= htmlspecialchars($activeFlyer['name']) ?>" anzeigen
						</a>
					</span>
					<span>
						<a href="admin.php" class="btn btn-secondary">Zur Verwaltung</a>
					</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../assets/jquery.js"></script>
    <script src="../assets/bootstrap.js"></script>
    <script>
        // Formular-Verarbeitung
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'block';
                
                if (data.success) {
                    messageDiv.className = 'alert alert-success';
                    messageDiv.textContent = data.message;
                    document.getElementById('orderForm').reset();
                    // Tabelle aktualisieren
                    updateOrdersTable();
                } else {
                    messageDiv.className = 'alert alert-danger';
                    messageDiv.textContent = data.message;
                }
                
                // Message nach 5 Sekunden ausblenden
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'block';
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
            });
        });

        // Funktion zum Aktualisieren der Bestelltabelle
        function updateOrdersTable() {
			fetch('get_orders.php?action=get_orders')
				.then(response => response.json())
				.then(data => {
					const tableBody = document.getElementById('ordersTableBody');
					let html = '';
					
					data.orders.forEach(order => {
						const orderTime = new Date(order.order_time).toLocaleTimeString('de-DE', {
							hour: '2-digit',
							minute: '2-digit'
						});
						
						const orderTotal = order.price * order.quantity;
						
						// Angemessene Bootstrap-Klassen anwenden
						const badgeClass = order.status === 'Neu' ? 'badge-light' : (order.status === 'In-Bearbeitung' ? 'badge-warning' : 'badge-success');

						html += `
							<tr>
								<td>${orderTime}</td>
								<td>${escapeHtml(order.customer_name)}</td>
								<td>
									${escapeHtml(order.flyer_number)}
									${order.quantity > 1 ? `(${order.quantity}x)` : ''}
								</td>
								<td>${escapeHtml(order.size)}</td>
								<td>
									${formatPrice(order.price)} € 
									${order.quantity > 1 ? `<br><small>Gesamt: ${formatPrice(orderTotal)} €</small>` : ''}
								</td>
								<td>
									<span class="badge ${badgeClass}">
										${escapeHtml(order.status)}
									</span>
								</td>
							</tr>
						`;
					});

					// Zusammenfassung
					html += `
						<tr class="font-weight-bold bg-light">
							<td colspan="4">Gesamt heute:</td>
							<td>${formatPrice(data.totalRevenue)} €</td>
							<td>${data.totalOrders} Bestellungen</td>
						</tr>
					`;

					tableBody.innerHTML = html;
				})
				.catch(error => console.error('Error:', error));
		}

        // Hilfsfunktion zum Formatieren von Preisen
        function formatPrice(price) {
            return new Intl.NumberFormat('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(price);
        }

        // Hilfsfunktion zum Escapen von HTML
        function escapeHtml(unsafe) {
            return unsafe
                .toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Automatische Aktualisierung alle 20 Sekunden
        setInterval(updateOrdersTable, 20000);
		
		// Preisberechnung
		document.querySelector('input[name="quantity"]').addEventListener('change', calculateTotalPrice);
		document.querySelector('input[name="price"]').addEventListener('change', calculateTotalPrice);

		function calculateTotalPrice() {
			const price = parseFloat(document.querySelector('input[name="price"]').value) || 0;
			const quantity = parseInt(document.querySelector('input[name="quantity"]').value) || 1;
			const total = (price * quantity).toFixed(2);
			
			// Optional: Anzeige der Gesamtsumme unter dem Formular
			let totalDisplay = document.getElementById('totalPrice');
			if (!totalDisplay) {
				totalDisplay = document.createElement('div');
				totalDisplay.id = 'totalPrice';
				totalDisplay.style.marginTop = '10px';
				totalDisplay.style.fontWeight = 'bold';
				document.querySelector('button[type="submit"]').parentNode.appendChild(totalDisplay);
			}
			totalDisplay.textContent = `Gesamtsumme: ${total} €`;
		}
    </script>
</body>
</html>
