var map;
var marker;

function load_map() {
	var lat = $('#map_latitude').val();
	var lon = $('#map_longitude').val();
	var zoomLevel = parseInt($('#map_zoom').val());

    map = new L.Map('map_canvas', {zoomControl: true});
    
    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        osmAttribution = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
        osm = new L.TileLayer(osmUrl, {maxZoom: 18, attribution: osmAttribution});
    
    map.setView(new L.LatLng(lat, lon), zoomLevel).addLayer(osm);
	
    placeMarker(new L.LatLng(lat, lon));
}

function placeMarker(location) {
	if (!marker) {
        marker = L.marker(location, { draggable: true } ).addTo(map);
        marker.on('dragend', function() { location = marker.getLatLng(); placeMarker(location)} );
	} else {
		marker.setLatLng(location);
	}

	$('#event_lat').val(location.lat);
	$('#event_long').val(location.lng);
	
	$('#output_latitude').html(location.lat);
	$('#output_longitude').html(location.lng);
}

function chooseAddr(lat, lng) {
	var location = new L.LatLng(lat, lng);
	map.panTo(location);
	placeMarker(location);
}

function addr_search() {
	var inp = document.getElementById("addr");

    $.getJSON('//nominatim.openstreetmap.org/search?format=json&limit=5&q=' + inp.value, function(data) {
        var items = [];

        $.each(data, function(key, val) {
            items.push("<li><a href='#' onclick='chooseAddr(" + val.lat + ", " + val.lon + ");return false;'>" + val.display_name + '</a></li>');
        });

        if (items.length != 0) {
            $('#results').empty();
            $('<p>', { html: "Search results:" }).appendTo('#results');
            $('<ul/>', {
                'class': 'my-new-list',
                html: items.join('')
            }).appendTo('#results');
        } else {
            $('#results').empty();
            $('<p>', { html: "No results found" }).appendTo('#results');
        }
    });
}

window.onload = load_map;
