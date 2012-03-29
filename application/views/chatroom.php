<html>
	<head>	
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		
		<title>Magnality Chat</title>
		
		<link rel="shortcut icon" href="<?=base_url()?>favicon.ico">
		<link rel="apple-touch-icon" href="<?=base_url()?>apple-touch-icon.png">
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="<?=base_url()?>css/chat.css" type="text/css" media="screen" />	
	</head>
	<body>
		<div id="chat-container" class="clearfix">
			<div id="chat"></div>
			<div class="chat-form">
			<?php	
				echo form_open('chatroom/set', array('id'=>'chat-post'));	
				echo form_textarea(array('name'=>'msg', 'id' => 'msgbox'));	
				echo form_submit(array('name' => 'submit', 'value' => 'Send', 'class' => 'submit'));	
				echo form_close();
			?>			
			</div>
		</div> <!-- end #chat-container -->
			
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script>!window.jQuery && document.write(unescape('%3Cscript src="js/libs/jquery-1.5.1.js"%3E%3C/script%3E'))</script>
			
		<script>
		var pollTime = 1500;
		var poll = setInterval(get, pollTime);
		
		var lastusr = "";
		var lasttime = "";
		var decayCount = 0;
		
		function get() {
			$.getJSON("<?=site_url('chatroom/getJSON')?>", function(json) {
				if($.isEmptyObject(json)) {
					if(decayCount++ > 10) {		
						clearInterval(poll);
						pollTime += 500;
						poll = setInterval(get, pollTime);
						decayCount = 0;
					}
					return;
				}
				
				decayCount = 0;
				clearInterval(poll);
				pollTime = 1500;
				poll = setInterval(get, pollTime);
				
					
				$.each(json, function(i, item) {
					var date = new Date(item.time*1000);
					var hours = date.getHours() - 12;
					var minutes = date.getMinutes();
					minutes = (minutes < 10) ? "0"+minutes : minutes;
					var timestamp = hours+":"+minutes;
					
					if(lastusr == item.usr) item.usr = "";
					else lastusr = item.usr;
					
					if(lasttime == timestamp) timestamp = "";
					else lasttime = timestamp;
					
					
					$('<div>').addClass('msg')
					.append("<span class=time>"+timestamp+"</span>")
					.append("<span class=name>"+item.usr+"</span>")
					.append("<span class=text>"+item.msg+"</span>")
					.appendTo('#chat');				
					
					var chat = document.getElementById('chat');
					chat.scrollTop = chat.scrollHeight;
					
				});
			});
		}
			
		$('#chat-post').submit(function(event) {
			event.preventDefault();
			
			var txt = $('#msgbox').val(),
				 url = $(this).attr('action');
				 
			$.post(url, {msg: txt}, function(data) {
				if( data ) $('#msgbox').val('');
				else $('#chat').append('Last message not received.');
			});
		});
		
		$('#msgbox').bind('keypress', function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if(code == 13) {
				$('#chat-post').submit();							
			}
		});	
		</script>
	</body>
</html>