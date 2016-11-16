let app = require('express')();

let bodyParser = require('body-parser');

app.use(bodyParser.json()); // support json encoded bodies
app.use(bodyParser.urlencoded({ extended: true })); // support encoded bodies

let serverResponse = {};

app.get('/', function (request, response) {
    var statusCode = serverResponse.statusCode;

    response.writeHead(statusCode || 200, { 'Content-Type': 'text/html' });
    response.end(serverResponse.body || "This is the testserver");
});

app.post('/setServerResponse', function(request, response) {
    serverResponse.statusCode = request.body.statusCode
    serverResponse.body = request.body.body;

    response.send("Response set");
});

var server = app.listen(8080, function () {
    var host = 'localhost';
    var port = server.address().port;

    console.log('Testing server listening at http://%s:%s', host, port);
});
