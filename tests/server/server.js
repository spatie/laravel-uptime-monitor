"use strict";

let app = require('express')();

let bodyParser = require('body-parser');
app.use(bodyParser.json()); // support json encoded bodies
app.use(bodyParser.urlencoded({ extended: true })); // support encoded bodies

let serverResponse = {};

app.get('/', function (request, response) {
    const statusCode = serverResponse.statusCode;

    response.writeHead(statusCode || 200, { 'Content-Type': 'text/html' });
    response.end(serverResponse.body || "This is the testserver");
});

app.post('/setServerResponse', function(request, response) {
    serverResponse.statusCode = request.body.statusCode
    serverResponse.body = request.body.body;

    console.log("Response set");
    response.send("Response set");
});

let server = app.listen(8080, function () {
    const host = 'localhost';
    const port = server.address().port;

    console.log('Testing server listening at http://%s:%s', host, port);
});
