var express = require('express');
var app = express();
var path = require('path');

// viewed at http://localhost:8080
app.get('*', function(req, res) {
    res.sendFile(path.join(__dirname + '/front-end/index.html'));
});

var port = process.env.PORT || 8080;
app.listen(port)