(function($, window, document, undefined){
    $.fn.extend({
        'joindIn_tabs': function() {
            var $tabContainer = $(this);
            $tabContainer.find('ul li a').bind('click.joindIn_tabs',function(e){
                e.preventDefault();
                var $t = $(this),$tp = $t.parent(), $r = $t.attr('rel'), $e = $('#'+$r);
                if ($e.length != 0) {
                    $tabContainer.find('.ui-tabs-panel').not('.ui-tabs-hide').addClass('ui-tabs-hide');
                    $e.removeClass('ui-tabs-hide');
                }

                $tp.siblings('.ui-tabs-selected').removeClass('ui-tabs-selected ui-state-active ui-state-focus');
                $tp.addClass('ui-tabs-selected ui-state-active ui-state-focus');
            });
        },
        'joindIn_map': function() {
            var mapDiv = this;
            if (mapDiv.length > 1) {
                mapDiv = mapDiv.first();
            }
            var lat = mapDiv.attr('data-lat');
            var lon = mapDiv.attr('data-lon');
            var zoomLevel = mapDiv.attr('data-zoom');
            if (zoomLevel > 18) {
                zoomLevel = 18;
            }
            var map = new L.Map(this.attr('id'), {zoomControl: false});
            
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                osmAttribution = 'Map data &copy; 2012 <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
                osm = new L.TileLayer(osmUrl, {maxZoom: 18, attribution: osmAttribution});
            
            map.setView(new L.LatLng(lat, lon), zoomLevel).addLayer(osm);
            var marker = L.marker([lat, lon]).addTo(map);

            function moveMap(lat, lon) {
                map.setView(new L.LatLng(lat, lon), map.getZoom());
            }
        }
    });
})(jQuery, window, document)
