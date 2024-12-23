<?php
/* load config */
require_once 'config.php';

$db = Database::getInstance();

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
<html lang="<?= $lang == 'german' ? 'de' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($catering['title']) ?></title>
    <link href="../assets/bootstrap.css" rel="stylesheet">
	<link href="custom.css" rel="stylesheet">
</head>
<body>
			<div class="container-fluid">
                <div class="row">
					<?php
					if(!isset($nav['catering'])):
					?>
					<!-- Überschrift über die gesamte Breite -->
                    <div class="col-12">
                        <div class="page-header">
                            <h1 id="<?php echo $nav['catering']; ?>"><?= htmlspecialchars($nav['catering']) ?></h1>
                        </div>
                    </div>
					<?php endif; ?>
					<!-- Nachrichtenbereich über die gesamte Breite -->
					<div class="col-12">
						<div id="message" class="mt-3" style="display: none;"></div>
					</div>
					
					<!-- Zwei-Spalten-Layout -->
					<div class="col-lg-6 col-md-6">
						<!-- Bestellformular -->
						<h2><?= htmlspecialchars($catering['new_order']) ?></h2>
						<form id="orderForm">
							<div class="form-group">
								<label><?= htmlspecialchars($catering['name']) ?>:</label>
								<input type="text" class="form-control" name="customer_name" required>
							</div>
							<div class="form-group">
								<label><?= htmlspecialchars($catering['flyer_number']) ?>:</label>
								<input type="number" class="form-control" name="flyer_number" required>
							</div>
							<div class="form-group">
								<label><?= htmlspecialchars($catering['quantity']) ?>:</label>
								<input type="number" class="form-control" name="quantity" min="1" value="1" required>
							</div>
							<div class="form-group">
								<label><?= htmlspecialchars($catering['size']) ?>:</label>
								<select class="form-control" name="size" required>
									<option value="normal"><?= htmlspecialchars($catering['size_normal']) ?></option>
									<option value="small"><?= htmlspecialchars($catering['size_small']) ?></option>
									<option value="big"><?= htmlspecialchars($catering['size_big']) ?></option>
								</select>
							</div>
							<div class="form-group">
								<label><?= htmlspecialchars($catering['comments']) ?>:</label>
								<textarea class="form-control" name="notes"></textarea>
							</div>
							<div class="form-group">
								<label><?= htmlspecialchars($catering['price']) ?>:</label>
								<input type="number" class="form-control" name="price" step="0.01" required>
							</div>
							<button type="submit" class="btn btn-success"><?= htmlspecialchars($catering['order_button']) ?></button>
						</form>
					</div>
					
					<!-- Bestellübersicht -->
					<div class="col-lg-6 col-md-6">
						<h2><?= htmlspecialchars($catering['current_orders']) ?></h2>
						<div style="overflow-x: auto;">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th><?= htmlspecialchars($catering['time']) ?></th>
										<th><?= htmlspecialchars($catering['name']) ?></th>
										<th><?= htmlspecialchars($catering['flyer_number']) ?></th>
										<th><?= htmlspecialchars($catering['size']) ?></th>
										<th><?= htmlspecialchars($catering['price']) ?></th>
										<th><?= htmlspecialchars($catering['status']) ?></th>
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
									$size='size_'.$row['size'];
									$status = 'status_'.$row['status'];
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
										<td><?= htmlspecialchars($catering[$size]) ?></td>
										<td>
											<?= number_format($row['price'], 2) ?> <?= ($catering['currency']) ?>
											<?php if ($row['quantity'] > 1): ?>
												<br>
												<small><?= htmlspecialchars($catering['total']) ?>: <?= number_format($orderTotal, 2) ?> <?= ($catering['currency']) ?></small>
											<?php endif; ?>
										</td>
										<td>
											<span class="badge <?= $row['status'] === 'new' ? 'badge-light' : ($row['status'] === 'processing' ? 'badge-warning' : 'badge-success') ?>">
												<?= htmlspecialchars($catering[$status]) ?>
											</span>
										</td>
									</tr>
								<?php endwhile; ?>

								<!-- Zusammenfassung -->
									<tr class="font-weight-bold bg-light">
										<td colspan="4"><?= htmlspecialchars($catering['total_today']) ?></td>
										<td><?= number_format($totalRevenue, 2) ?> <?= ($catering['currency']) ?></td>
										<td><?= $totalOrders ?> <?= htmlspecialchars($catering['orders_count']) ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<!-- Legende -->
						<div class="mt-3" style="font-size: 0.9em;">
							<p><?= htmlspecialchars($catering['status_legend']) ?>:</p>
							<span class="badge badge-light"><?= htmlspecialchars($catering['status_new']) ?></span>
							<span class="badge badge-warning"><?= htmlspecialchars($catering['status_processing']) ?></span>
							<span class="badge badge-success"><?= htmlspecialchars($catering['status_completed']) ?></span>
						</div>
						
						<?php
						$activeFlyer = $db->query('SELECT * FROM flyers WHERE is_active = 1 LIMIT 1')
										  ->fetchArray(SQLITE3_ASSOC);
						if ($activeFlyer): 
						?>						
						<div class="mt-3">
							<br />
							<a href="flyer/<?= htmlspecialchars($activeFlyer['filename']) ?>" 
							   target="_blank" class="btn btn-primary">
							   <?= sprintf(htmlspecialchars($catering['view_flyer']), htmlspecialchars($activeFlyer['name'])) ?>
							</a>
						</div>
						<?php endif; ?>
						<div class="mt-3">
							<br />
							<a href="admin.php" class="btn btn-secondary">
								<?= htmlspecialchars($catering['admin_panel']) ?>
							</a>
						</div>						
					</div>
				</div>
			</div>

    <script src="../assets/jquery.js"></script>
    <script src="../assets/bootstrap.js"></script>
    <script>
		// JavaScript-Übersetzungen aus PHP-Variablen
		const translations = {
			errorOccurred: '<?= htmlspecialchars($catering['error_occurred']) ?>',
			totalSum: '<?= htmlspecialchars($catering['total_sum']) ?>',
			error: '<?= htmlspecialchars($catering['error']) ?>',
			currency: '<?= html_entity_decode($catering['currency']) ?>',
			quantityTimes: '<?= htmlspecialchars($catering['quantity_times']) ?>',
			status_new: '<?= htmlspecialchars($catering['status_new']) ?>',
    		status_processing: '<?= htmlspecialchars($catering['status_processing']) ?>',
    		status_completed: '<?= htmlspecialchars($catering['status_completed']) ?>',
			'size_normal': '<?= htmlspecialchars($catering['size_normal']) ?>',
			'size_small': '<?= htmlspecialchars($catering['size_small']) ?>',
			'size_big': '<?= htmlspecialchars($catering['size_big']) ?>',
			totalSummary: '<?= htmlspecialchars($catering['total_summary']) ?>',
			orders: '<?= htmlspecialchars($catering['orders']) ?>'
		};

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
					updateOrdersTable();
				} else {
					messageDiv.className = 'alert alert-danger';
					messageDiv.textContent = data.message;
				}
				
				setTimeout(() => {
					messageDiv.style.display = 'none';
				}, 5000);
			})
			.catch(error => {
				console.error(translations.error + ':', error);
				const messageDiv = document.getElementById('message');
				messageDiv.style.display = 'block';
				messageDiv.className = 'alert alert-danger';
				messageDiv.textContent = translations.errorOccurred;
			});
		});

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
						const badgeClass = order.status === "new" ? 'badge-light' : 
										 (order.status === "processing" ? 'badge-warning' : 'badge-success');

						html += `
							<tr>
								<td>${orderTime}</td>
								<td>${escapeHtml(order.customer_name)}</td>
								<td>
									${escapeHtml(order.flyer_number)}
									${order.quantity > 1 ? `(${order.quantity}${translations.quantityTimes})` : ''}
								</td>
								<td>${translations['size_' + order.size]}</td>
								<td>
									${formatPrice(order.price)} ${translations.currency}
									${order.quantity > 1 ? `<br><small>${translations.totalSum}: ${formatPrice(orderTotal)} ${translations.currency}</small>` : ''}
								</td>
								<td>
									<span class="badge ${badgeClass}">
										${translations['status_' + order.status]}
									</span>
								</td>
							</tr>
						`;
					});

					html += `
						<tr class="font-weight-bold bg-light">
							<td colspan="4">${translations.totalSummary}:</td>
							<td>${formatPrice(data.totalRevenue)} ${translations.currency}</td>
							<td>${data.totalOrders} ${translations.orders}</td>
						</tr>
					`;

					tableBody.innerHTML = html;
				})
				.catch(error => console.error(translations.error + ':', error));
		}

		function formatPrice(price) {
			return new Intl.NumberFormat('de-DE', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			}).format(price);
		}

		function escapeHtml(unsafe) {
			return unsafe
				.toString()
				.replace(/&/g, "&amp;")
				.replace(/</g, "&lt;")
				.replace(/>/g, "&gt;")
				.replace(/"/g, "&quot;")
				.replace(/'/g, "&#039;");
		}

		setInterval(updateOrdersTable, 20000); // 20Sec

		function calculateTotalPrice() {
			const price = parseFloat(document.querySelector('input[name="price"]').value) || 0;
			const quantity = parseInt(document.querySelector('input[name="quantity"]').value) || 1;
			const total = (price * quantity).toFixed(2);
			
			let totalDisplay = document.getElementById('totalPrice');
			if (!totalDisplay) {
				totalDisplay = document.createElement('div');
				totalDisplay.id = 'totalPrice';
				totalDisplay.style.marginTop = '10px';
				totalDisplay.style.fontWeight = 'bold';
				document.querySelector('button[type="submit"]').parentNode.appendChild(totalDisplay);
			}
			totalDisplay.textContent = `${translations.totalSum}: ${total} ${translations.currency}`;
		}

		document.querySelector('input[name="quantity"]').addEventListener('change', calculateTotalPrice);
		document.querySelector('input[name="price"]').addEventListener('change', calculateTotalPrice);
	</script>
</body>
</html>
