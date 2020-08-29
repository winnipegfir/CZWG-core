var map = L.map('map').setView([50.15109, -94.80795], 4);
var processedNats = [];
function createBigMap(planes, finalPositions) {

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>, NAT Track data from <a href="https://flightplandatabase.com/">Flight Plan Database</a>, Plane Icon from AccuMap Project',
        maxZoom: 100,
        zoom: 0,
        id: 'mapbox.light',
        accessToken: 'pk.eyJ1IjoiZWx0ZWNocm9uIiwiYSI6ImNqOTlydHR4czB4NG8ycWxzYXNla2pmOXcifQ.hBI3z2L84aiEDfp5H946_Q'
    }).addTo(map);

    planes.forEach(function (plane) {
        let markerIcon = L.icon({
            iconUrl: '/img/planes/base.png',
            iconSize: [30, 30],
            iconAnchor: [2,4]
        });
       var marker = L.marker([plane.latitude, plane.longitude], {rotationAngle: plane.heading, icon:markerIcon}).addTo(map);
       marker.bindPopup(`<h4>${plane.callsign}</h4><br>${plane.realname} ${plane.cid}<br>${plane.planned_depairport} to ${plane.planned_destairport}<br>${plane.planned_aircraft}`)
    });

    map.setZoom(4);
    console.log(ganderControllers)
    if(finalPositions['callsign']="CZWG_CTR") {
        console.log('test');
        var winnipegFIR = L.polygon([
            [47.083333, -87.000000],
            [47.908333, -88.775000],
            [48.108333, -90.100000],
            [49.000000, -93.500000],
            [49.000000, -95.166667],
            [48.999722, -110.000000],
            [53.425556, -110.001389],
            [54.767222, -108.691667],
            [54.766667, -108.416667],
            [64.408333, -80.000000],
            [53.466667, -80.000000],
            [52.000000, -83.141667],
            [50.000000, -86.266667],
            [49.533333, -87.000000],
        ]).addTo(map);
        winnipegFIR.bindPopup('<h3>Winnipeg Centre Online</h3>')
    }

    //Get tracks
    let api = "https://api.flightplandatabase.com/nav/NATS";
    let xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", api, false);
    xmlHttp.send(null);
    let apiString = xmlHttp.responseText;
    let apiJson = JSON.parse(apiString);
    console.log(apiJson);

    //Go through all the tracks
    for (track in apiJson) {
        //Go through the tracks and only use the good ones...
        if (checkIfNatProcessed(apiJson[track].ident) == false) {
            processedNats.push(apiJson[track].ident);
            //Create some markers
            let fixArray = [];
            for (n in apiJson[track].route.nodes) {/*
                if (apiJson[track].route.eastLevels.length == 0) {
                    createMarker(apiJson[track].route.nodes[n], apiJson[track].ident, 'orange');
                }
                else
                {
                    createMarker(apiJson[track].route.nodes[n], apiJson[track].ident, 'blue');
                } */
                fixArray.push([apiJson[track].route.nodes[n].lat, apiJson[track].route.nodes[n].lon]);
            }
            let polyline = new L.Polyline(fixArray, {
                color: '#616161 ',
                weight: 2,
                opacity: 1,
                smoothFactor: 1
            });
            if (apiJson[track].route.eastLevels.length == 0) {
                polyline.setStyle({
                    color: '#757575 '
                });
            }
            polyline.addTo(map);
        };
    }
}

function checkIfNatProcessed(ident) {
    if (processedNats.indexOf(ident) > -1) {
        return true;
    } else {
        return false;
    }
}

function createMarker(node, trackId, colour) {
    let markerIcon = L.icon({
        iconUrl: 'https://nesa.com.au/wp-content/uploads/2017/05/Dot-points-1.png',
        iconSize: [10, 10],
        iconAnchor: [2,4]
    });
    let marker = L.marker([node.lat, node.lon], {icon: markerIcon}).addTo(map);
    marker.bindPopup("<b>"+node.ident+"</b><br/>"+node.type+"<br/>"+node.lat+" "+node.lon);
}