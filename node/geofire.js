var express = require('express');
var app = express();
var bodyParser = require('body-parser');
var Firebase = require('firebase');
var GeoFire = require('geofire');
const Redis = require("ioredis");
const { set } = require('lodash');
const redis = new Redis();

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
    extended: true
}));

Firebase.initializeApp({
    //development 
     databaseURL: "https://taxi-379310-default-rtdb.firebaseio.com",
    //databaseURL: "https://roda-a8392-default-rtdb.firebaseio.com",
    serviceAccount: '../public/firebase.json', //this file is downloaded from Firebase Console
});

var fire_db = Firebase.database();
var driversRef = Firebase.database().ref().child('drivers');
var requestRef = Firebase.database().ref().child('requests');

// Create a GeoFire index
var geoFire = new GeoFire.GeoFire(driversRef);

const env = require('dotenv').config({
    path: '../.env'
});

const port = env.parsed.NODE_GEOFIRE_PORT;

function queryGeoLocation(req, res) {
    try {
        const lat = parseFloat(req.params.lat);
        const long = parseFloat(req.params.lng);
        const vehicle_type = req.params.vehicle_type;
       // const service_type = req.params.service_type;
        const radius =  parseInt(req.params.radius);
        //  console.log("------------------------------------");
        //  console.log(lat);
        //  console.log(long);
        //  console.log("------------------------------------");
        var fire_drivers = [];
        
        let geoQuery = geoFire.query({ center: [lat, long], radius: radius });

        getGeoData = function(geoQuery) {
            
            return new Promise(function(resolve, reject) {
                // console.log("sdsd");
                geoQuery.on("key_entered", function(key, location, distance) {
                    
                    driversRef.child(key).on('value', function(snap) {
                        let driver = snap.val();
                        let date = new Date();
                        let timestamp = date.getTime();
                        let conditional_timestamp = timestamp - (5 * 60 * 1000);
                       //console.log(driver);
                        
                        if (conditional_timestamp < driver.updated_at) {
                            // var servi = driver.service_category;
                            // console.log("-----------------------------------");
                            // console.log(servi);
                            // console.log("-----------------------------------");
                            
                            // var splited_service = servi.split(',');
                            
                           if (driver.is_active == 1 & driver.is_available == 1 & driver.type == vehicle_type) {
                        //    if (driver.is_active == 1 & driver.is_available == 1) {
                              
                            //   splited_service.forEach(singleservice => {
                                //   console.log("--------------------------------");
                                //   console.log(singleservice);
                                //   console.log(service_type);
                                //   console.log("--------------------------------");
                                //   if(singleservice == service_type){
                                    driver.distance = distance;
                                    fire_drivers.push(driver);
                                    // console.log(driver);
                                //   }
                            //   });
                             console.log(driver);
                          }
                        }

                        resolve(fire_drivers);
                    });
                });
                
            });
        };

        getGeoData(geoQuery).then(function(data) {
            res.send({ success: true, message: 'success', data: data });
        }).catch((err) => {
            res.status(500).send("Error: " + err);
        });

    } catch (err) {
        res.status(500).send("Error: " + err);
    }
}
function queryGetDriversNotUpdated(req, res) {
    try {
        const lat = parseFloat(req.params.lat);
        const long = parseFloat(req.params.lng);
        const radius =  parseInt(req.params.radius);
        var fire_drivers = [];
        let geoQuery = geoFire.query({ center: [lat, long], radius: radius });
        getGeoData = function(geoQuery) {
            return new Promise(function(resolve, reject) {
                geoQuery.on("key_entered", function(key, location, distance) {
                    driversRef.child(key).on('value', function(snap) {
                        let driver = snap.val();
                        let date = new Date();
                        let timestamp = date.getTime();
                        let conditional_timestamp = timestamp - (2 * 60 * 1000);
                        if (conditional_timestamp > driver.updated_at) {
                            if (driver.is_active == 1 & driver.is_available == 1) {
                                driver.distance = distance;
                                fire_drivers.push(driver);
                            }
                        }
                        resolve(fire_drivers);
                    });
                });
                
            });
        };

        getGeoData(geoQuery).then(function(data) {
            res.send({ success: true, message: 'success', data: data });
        }).catch((err) => {
            res.status(500).send("Error: " + err);
        });

    } catch (err) {
        res.status(500).send("Error: " + err);
    }
}
function queryGetDriversLogout(req, res) {
    try {
        const lat = parseFloat(req.params.lat);
        const long = parseFloat(req.params.lng);
        const radius =  parseInt(req.params.radius);
        var fire_drivers = [];
        let geoQuery = geoFire.query({ center: [lat, long], radius: radius });
        getGeoData = function(geoQuery) {
            return new Promise(function(resolve, reject) {
                geoQuery.on("key_entered", function(key, location, distance) {
                    driversRef.child(key).on('value', function(snap) {
                        let driver = snap.val();
                        let date = new Date();
                        let timestamp = date.getTime();
                        let conditional_timestamp = timestamp - (30 * 60 * 1000);
                        if (conditional_timestamp > driver.updated_at) {
                            if (driver.is_active == 1 & driver.is_available == 1) {
                                driver.distance = distance;
                                fire_drivers.push(driver);
                            }
                        }
                        resolve(fire_drivers);
                    });
                });
                
            });
        };

        getGeoData(geoQuery).then(function(data) {
            res.send({ success: true, message: 'success', data: data });
        }).catch((err) => {
            res.status(500).send("Error: " + err);
        });

    } catch (err) {
        res.status(500).send("Error: " + err);
    }
}

function queryDriverLocation(req, res) {
    try {
        // const slug = req.params.slug;
       
       
    } catch (err) {
        res.status(500).send("Error: " + err);
    }
}
function queryGetDrivers(req, res) {
    try {
        
        var fire_drivers = [];

        let geoQuery = geoFire.query({});

        getGeoData = function(geoQuery) {
            return new Promise(function(resolve, reject) {
                geoQuery.on("key_entered", function(key, location, distance) {
                    driversRef.child(key).on('value', function(snap) {
                        let driver = snap.val();
                        
                        let date = new Date();
                        let timestamp = date.getTime();
                        let conditional_timestamp = timestamp - (30 * 60 * 1000);
            
                        if (conditional_timestamp < driver.updated_at) {
                          if (driver.is_active == 1 & driver.is_available == 1) {
                              driver.distance = distance;
                              fire_drivers.push(driver);
                          }
                        }

                        resolve(fire_drivers);
                    });
                });
            });
        };

        getGeoData(geoQuery).then(function(data) {
            res.send({ success: true, message: 'success', data: data });
        }).catch((err) => {
            res.status(500).send("Error: " + err);
        });

    } catch (err) {
        res.status(500).send("Error: " + err);
    }
}

// default route
app.get('/', function(req, res) {
    return res.send({ success: true, message: 'hello' })
});

app.get('/:lat/:lng/:vehicle_type/:service_type/:radius', function(req, res) {
    return queryGeoLocation(req, res);
});

app.get('/driver/:slug', function(req, res) {
    return queryDriverLocation(req, res);
});

app.get('/get-drivers', function(req, res) {
    return queryGetDrivers(req, res);
});

app.get('/drivers-logout/:lat/:lng/:radius', function(req, res) {
    return queryGetDriversLogout(req, res);
});

app.get('/get-drivers-not-updated/:lat/:lng/:radius', function(req, res) {
    return queryGetDriversNotUpdated(req, res);
});



app.listen(port, function() {
    console.log('Node app is running on port ' + port);
});
