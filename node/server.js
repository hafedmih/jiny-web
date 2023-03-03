const http = require("http");
const express = require("express");
const app = express();
const cors = require("cors");
const Redis = require("ioredis");
const redis = new Redis();
var mysql = require('mysql');

require("dotenv").config({
    path: '../.env'
});



const { NODE_SERVER_PORT } = process.env;

const server = http.createServer(app);

const io = require('socket.io')(server,{
    cors: { origin : "*" }
})

server.listen(NODE_SERVER_PORT, function () {
    console.log("server is running.");
});

// subscribe to the published event
redis.subscribe("general");

// message is default in redis
redis.on("message", function (channel, data) {
    let response = JSON.parse(data);
    
    console.log(response)
    io.emit(response.event, response.message)
})


io.on('connection',function (socket){
    console.log('connected');

    socket.on('disconnect',(err) => {
        console.log('disconnected',err)
    })

    // for testing does it works
    socket.on('test',function(data){
        console.log(data)
    })
})