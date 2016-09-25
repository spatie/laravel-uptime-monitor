"use strict";

var app = require('express')();

app.get('/:statusCode', function (req, res) {
    var statusCode = req.params.statusCode;

    res.writeHead(statusCode, { 'Content-Type': 'text/html' });
    res.end(html);
});

var server = app.listen(80, function () {
    var host = 'localhost';
    var port = server.address().port;

    console.log('Testing server listening at http://%s:%s', host, port);
});
