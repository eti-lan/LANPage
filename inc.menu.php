  <div id="sidebar-wrapper">
      <ul class="sidebar-nav">
	      <br />
		  <li class="sidebar-brand">
	      <?php 
			$logo = "./logo.jpg";
			if (file_exists($logo)) { echo '<a href="'.$_SERVER['PHP_SELF'].'"><img src="logo.jpg"></a>'; };
		  ?>
		  </li>
		  
          <li class="sidebar-brand">
              <a href="<?php $_SERVER['PHP_SELF']; ?>"><?php if (!isset($lan_title)) { echo "LAN Homepage"; } else { echo $lan_title; } ?></a>
          <li>
              <a href="<?php $_SERVER['PHP_SELF']; ?>#News"><?php echo $nav['news']; ?> <span
                      class="glyphicon glyphicon-asterisk"></span></a>
          </li>
          <li>
              <?php if ($enable_chat == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#Chat">'.$nav['chat'].'<span class="glyphicon glyphicon-fire"></span></a>';
        } ?>
          </li>
          <li>
              <?php if ($enable_stats == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#Stats">'.$nav['stats'].'<span class="glyphicon glyphicon-stats"></span></a>';
        } ?>
          </li>
          <li>
              <?php if ($enable_downloads == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#Downloads">'.$nav['downloads'].'<span class="glyphicon glyphicon-circle-arrow-down"></span></a>';
        } ?>
          </li>
          <li>
              <?php if ($enable_serverlist == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#Serverlist">'.$nav['serverlist'].'<span class="glyphicon glyphicon-tasks"></span></a>';
        } ?>
          </li>
          <li>
              <?php if ($enable_competition == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#Competition">'.$nav['competition'].'<span class="glyphicon glyphicon-knight"></span></a>';
        } ?>
          </li>
      </ul>

      <div id="sidebar-footer">
          LANPage
		  <br />
          <small>powered by <a href="https://www.eti-lan.xyz">eti-lan.xyz</a></small>
      </div>
  </div>