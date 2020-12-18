<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
 
 // get $competition_edit_password
 include_once("../config.php");
 $delete_token=md5($competition_edit_password);
 
 if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Competition Authentication"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'No access without P@ssw0rd.';
    exit;
} else {
    if($_SERVER['PHP_AUTH_PW']!=$competition_edit_password) exit; // user doesn't matter!
}
?>
<html>
<head>
<title>Competitions</title>
<script type="text/javascript" src="../assets/jquery.js"></script>
<script type="text/javascript" src="../assets/jquery.json.js"></script>
<script type="text/javascript" src="../assets/jquery.bracket.min.js"></script>
<link rel="stylesheet" type="text/css" href="../assets/jquery.bracket.min.css" />
<style type="text/css">
.empty {
  background-color: #FCC;
}
.invalid {
  background-color: #FC6;
}
</style>
<script type="text/javascript">
function newFields() {
  $('#delete').hide();
  return 'Bracket name [a-zA-Z0-9_] <input type="text" id="bracketId" class="empty" /><input type="submit" value="Create" disabled />'
}

/* Default Save-Data */
var singleEleminationData = { teams: [], results: [[]]};
var doubleEleminationData = { teams: [], results: [[[[]]],[],[]]};

/* Called whenever bracket is modified
 *
 * data:     changed bracket object in format given to init
 * userData: optional data given when bracket is created.
 */
function saveFn(data, userData) {
  //var json = jQuery.toJSON(data)
  //$('pre').text(jQuery.toJSON(data))
  /* You probably want to do something like this
  jQuery.ajax("rest/"+userData, {contentType: 'application/json',
                                dataType: 'json',
                                type: 'post',
                                data: json})
  */
}

function newBracket() {
  $('#editor').empty().bracket({
	  init: singleEleminationData,
	  save: saveFn
      })
  $('#fields').html(newFields())
}

function refreshSelect(pick) {
  var select = $('#bracketSelect').empty()
  $('<option value="">New competition</option>').appendTo(select)
  $.getJSON('/competition/rest.php?op=list', function(data) {

    $.each(data, function(i, e) {
      select.append('<option value="'+e+'">'+e+'</option>')
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

$(document).ready(newBracket)
$(document).ready(function() {
    newBracket()
    $('input#bracketId').on('keyup', function() {
      var input = $(this)
      var submit = $('input[value="Create"]')
      if (input.val().length === 0) {
        input.removeClass('invalid')
        input.addClass('empty')
        submit.attr('disabled', 'disabled')
      }
      else if (input.val().match(/[^0-9A-Za-z_]+/)) {
        input.addClass('invalid')
        submit.attr('disabled', 'disabled')
      }
      else {
        input.removeClass('empty invalid')
        submit.removeAttr('disabled')
      }
    })

    $('input[value="Create"]').on('click', function() {
      $(this).attr('disabled', 'disabled')
      var input = $('input#bracketId')
      var bracketId = input.val()

      if (bracketId.match(/[^0-9A-Za-z_]+/))
        return

      var data = $('#editor').bracket('data')
      var json = jQuery.toJSON(data)
      $.getJSON('/competition/rest.php?op=set&id='+bracketId+'&data='+json)
        .success(function() {
          refreshSelect(bracketId);
		  $('pre').text("Bracket created: "+bracketId);
        })
    })

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
		$('pre').text("Bracket loaded: "+value);
        $('#editor').empty().bracket({
            init: data,
            save: function(data){
                var json = jQuery.toJSON(data)
                //$('pre').text(jQuery.toJSON(data))
                $.getJSON('rest.php?op=set&id='+value+'&data='+json)
              }
          })
      }).error(function() { })
	  
	  $('#delete').show();
    })
	
	$('#delete').click(function() {
		var value = $('#bracketSelect').val();
		$('pre').text("Bracket deleted: "+value);
		$.getJSON('/competition/rest.php?op=delete&id='+value+'&token=<?php echo($delete_token);?>').success(function() {
          refreshSelect(hash())
        })
	})
  })
</script>
</head>
<body>
<h1>Competitions</h1>
<p>Pick a Competition: <select id="bracketSelect"></select></p>
<div id="main">
<div id="editor"></div>
<div style="clear: both;" id="fields"></div>
<pre></pre>
<div id="delete" style="color:red; background-color:DDD;display:inline;">[Delete this Competition]</div>
<div><p><a href="../">Back to LanPage</a></p></div>
</body>
</html>