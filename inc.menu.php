  <div id="sidebar-wrapper">
      <ul class="sidebar-nav">
	      <br />
		  <li class="sidebar-brand">
	      <?php 
			if (file_exists($logo)) { echo '<a href="'.$_SERVER['PHP_SELF'].'"><img class="logo" src="'.$logo.'"></a>'; };
		  ?>
		  </li>
		  
          <li class="sidebar-brand">
              <a href="<?php $_SERVER['PHP_SELF']; ?>"><?php if (!isset($lan_title) & (!file_exists($logo))) { echo "LAN Homepage"; } elseif (file_exists($logo)) {;} else { echo $lan_title; } ?></a>
          <li>
              <a href="<?php $_SERVER['PHP_SELF']; ?>#<?php echo $nav['home']; ?>"><?php echo $nav['home']; ?><span class="glyphicon glyphicon-asterisk"></span></a>
          </li>
          <li>
              <?php if ($enable_chat == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#'.$nav['chat'].'">'.$nav['chat'].'<span class="glyphicon glyphicon-fire"></span></a>';
        } ?>
          </li>
          <li>
              <?php if ($enable_stats == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#'.$nav['stats'].'">'.$nav['stats'].'<span class="glyphicon glyphicon-stats"></span></a>';
        } ?>
          </li>
          <li>
              <?php if ($enable_downloads == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#'.$nav['downloads'].'">'.$nav['downloads'].'<span class="glyphicon glyphicon-circle-arrow-down"></span></a>';
        } ?>
          </li>
          <li>
              <?php if ($enable_serverlist == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#'.$nav['serverlist'].'">'.$nav['serverlist'].'<span class="glyphicon glyphicon-tasks"></span></a>';
        } ?>
          </li>
          <li>
              <?php if ($enable_competition == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#'.$nav['competition'].'">'.$nav['competition'].'<span class="glyphicon glyphicon-knight"></span></a>';
        } ?>
          </li>
		  <li>
              <?php if ($enable_catering == true) {
	      echo '<a href="' . $_SERVER['PHP_SELF'] . '#'.$nav['catering'].'">'.$nav['catering'].'<span class="glyphicon glyphicon-cutlery"></span></a>';
        } ?>
          </li>
		  <li>
              <?php if ($enable_faq == true) {
          echo '<a href="' . $_SERVER['PHP_SELF'] . '#'.$nav['faq'].'">'.$nav['faq'].'<span class="glyphicon glyphicon-question-sign"></span></a>';
        } ?>
          </li>
      </ul>

      <div id="sidebar-footer">
          LANPage
		  <br />
          <small>powered by <a href="https://www.eti-lan.xyz">eti-lan.xyz</a></small>
      </div>
  </div>