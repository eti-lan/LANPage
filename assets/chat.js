jQuery.noConflict();

function entry_message(m) { 
new Ajax.Request('chat/system/send.php', {
            method:'post',
            postBody: 'say=' + m,
            onSuccess: function(transport) { 
                   	new Ajax.Request('chat/system/retr.php', {
  		method: 'get',
  		onSuccess: function(transport) {
     		document.getElementById('chat_text').innerHTML=transport.responseText;
		 var objDiv = document.getElementById("chat_text");
		objDiv.scrollTop = objDiv.scrollHeight;  
  		}
		});     
            }
});
}

function send() {
	var form = $('writer'); 
	form.request({
	 method: 'post',
   	 onSuccess: function(transport){

	new Ajax.Request('chat/system/retr.php', {
  		method: 'get',
  		onSuccess: function(transport) {
     		document.getElementById('chat_text').innerHTML=transport.responseText;
 		var objDiv = document.getElementById("chat_text");
		objDiv.scrollTop = objDiv.scrollHeight;  
  		}
	});	
    	},
	})
document.getElementById('say').value="";
document.getElementById('say').focus();
}

function openwindow(id) {
	document.getElementById(id).style.display = 'block';
}

function closewindow(id) {
	document.getElementById(id).style.display = 'none';
}

function changecolor(cc) {
	var url = 'chat/system/c_color.php?c=' + cc;
	new Ajax.Request(url, {
    method:'get',
    onSuccess: function(transport){
    },
    onFailure: function(){ alert('Failure') }
  });
}

function set_pm(name) {
if(name!='0') {
document.getElementById('prv_to').value= name;
document.getElementById('prv_text').innerHTML= " Senden an " + name + ".";
} else {
document.getElementById('prv_to').value= "";
document.getElementById('prv_text').innerHTML= " Senden an Alle.";
}
}

function add_sm(code) {
document.getElementById('say').value=document.getElementById('say').value + code;
}


function evaluateSubmit(e) {  
    var key = null;  
      if (e.which) {          
        key = e.which;
    } else {  
        key = e.keyCode;
    }  
      if (key == 13) { 
        send();
        return false;
    } else {  
        return true;  
    }  
}  


new Ajax.PeriodicalUpdater('chat_text', 'chat/system/retr.php', {
method: 'get', frequency: 7, decay: 2
});

new Ajax.PeriodicalUpdater('online_list', 'chat/system/wwo.php', {
method: 'get', frequency: 7, decay: 2
});
