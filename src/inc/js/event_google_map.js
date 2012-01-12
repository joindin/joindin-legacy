var map;
var marker;
var geocoder;
var infowindow = new google.maps.InfoWindow();

function load_map() {
	geocoder = new google.maps.Geocoder();
	
	var map_latitude 	= $('#map_latitude').val();
	var map_longitude 	= $('#map_longitude').val();
	var map_zoom 		= parseInt($('#map_zoom').val());
	
	var myOptions = {
	  zoom: map_zoom,
	  center: new google.maps.LatLng(map_latitude, map_longitude), // UK
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	google.maps.event.addListener(map, 'click', function(event) {
	  placeMarker(event.latLng);
	});
	placeMarker(new google.maps.LatLng(map_latitude, map_longitude));
}

function placeMarker(location) {
	var clickedLocation = new google.maps.LatLng(location);
	if (!marker) {
		marker = new google.maps.Marker({
			position: location, 
			map: map
		});
	} else {
		marker.setPosition(location);
	}

	//map.setCenter(location);

	$('#event_lat').val(location.lat());
	$('#event_long').val(location.lng());
	
	$('#output_latitude').html(location.lat());
	$('#output_longitude').html(location.lng());
}

function chooseAddr(lat, lng) {
	var location = new google.maps.LatLng(lat, lng);
	map.setCenter(location);
	placeMarker(location);
}

function addr_search() {
	var inp = document.getElementById("addr");
	if (geocoder) {
		geocoder.geocode( { 'address': inp.value}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				$('#addr_selection').empty();
				if (results.length > 1) {
					$(results).each(function(result) {
						var newLI = $('<li><a href="#" onclick="chooseAddr(' + this.geometry.location.lat() + ', ' + this.geometry.location.lng() + ');return false;">' + this.formatted_address + '</a></li>');
						newLI.appendTo($('#addr_selection'));
						//console.log(result.geometry.location);
					});
				}
				//map.setCenter(results[0].geometry.location);
				map.fitBounds(results[0].geometry.viewport);
				placeMarker(results[0].geometry.location);
			} else {
				notifications.alert("Geocode was not successful for the following reason: " + status);
			}
		});
	}
}
window.onload = load_map;