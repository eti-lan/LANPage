<?php
/**
 * jQuery Bracket server backend
 *
 * Copyright (c) 2012, Teijo Laine,
 * http://aropupu.fi/bracket-server/
 *
 * Licenced under the MIT licence
 * 
 * Copyright (c) 2020, fly
 */
?>
<script type="text/javascript">
jQuery.noConflict();
var $j = jQuery;

var singleEleminationData = { teams: [], results: [[]]};
var doubleEleminationData = { teams: [], results: [[[[]]],[],[]]};

function refreshSelect(pick) {
  var select = $j('#bracketSelect').empty()
  $j.getJSON('/competition/rest.php?op=list', function(data) {

    $j.each(data, function(i, e) {
      select.append('<option value="'+e+'">'+e+'</option>')
	  select.change()
    })
  }).success(function() {
    if (pick) {
      select.find(':selected').removeAttr('seleceted')
      select.find('option[value="'+pick+'"]').attr('selected','selected')
      select.change()
    }
  })
}

function hash() {
  var bracket = null
  var parts = window.location.href.replace(/#!([a-zA-Z0-9_]+)$/gi, function(m, match) {
    bracket = match
  });
  return bracket;
}

$j(document).ready()
$j(document).ready(function() {
    refreshSelect(hash())

    $j('#bracketSelect').change(function() {
      var value = $j(this).val()
      location.hash = '#!'+value
      if (!value) {
        newBracket()
        return
      }
      $j('#fields').empty()

      $j.getJSON('/competition/rest.php?op=get&id='+value, function(data) {
        $j('#editor').empty().bracket({
            init: data,
            save: function(data){
                var json = jQuery.toJSON(data)
                $j.getJSON('/competition/rest.php?op=set&id='+value+'&data='+json)
              },
			disableToolbar:true,
			disableTeamEdit:true
          })
      }).error(function() { })
    })
	
  })
</script>
			<div class="container-fluid">
                <div class="row">
                    <div class="col-lg-10 col-md-10">
                        <div class="page-header">
                            <h1 id="<?php echo $nav['competition']; ?>"><?php echo $nav['competition']; ?></h1>
                        </div>
                    </div>
					<div class="col-lg-10 col-md-10 competition">
						<p>Pick a Competition: <select id="bracketSelect"></select> <a href="/competition/edit.php">[Edit]</a></p>
						<div id="main">
							<div id="editor"></div>
							<div style="clear: both;" id="fields"></div>
							<pre></pre>
						</div>
					</div>
				</div>
			</div>