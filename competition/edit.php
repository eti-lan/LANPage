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
 
 // get $competition_edit_password
if (file_exists(stream_resolve_include_path('../config.php'))) {
	include_once('../config.php');
} else if (file_exists(stream_resolve_include_path('../config.sample.php'))) {
	include_once('../config.sample.php');
} else {
	die;
}

 $delete_token=md5($competition_edit_password);
 
 if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Competition Authentication"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'No access without Password.';
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
<script type="text/javascript" src="../assets/jquery.bracket.js"></script>

<style type="text/css">
/* jQuery Bracket | Copyright (c) Teijo Laine 2011-2018 | Licenced under the MIT licence */
div.jQBracket{font-family:Arial;font-size:14px;position:relative}div.jQBracket .tools{position:absolute;top:0;color:#fff}div.jQBracket .tools span{cursor:pointer;margin:5px;display:block;text-align:center;width:18px;height:18px;background-color:#666}div.jQBracket .tools span:hover{background-color:#999}div.jQBracket .finals{float:right;right:0;clear:right;position:relative}div.jQBracket .bracket{float:right;clear:left}div.jQBracket .loserBracket{float:right;clear:left;position:relative}div.jQBracket .round{position:relative;float:left}div.jQBracket .match{position:relative}div.jQBracket .editable{cursor:pointer}div.jQBracket .team{position:relative;z-index:1;float:left;background-color:#eee;cursor:default}div.jQBracket .team:first-child{border-bottom:1px solid #999}div.jQBracket .team input{font-size:14px;padding:0;width:100%;border:0;margin:0;outline:0}div.jQBracket .team div.label{padding:3px;position:absolute;height:22px;white-space:nowrap;overflow:hidden;box-sizing:border-box}div.jQBracket .team div.label[disabled]{cursor:default}div.jQBracket .team div.score{float:right;padding:3px;background-color:rgba(255,255,255,.3);text-align:center;box-sizing:border-box}div.jQBracket .team div.score input{text-align:center}div.jQBracket .team div.score[disabled]{color:#999;cursor:default}div.jQBracket .team div.label input.error,div.jQBracket .team div.score input.error{background-color:#fcc}div.jQBracket .team.np{background-color:#666;color:#eee}div.jQBracket .team.na{background-color:#999;color:#ccc}div.jQBracket .team.win{color:#333}div.jQBracket .team.win div.score{color:#060}div.jQBracket .team.lose div.score{color:#900}div.jQBracket .team.lose{background-color:#ddd;color:#999}div.jQBracket .team.tie div.score{color:#00f}div.jQBracket .team.highlightWinner{background-color:#da0;color:#000}div.jQBracket .team.highlightLoser{background-color:#ccc;color:#000}div.jQBracket .team.highlight{background-color:#3c0;color:#000}div.jQBracket .team.bye{background-color:#999;color:#ccc}div.jQBracket .teamContainer{z-index:1;position:relative;float:left}div.jQBracket .connector{border:2px solid #666;border-left-style:none;position:absolute;z-index:1}div.jQBracket .connector div.connector{border:0;border-bottom:2px solid #666;height:0;position:absolute}div.jQBracket .connector.highlightWinner,div.jQBracket .connector div.connector.highlightWinner{border-color:#da0}div.jQBracket .connector.highlightLoser,div.jQBracket .connector div.connector.highlightLoser{border-color:#ccc}div.jQBracket .connector.highlight,div.jQBracket .connector div.connector.highlight{border-color:#0c0}div.jQBracket .np .connector,div.jQBracket .np .connector div.connector{border-color:#999}div.jQBracket .bubble{height:22px;line-height:22px;width:30px;right:-35px;position:absolute;text-align:center;font-size:11px}div.jQBracket .bubble:after{content:"";position:absolute;top:6px;width:0;height:0;border-top:5px solid transparent;border-left:5px solid transparent;border-right:5px solid transparent;border-bottom:5px solid transparent}div.jQBracket .bubble:after{left:-5px;border-left:0}div.jQBracket .win .bubble{background-color:#da0;color:#960}div.jQBracket .win .bubble:after{border-right-color:#da0}div.jQBracket .win .bubble.third{background-color:#963;color:#d95}div.jQBracket .win .bubble.third:after{border-right:6px solid #963}div.jQBracket .lose .bubble{background-color:#ccc;color:#333}div.jQBracket .lose .bubble:after{border-right-color:#ccc}div.jQBracket .lose .bubble.fourth{background-color:#678;color:#ccd}div.jQBracket .lose .bubble.fourth:after{border-right:6px solid #678}div.jQBracket.rl .finals{float:left;left:0;clear:left}div.jQBracket.rl .bracket{float:left;clear:right}div.jQBracket.rl .loserBracket{float:left;clear:right}div.jQBracket.rl .round{margin-right:0;float:right}div.jQBracket.rl .team{float:right}div.jQBracket.rl .team div.label{right:0}div.jQBracket.rl .team div.score{float:left}div.jQBracket.rl .teamContainer{float:right}div.jQBracket.rl .connector{border-left-style:solid;border-right-style:none;border-width:2px}div.jQBracket.rl .connector.highlightWinner,div.jQBracket.rl .connector div.connector.highlightWinner{border-color:#da0}div.jQBracket.rl .connector.highlightLoser,div.jQBracket.rl .connector div.connector.highlightLoser{border-color:#ccc}div.jQBracket.rl .connector.highlight,div.jQBracket.rl .connector div.connector.highlight{border-color:#0c0}div.jQBracket.rl .bubble{left:-35px}div.jQBracket.rl .bubble.third{background-color:#963;color:#310}div.jQBracket.rl .bubble.fourth{background-color:#678;color:#ccd}div.jQBracket.rl .bubble:after{left:auto;right:-5px;border-left:5px solid transparent;border-right:0}div.jQBracket.rl .bubble.third:after{border-right:0;border-left:6px solid #963}div.jQBracket.rl .bubble.fourth:after{border-right:0;border-left:6px solid #678}div.jQBracket.rl .highlightWinner .bubble:after{border-left-color:#da0}div.jQBracket.rl .highlightLoser .bubble:after{border-left-color:#ccc}
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
  $.getJSON('rest.php?op=list', function(data) {

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
      $.getJSON('rest.php?op=set&id='+bracketId+'&data='+json)
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

      $.getJSON('rest.php?op=get&id='+value, function(data) {
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
		$.getJSON('rest.php?op=delete&id='+value+'&token=<?php echo($delete_token);?>').success(function() {
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
<div><p><a href="../">Back</a></p></div>
</body>
</html>