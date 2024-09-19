<div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<?php 
				$headers = ['name', 'game', 'ip', 'port', 'admin', 'status'];
				foreach ($headers as $header): ?>
					<th><?php echo htmlspecialchars($servers[$header]); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			$serverFile = file_exists('servers.xml') ? 'servers.xml' : (file_exists('servers.sample.xml') ? 'servers.sample.xml' : null);
			if (!$serverFile) {
				die;
			}

			$sXml = file_get_contents($serverFile);
			$oXML = simplexml_load_string($sXml);
			if (!$oXML) {
				die(htmlspecialchars($servers['error']));
			}

			foreach ($oXML->server as $server) {
				$serverData = [
					'name' => (string) $server->name,
					'game' => (string) $server->game,
					'ip' => (string) $server->ip,
					'port' => (string) $server->port,
					'admin' => (string) $server->admin,
				];

				echo '<tr>';
				foreach ($serverData as $data) {
					echo '<td>' . htmlspecialchars($data) . '</td>';
				}

				$timeout = 1;
				$status = @fsockopen($serverData['ip'], $serverData['port'], $errno, $errstr, $timeout) ? 'online' : 'offline';
				echo '<td class="' . ($status === 'online' ? 'success' : 'danger') . '">' . htmlspecialchars($servers[$status]) . '</td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>
</div>
