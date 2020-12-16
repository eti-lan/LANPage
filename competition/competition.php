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
<script type="text/javascript" src="/competition/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/competition/jquery.json-2.2.min.js"></script>
<script type="text/javascript" src="/competition/jquery.bracket.min.js"></script>
<link rel="stylesheet" type="text/css" href="/competition/jquery.bracket.mainpage.css" />
<style type="text/css">
.empty {
  background-color: #FCC;
}
.invalid {
  background-color: #FC6;
}
</style>
<script type="text/javascript">
var singleEleminationData = { teams: [], results: [[]]};
var doubleEleminationData = { teams: [], results: [[[[]]],[],[]]};

function refreshSelect(pick) {
  var select = $('#bracketSelect').empty()
  $.getJSON('/competition/rest.php?op=list', function(data) {

    $.each(data, function(i, e) {
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

$(document).ready()
$(document).ready(function() {
    refreshSelect(hash())

    $('#bracketSelect').change(function() {
      var value = $(this).val()
      location.hash = '#!'+value
      if (!value) {
        newBracket()
        return
      }
      $('#fields').empty()

      $.getJSON('/competition/rest.php?op=get&id='+value, function(data) {
        $('#editor').empty().bracket({
            init: data,
            save: function(data){
                var json = jQuery.toJSON(data)
                $.getJSON('/competition/rest.php?op=set&id='+value+'&data='+json)
              },
			disableToolbar:true,
			disableTeamEdit:true
          })
      }).error(function() { })
    })
	
  })
</script>
<h1 id="Competition">Competitions</h1>
<p>Pick a Competition: <select id="bracketSelect"></select> <a href="/competition/edit.php">[Edit]</a></p>
<div id="main">
<div id="editor"></div>
<div style="clear: both;" id="fields"></div>
<pre></pre>