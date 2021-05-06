            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-9">
                        <div class="page-header">
                            <h1 id="Downloads"><?php echo $downloads['title']; ?></h1>
                        </div>
                    </div>
                    <div class="col col-md-2">
                        <div class="thumbnail">
                            <div class="caption">
                                <h3>LAN Launcher</h3>
                                <img style="max-height:100px; padding-bottom:10px;" src="assets/lan_launcher.png" />
                                <p><?php echo $downloads['launcher_text']; ?></p>
                                <p><a href="dl/launcher-setup.exe" class="btn btn-warning"
                                        role="button"><?php echo $downloads['download']; ?></a></p>
                            </div>
                        </div>
                    </div>

                    <?php

                    if (file_exists('dl.xml')) {
                        $sXml = file_get_contents('dl.xml');
                    } else if (file_exists('dl.sample.xml')) {
                        $sXml = file_get_contents('dl.sample.xml');
                    } else {
                        die;
                    }

                    $oXML = simplexml_load_string($sXml);
                    if (!$oXML) {
                        die($downloads['error']);
                    }

                    foreach ($oXML->download as $download) {
                        foreach ($download->name as $name) {
                            print '<div class="col col-md-2"><div class="thumbnail"><div class="caption"><h3>' . (string) $name . '</h3>';
                        }
                        foreach ($download->image as $image) {
                            print '<img style="max-width:220px; max-height:100px; padding-bottom:10px;" src="' . (string) $image . '"/>';
                        }
                        foreach ($download->version as $version) {
                            print '<p>' . (string) $version . '</p>';
                        }
                        foreach ($download->file as $file) {
                            print '<p><a href="' . (string) $file . '" class="btn btn-warning" role="button">' . $downloads['download'] . '</a></p></div></div></div>';
                        }
                    }
                    ?>

                </div>
            </div>