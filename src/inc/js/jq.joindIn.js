;
/**
 * The semi-colon above is a safety-net for when plugin files are joined and
 * minified and trailing semi-colons have been incorrectly ommitted from other
 * plugin files.
 * 
 * Despite what you might see in other javascript source code, omitting semi-colons
 * should not be encouraged. Relying on the parser to make up for lazy programming 
 * WILL come back and bite you in the ass!
 */

/**
 * These plugins are being used as a way to organise code for the joindIn site, they
 * are not intended to be stand alone jQuery plugins that could be distributed for 
 * wider consumption.
 * 
 * References:
 *      http://learn.jquery.com/plugins/basic-plugin-creation/
 *      http://docs.jquery.com/Plugins/Authoring
 *      http://jqueryboilerplate.com/
 *      
 */

/**
 * joindIn_tabs plugin.
 */
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
        }
    });
})(jQuery, window, document);

/**
 * joindIn_map Plugin
 */
(function($, window, document, undefined){
    $.fn.joindIn_map = function(method) {
        var methods = {
            defaults: {
                draggable: false,
                moveMapCallback: function() {}
            },
            init: function(options) {
                return this.each(function(){
                    // Merge options with the defaults.
                    var customOptions = $.extend({}, methods.defaults, options);
                    
                    // Get initial settings from the data-* attribs.
                    var $this = $(this);
                    var mapDiv = $(this);
                    var lat = mapDiv.attr('data-lat');
                    var lon = mapDiv.attr('data-lon');
                    var zoomLevel = mapDiv.attr('data-zoom');
                    if (zoomLevel > 18) {
                        zoomLevel = 18;
                    }
                    
                    // Initialise the map
                    var map = new L.Map(mapDiv.attr('id'), {zoomControl: true});
                    var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                        osmAttribution = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
                        osm = new L.TileLayer(osmUrl, {maxZoom: 18, attribution: osmAttribution});
                    
                    map.setView(new L.LatLng(lat, lon), zoomLevel).addLayer(osm);
                    map.on('click', function(e){
                        methods.moveMap.call($this, {lat: e.latlng.lat, lon: e.latlng.lng});
                    });
                    map.on('zoomend', function(){
                        methods.moveMap.call($this, {});
                    });
                    
                    // Initialise the marker
                    if (customOptions.draggable) {
                        var marker = L.marker([lat, lon], { draggable: true } ).addTo(map);
                        marker.on('dragend', function() {
                            // Keep the data-* attribs updated with the marker position.
                            var latlng = marker.getLatLng();
                            methods.moveMap.call($this, {lat: latlng.lat, lon: latlng.lng});
                        });
                    } else {
                        var marker = L.marker([lat, lon], { draggable: false } ).addTo(map);
                    }
                    
                    // Store the data for this element.
                    $this.data('joindIn_map', {
                        options: customOptions,
                        map: map,
                        marker: marker
                    });
                    
                    // Trigger an initial moveMapCallback.
                    customOptions.moveMapCallback(this, {lat: lat, lon: lon, zoom: map.getZoom()});
                });
            },
            moveMap: function(options) {
                return this.each(function(){
                    // Get a data store on a per element basis.
                    var $this = $(this);
                    var data = $this.data('joindIn_map');
                    
                    // Update the map and marker positions.
                    if ((options.lat !== undefined) && (options.lon !== undefined)) {
                        data.map.setView(new L.LatLng(options.lat, options.lon), data.map.getZoom());
                        data.marker.setLatLng(data.map.getCenter());
                    }
                    
                    // Update the data-* attribs of the HTML element.
                    // We get the position from the marker incase the user zoomed into another part of the map
                    // but didn't move the marker. 
                    // (And also because the soom action takes into account the mouse position!)
                    var latlng = data.marker.getLatLng();
                    $this.attr('data-lat', latlng.lat);
                    $this.attr('data-lon', latlng.lng);
                    $this.attr('data-zoom', data.map.getZoom());
                    
                    // Call the callback.
                    data.options.moveMapCallback(this, {lat: options.lat, lon: options.lon, zoom: data.map.getZoom()});
                });
            }
        };
        
        // Method calling logic
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
        }

    };
})(jQuery, window, document);
