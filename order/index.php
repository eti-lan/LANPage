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
<html>
<head>
    <title>Pizza Bestellen</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        label { 
            display: block; 
            margin-bottom: 5px; 
        }
        input, select, textarea { 
            width: 100%; 
            padding: 8px; 
        }
        button { 
            padding: 10px 20px; 
            background: #4CAF50; 
            color: white; 
            border: none; 
            cursor: pointer; 
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .orders-table th, 
        .orders-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .orders-table th {
            background-color: #f5f5f5;
        }
        .status-Neu { background: #ffe6e6; }
        .status-In-Bearbeitung { background: #fff3e6; }
        .status-Fertig { background: #e6ffe6; }
        .overview {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .overview h2 {
            margin-top: 0;
            color: #333;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .status-badge.Neu { background: #ffe6e6; }
        .status-badge.In-Bearbeitung { background: #fff3e6; }
        .status-badge.Fertig { background: #e6ffe6; }
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
        #message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
		.admin-link {
			position: fixed;
			top: 0;
			right: 0;
			padding: 10px;
			color: transparent;
			text-decoration: none;
			font-size: 12px;
			transition: all 0.3s ease;
			z-index: 1000;
		}

		.admin-link:hover {
			color: #666;
			background-color: rgba(255, 255, 255, 0.9);
		}

		.admin-corner {
			position: fixed;
			top: 0;
			right: 0;
			width: 50px;
			height: 50px;
			cursor: default;
		}
		.admin-button {
			display: inline-block;
			padding: 10px 20px;
			background: #007bff;
			color: white;
			text-decoration: none;
			border-radius: 5px;
			box-shadow: 0 2px 5px rgba(0,0,0,0.2);
			transition: all 0.3s ease;
		}

		.admin-button:hover {
			background: #0056b3;
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(0,0,0,0.2);
		}
		.current-flyer {
			position: relative;
			top: 20px;
			z-index: 1000;
		}

		.flyer-button {
			display: inline-block;
			padding: 10px 20px;
			background: #4CAF50;
			color: white;
			text-decoration: none;
			border-radius: 5px;
			box-shadow: 0 2px 5px rgba(0,0,0,0.2);
			transition: all 0.3s ease;
		}

		.flyer-button:hover {
			background: #45a049;
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(0,0,0,0.2);
		}
    </style>
</head>
<body>
	<div class="admin-corner">
		<a href="admin.php" class="admin-link">Admin</a>
	</div>
	<!--
    <h1>Pizza Bestellen</h1>
    -->
	
    <div id="message"></div>
    
    <div class="container">
        <!-- Bestellformular -->
        <div class="order-form">
            <h2>Neue Bestellung aufgeben</h2>
            <form id="orderForm">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label>Nummer auf Flyer:</label>
                    <input type="number" name="flyer_number" required>
                </div>
                <div class="form-group">
                    <label>Anzahl:</label>
                    <input type="number" name="quantity" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label>Größe:</label>
                    <select name="size" required>
                        <option value="Normal">Normal</option>
                        <option value="Klein">Klein</option>
                        <option value="Groß">Groß</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Bemerkungen:</label>
                    <textarea name="notes"></textarea>
                </div>
                <div class="form-group">
					<label>Einzelpreis inkl. Lieferung:</label>
					<input type="number" name="price" step="0.01" required>
				</div>
                <button type="submit">Bestellen</button>
            </form>
        </div>

        <!-- Bestellübersicht -->
        <div class="overview">
            <h2>Aktuelle Bestellungen heute</h2>
            <div style="overflow-x: auto;">
                <table class="orders-table">
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
                                <span class="status-badge <?= $row['status'] ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    
                    <!-- Zusammenfassung -->
                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                            <td colspan="4">Gesamt heute:</td>
                            <td><?= number_format($totalRevenue, 2) ?> €</td>
                            <td><?= $totalOrders ?> Bestellungen</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Legende -->
            <div style="margin-top: 15px; font-size: 0.9em;">
                <p>Status-Legende:</p>
                <span class="status-badge Neu">Neu</span>
                <span class="status-badge In-Bearbeitung">In-Bearbeitung</span>
                <span class="status-badge Fertig">Fertig</span>
            </div>
			<?php
			$activeFlyer = $db->query('SELECT * FROM flyers WHERE is_active = 1 LIMIT 1')
							  ->fetchArray(SQLITE3_ASSOC);
			if ($activeFlyer): 
			?>
			<div class="current-flyer">
				<a href="flyer/<?= htmlspecialchars($activeFlyer['filename']) ?>" 
				   target="_blank" class="flyer-button">
					Aktuellen Flyer "<?= htmlspecialchars($activeFlyer['name']) ?>" anzeigen
				</a>
				<a href="admin.php" class="admin-button" target="_blank">Zur Verwaltung</a>
			</div>
			<?php endif; ?>
        </div>
    </div>

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
                    messageDiv.className = 'success';
                    messageDiv.textContent = data.message;
                    document.getElementById('orderForm').reset();
                    // Tabelle aktualisieren
                    updateOrdersTable();
                } else {
                    messageDiv.className = 'error';
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
                messageDiv.className = 'error';
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
									<span class="status-badge ${order.status.replace(' ', '-')}">
										${escapeHtml(order.status)}
									</span>
								</td>
							</tr>
						`;
					});

					// Zusammenfassung
					html += `
						<tr style="font-weight: bold; background-color: #f0f0f0;">
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
