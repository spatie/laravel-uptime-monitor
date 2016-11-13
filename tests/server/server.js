"use strict";

let app = require('express')();

let serverResponse = {};

app.get('/', function (request, response) {
    const statusCode = serverResponse.statusCode;

    response.writeHead(statusCode || 200, { 'Content-Type': 'text/html' });
    response.end(serverResponse.body || "This is the testserver");
});

app.post('setServerResponse', function(request, response) {
    serverResponse.statusCode = request.body.statusCode
    serverResponse.body = request.body.body;

    response.send("Response set");
});

app.get('/clearServerResponse', function(request, response) {
    serverResponse = {};

    response.send("Response cleared");
});

let server = app.listen(8080, function () {
    const host = 'localhost';
    const port = server.address().port;

    console.log('Testing server listening at http://%s:%s', host, port);
});
