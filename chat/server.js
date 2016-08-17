var mongo = require('mongodb').MongoClient;
client = require('socket.io').listen(8080).sockets;


mongo.connect('mongodb://127.0.0.1/chat', function(err, db){
	if(err) throw err; 

	client.on('connection', function(socket){

		var col = db.collection('messages'),
			sendStatus = function(s){
				socket.emit('status', s)
			};


		// Emit all messages
		col.find().limit(100).sort({_id: 1}).toArray(function(error, results){
			if(err) throw error;
			socket.emit('output', results); 
		})

		console.log('Someone connected');

		// wait for input
		socket.on('input', function(data){
			console.log(data);

			var name = data.name,
				message = data.message,
				whiteSpacePatters = /^\s*$/;



			// validation for whitespaces
			if (whiteSpacePatters.test(name) || whiteSpacePatters.test(message)) {

				console.log('Invalid data');
				sendStatus('Name and message is required');

			} else {

				// CONNECT TO PHP SCRIPTS AND MYSQL HERE ???

				col.insert({"name" : name, "message" : message}, function(){
					console.log('Inserted');


					// Emit latest message to all clients
					client.emit('output', [data])

					sendStatus({
						message : "Message sent",
						clear : true
					});

				});

			}

		});

	})
});

