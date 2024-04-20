            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-10 col-md-10">
                        <div class="page-header">
                            <h1 id="<?php echo $nav['stats']; ?>"><?php echo $stats['title']; ?></h1>
							<h4 id="<?php echo $stats['topgames']; ?>"><?php echo $stats['topgames']; ?></h4>
                        </div>
                    </div>

                    <?php
					$player_overview_query = "SELECT rowid, * FROM assets";
					$player_overview = $stats_db->query($player_overview_query);
					$game_overview_query = "SELECT game_id, SUM(counter) FROM gamestats GROUP BY game_id ORDER BY -SUM(counter) LIMIT " . $stats_show_topgames;
					$game_overview = $stats_db->query($game_overview_query);
					?>

                    <?php $top_game = $stats_db->querySingle('SELECT SUM(counter) FROM gamestats ORDER BY game_id');
					?>


                    <div class="col-lg-10 col-md-10 top-games">
                        <?php while ($row = $game_overview->fetchArray()) {
							$percent = round(($row['SUM(counter)'] / $top_game * 100), 0); 
							$game_title = $game_db->querySingle('SELECT game_title FROM games WHERE game_id = "'.($row['game_id']).'"');
							?>
                        <?php echo '<div class="col col-md-4">
							<div class="row"><div class="col-xs-2"><img style="width: 75px; border-radius: 4px; margin: 0px 10px 15px 0px;" src="assets/games/' . $row['game_id'] . '.jpg"></div>
    						<div class="col-xs-8" style="padding-left:35px;">
      						<div class="progress"><div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' . $row['SUM(counter)'] . '" aria-valuemin="0" aria-valuemax="' . $top_game . '" style="width:'.$percent.'%; padding-left: 15px;">' . $percent . '%</div>
    						</div>
							<div class="stats_game_title">'. $game_title .'</div>
							</div>
							</div>
							</div>'; ?>
                        <?php } ?>
                    </div>

                    <div class="col-lg-10 col-md-10">
						<div class="page-header">
							<h4 id="<?php $stats['players']; ?>"><?php echo $lan_title .' '. $stats['players']; ?></h4>
						 </div>
							<table id="stats" class="table table-striped table-bordered table_morecondensed" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><?php echo $stats['player']; ?></th>
                                    <th><?php echo $stats['hostname']; ?></th>
                                    <th><?php echo $stats['ip']; ?></th>
                                    <th><?php echo $stats['board_manufacturer']; ?></th>
                                    <th><?php echo $stats['baseboard']; ?></th>
                                    <th><?php echo $stats['cpu']; ?></th>
									<th><?php echo $stats['gpu']; ?></th>
                                    <th><?php echo $stats['os']; ?></th>
                                    <th class="col-xs-1"><?php echo $stats['last_game']; ?></th>
                                    <th><?php echo $stats['status']; ?></th>
                                </tr>
                            </thead>
                            <tbody>

							<?php while ($row = $player_overview->fetchArray()) {
								if ($row['timestamp'] >= strtotime(date('Y-m-d H:i:s', strtotime("-" . $stats_expire . " hours")))) { ?>
                                <tr>
                                    <td><?php echo $row['player_name']; ?></td>
                                    <td><?php echo $row['hostname']; ?></td>
                                    <td><?php echo $row['ipv4addr']; ?></td>
                                    <td><?php echo $row['board_manufacturer']; ?></td>
                                    <td><?php echo $row['baseboard']; ?></td>
                                    <td><?php echo $row['cpu']; ?></td>
									<td><?php echo $row['gpu']; ?></td>
                                    <td><?php echo $row['windows_edition']; ?></td>
                                    <td class="stats_game" id="<?php echo $row['current_game']; ?>"><?php if (!empty($row['current_game'])) {
																												echo '<img class="stats_game" src="assets/games/' . $row['current_game'] . '.jpg">';
																											} ?></td>
                                    <?php if ($row['timestamp'] >= strtotime(date('Y-m-d H:i:s', strtotime("-" . $stats_playerstatus_timespan . " minutes"))) /* or !empty($row['current_game'])*/)  {
												echo '<td class="success"></td>';
											} else {
												echo '<td class="danger"></td>';
									}; ?>
                                </tr>
							<?php };
							} ?>
                            </tbody>
                        </table>
                    </div>