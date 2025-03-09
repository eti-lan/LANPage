<?php
/**
 * LANPage - A simple website template for your LAN Party that integrates with LAN Launcher
 *
 * @package  lanpage
 * @author   eti and various anonymous
 *
 * things required:
 * Webserver and PHP7 or newer with SQLite extension enabled
 */

include("inc.head.php");
?>

<body>
    <div id="wrapper">
        <?php include("inc.menu.php"); ?>

        <div id="page-content-wrapper">

            <!-- Start -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header">
                            <h1 id="<?php echo $nav['home']; ?>"><?php echo($start['welcome']); ?>
                                <?php if (isset($lan_title)) { echo "<small>" . $start['welcome2'] . " " . $lan_title . "</small>"; } ?>
                            </h1>
                        </div>

                        <div class="jumbotron">
                            <h1>
                                <?php echo $_SERVER['REMOTE_ADDR']; ?>
                            </h1>

                            <p><?php echo($start['iptext']); ?>
                                <?php if (isset($lan_title)) { echo $lan_title; } ?> <?php echo($start['iptext2']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Downloads -->
            <?php if ($enable_downloads == true) { include("inc.dl.php"); } ?>
			
            <!-- Chat -->
            <?php if ($enable_chat == true) { include("chat/chat.php"); } ?>

            <!-- Stats -->
            <?php if ($enable_stats == true) { include("inc.stats.php"); } ?>

            <!-- Servers -->
            <?php if ($enable_serverlist == true) { 
				echo "<div class=\"container-fluid\">\n";
				echo "                <div class=\"row\">\n";
				echo "                    <div class=\"col-12 col-md-10\">\n";
				echo "                        <div class=\"page-header\">\n";
				echo "                            <h1 id=\"" . $nav['serverlist'] . "\"> " . $nav['serverlist'] . " </h1>\n";
				echo "                        </div>\n";
				echo "                        <div id=\"serverlist\">";
				require("inc.servers.php");
				echo "						  </div>\n";
				echo "                    </div>\n";
				echo "                </div>\n";
				echo "</div>\n";
			};
			?>

            <!-- Competition -->
            <?php if ($enable_competition == true) { include("inc.competition.php"); }; ?>
			
			<!-- FAQ -->
            <?php if ($enable_faq == true) { include("inc.faq.php"); } ?>

        </div>
		
		<footer>
			<div class="col-lg-10">
				<div class="row">
					<hr>
					<p class="pull-right">&copy; <?php echo date("Y"); ?> eti Team</p>
				</div>
			</div>
		</footer>
		
	 </div>
</body>
</html>