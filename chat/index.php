<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="style.css">
	<meta charset="UTF-8">
	<title>live chat</title>
</head>
<body>
	<div class="chat">
		<input type="text" class="chat-name" placeholder="Enter Your name">
		<div class="chat-messages">
			<!-- Messages will be appended-->
		</div>
		<textarea placeholder="Type Your Message"></textarea>
		<div class="chat-status">Status: <span> Idle </span></div>
	</div>

	<script src="http://127.0.0.1:8080/socket.io/socket.io.js"></script>

	<script>
		
	functionName();

	function functionName() {
		var getNode = function(s) {
			return document.querySelector(s);
		}

		// Get the required nodes
		var status = getNode('.chat-status span'),
		messages = getNode('.chat-messages'),
		textarea = getNode('.chat textarea'),
		chatName = getNode('.chat-name'),
		statusDefault = status.textContent,

		setStatus = function(s){
			status.textContent = s

			if(s !== statusDefault){
				var delay = setTimeout(function(){
					setStatus(statusDefault)
					clearInterval();
				}, 3000);
			}

		};


		
		// setStatus('Testing..');



		try {
			var socket = io.connect('http://127.0.0.1:8080');
		} catch(error){
			// Set status to warn user
		}

		if(socket != undefined) {

			// Lsting for output 
			socket.on('output', function(data){
				console.log(data);

				if(data.length) {
					// Loop through results
					for(var i = 0; i < data.length; i++) {
						var message = document.createElement('div');
						message.setAttribute('class', 'chat-message');
						message.textContent = data[i].name + ': ' + data[i].message;

						console.log(message);

						// Append 
						messages.appendChild(message);
						// messages.insertBefore(message, messages.firstChild)
					}
				}

			})

			// Listing for a status
			socket.on('status', function(data){
				setStatus((typeof data === 'object') ? data.message : data);

				if(data.clear === true) {
					textarea.value = '';
				}

			})
			
			// Listing for key down
			textarea.addEventListener('keydown', function(event){
				
				var self = this,
					name = chatName.value;

					// console.log(event.which);
					// console.log(event);

				if(event.which == 13 && event.shiftKey === false) {
					console.log("Send");
					socket.emit('input', {
						"name": name, "message": self.value
					})

					event.preventDefault();
				}
			})

		}
	}

	</script>
	
</body>
</html>