<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th><?php echo $servers['name']; ?></th>
                <th><?php echo $servers['game']; ?></th>
                <th><?php echo $servers['ip']; ?></th>
                <th><?php echo $servers['port']; ?></th>
                <th><?php echo $servers['admin']; ?></th>
                <th><?php echo $servers['status']; ?></th>
            </tr>
        </thead>
        <tbody>

            <?php
			$server = "";
			if (file_exists('servers.xml')) {
				$sXml = file_get_contents('servers.xml');
			} else if (file_exists('servers.sample.xml')) {
				$sXml = file_get_contents('servers.sample.xml');
			} else {
				die;
			}

			// parse XML  
			$oXML = simplexml_load_string($sXml);
			if (!$oXML) {
				die($servers['error']);
			}

			foreach ($oXML->server as $server) {
				foreach ($server->name as $name) {
					print '<tr><td>' . (string) $name . '</td>';
				}
				foreach ($server->game as $game) {
					print '<td>' . (string) $game . '</td>';
				}
				foreach ($server->ip as $ip) {
					print '<td>' . (string) $ip . '</td>';
				}
				foreach ($server->port as $port) {
					print '<td>' . (string) $port . '</td>';
				}
				foreach ($server->admin as $admin) {
					print '<td>' . (string) $admin . '</td>';
				}
				foreach ($server->ip as $ip) {
					$timeout = 1;
					$port = (string) $oXML->server->port;
					error_reporting(0);
					if (fsockopen($ip, $port, $errno, $errstr, 1)) {
						print '<td class="success">'.$servers['online'].'</td></tr>';
					} else {
						print '<td class="danger">'.$servers['offline'].'</td></tr>';
					}
				}
			}
			?>

        </tbody>
    </table>
</div>