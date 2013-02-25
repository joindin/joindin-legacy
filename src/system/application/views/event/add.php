<?php menu_pagetitle('Edit an event'); ?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
    var map;
    var marker;
    var geocoder;
    var infowindow = new google.maps.InfoWindow();

    function load_map() {
        geocoder = new google.maps.Geocoder();
        var myOptions = {
            zoom: 5,
            center: new google.maps.LatLng(53.8000, -1.5833), // UK
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng);
        });
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

    function toggleCfpDates(){

        var sel_fields = new Array(
                'cfp_start_mo','cfp_start_day','cfp_start_yr',
                'cfp_end_mo','cfp_end_day','cfp_end_yr','cfp_url'
        );

        // Get the current status of the first one...
        stat = $('input[name="is_cfp"]').is(':checked');
        if(stat){
            $('div.cfp').show();
            $.each(sel_fields,function(){
                $('#'+this).removeAttr("disabled");
            });
        }else{
            $('div.cfp').hide();
            $.each(sel_fields,function(){
                $('#'+this).attr("disabled","disabled");
            });
        }
    }

    $('document').ready(function(){
        load_map();
        toggleCfpDates();
    });
</script>
<style type="text/css">
    h2.first
    {
        margin-top: 10px;
    }

    h2
    {
        margin-top: 40px;
    }

    #ctn .main form input, #ctn .main form textarea
    {
        border: 1px solid silver;
        padding: 3px 5px;
        border-radius: 3px;
    }

    #ctn .main div.row.last
    {
        border: none;
    }

    div.cfp
    {
        display:      none;
        padding-left: 30px;
    }

    #addr_selection
    {
        border:           1px solid silver;
        height:           210px;
        margin-right:     13px;
        background:       white;
        overflow:         auto;
        list-style-image: none;
        margin-bottom:    0px;
    }

    #addr_selection li
    {
        margin:  0;
        padding: 0;
    }

    #addr_selection li a
    {
        display:       block;
        border-bottom: 1px solid #f0f0f0;
        padding:       7px 10px;
        font-size:     0.9em;
    }

    #addr_selection li a:hover
    {
        background: #f9f9f9;
    }

    #ctn .main form #event_long, #ctn .main form #event_lat
    {
        border:     none;
        background: transparent;
        width:      auto;
        display:    inline
    }

    .box .row td
    {
        vertical-align: top
    }

    .box .row table
    {
        margin-bottom: 0;
    }

    #ctn .main form #addr_search_button
    {
        width:   auto;
        display: inline
    }

    #ctn .main form #addr
    {
        width:   260px;
        display: inline;
    }

    #map_canvas
    {
        width:  250px;
        height: 300px;
    }
</style>

<h1 class="icon-event">Edit an event</h1>

<?php if (!empty($msg)) $this->load->view('msg_info', array('msg' => $msg)); ?>

<?php if (empty($msg)): ?>
<div class="box">
    <?php echo form_open('event/edit/'.$this->validation->ID, array('class' => 'form-event-submit')); ?>
    <?php if (!empty($this->validation->error_string)): ?>
    <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>

<h2 class="first">General</h2>

    <?php if (isset($is_site_admin) && $is_site_admin): ?>
<div class="row">
    <label for="bypass_spam_filter">Bypass Spam Filter</label>
    <?php echo form_checkbox('bypass_spam_filter', 1); ?> Check to bypass spam filtering
    <div class="clear"></div>
</div>
    <?php endif; ?>

<div class="row">
    <label for="event_title">Event Title *</label>
    <?php echo form_input(array('name' => 'event_name', 'id' => 'event_title'), $this->validation->event_name); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="event_location">Venue name *</label>
    <?php echo form_input(array('name' => 'event_loc', 'id' => 'event_location'), $this->validation->event_loc); ?>
    <div class="clear"></div>
</div>

<div class="row last">
    <label for="addr">Venue location</label>
    <table>
        <tr>
            <td>
                <table>
                    <tr>
                        <td colspan="2">
                            <input type="text" name="addr" id="addr" value="<?php echo $this->validation->addr; ?>" />
                            <input type="button" id="addr_search_button" onclick="addr_search()" value="Search" />
                        </td>
                    </tr>
                    <tr>
                        <td>Latitude</td>
                        <td>
                            <input type="text" name="event_lat" id="event_lat" readonly="readonly" value="<?php echo $this->validation->event_lat; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>Longitude</td>
                        <td>
                            <input type="text" name="event_long" id="event_long" readonly="readonly" value="<?php echo $this->validation->event_long; ?>" />
                        </td>
                    </tr>
                </table>
                <ul id="addr_selection"></ul>
            </td>
            <td align="right">
                <div id="map_canvas"></div>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
</div>

<h2>Contact Information</h2>
<div class="row">
    <label for="event_contact_name">Event Contact Name *</label>
    <?php echo form_input(array('name' => 'event_contact_name', 'id' => 'event_contact_name', 'value' => $this->validation->event_contact_name)); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="event_contact_email">Event Contact Email *</label>
    <?php echo form_input(array('name' => 'event_contact_email', 'id' => 'event_contact_email'), $this->validation->event_contact_email); ?>
    <div class="clear"></div>
</div>

    <?php if (isset($is_auth) && $is_auth): ?>
<div class="row last">
    <?php
    echo form_checkbox('is_admin','1', ($this->validation->is_admin == '1')); ?> I'm an event admin!<br/>
    <div class="clear"></div>
</div>
    <?php endif; ?>

<h2>Event Details</h2>
<div class="row">
    <label for="event_stub">Event Stub</label>
    <?php echo form_input(array('name' => 'event_stub', 'id' => 'event_stub'), $this->validation->event_stub, 'placeholder="my-event"'); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="event_tz_cont">Event Timezone *</label>
    <?php echo custom_timezone_menu('event_tz', $this->validation->event_tz_cont, $this->validation->event_tz_place ); ?>
    <div class="clear"></div>
</div>

<div class="row">
    <table>
        <tr>
            <td><label for="start">Event Start Date *</label></td>
            <td><label for="end">Event End Date *</label></td>
        </tr>
        <tr>
            <td>
                <?php
                foreach (range(1,12) as $v) { $start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
                foreach (range(1,31) as $v) { $start_day[$v]=sprintf('%02d', $v); }
                foreach (range(date('Y'), date('Y')+5) as $v) { $start_yr[$v]=$v; }

                echo form_dropdown('start_mo', $start_mo, $this->validation->start_mo);
                echo form_dropdown('start_day', $start_day, $this->validation->start_day);
                echo form_dropdown('start_yr', $start_yr, $this->validation->start_yr);
                echo form_datepicker('start_day', 'start_mo', 'start_yr');
                ?>
            </td>
            <td>
                <?php
                foreach (range(1,12) as $v) { $start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
                foreach (range(1,31) as $v) { $start_day[$v]=sprintf('%02d', $v); }
                foreach (range(date('Y'), date('Y')+5) as $v) { $start_yr[$v]=$v; }

                echo form_dropdown('end_mo', $start_mo, $this->validation->end_mo);
                echo form_dropdown('end_day', $start_day, $this->validation->end_day);
                echo form_dropdown('end_yr', $start_yr, $this->validation->end_yr);
                echo form_datepicker('end_day', 'end_mo', 'end_yr');
                ?>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="event_desc">Event Description *</label>
    <div>(In <em>English</em>, with optional alternative translation)</div>
    <?php
    echo form_textarea(array(
        'name'  => 'event_desc',
        'id'    => 'event_desc',
        'cols'  => 50,
        'rows'  => 10,
        'value' => $this->validation->event_desc
    ));
    ?>
    <div class="clear"></div>
</div>

<div class="row">
    <label for="start">Call for Papers</label>
    <?php echo form_checkbox('is_cfp','1', $this->validation->cfp_checked, 'onclick="toggleCfpDates()"'); ?>
    Yes, we're going to have a Call for Papers
    <div class="clear"></div>
</div>

<div class="row cfp">
    <table>
        <tr>
            <td><label for="cfp_start">Call for Papers Start Date</label></td>
            <td><label for="cfp_end">Call for Papers End Date</label></td>
        </tr>
        <tr>
            <td>
                <?php
                $js = ($this->validation->cfp_checked == 1) ? '' : 'disabled';

                foreach (range(1,12) as $v) { $cfp_start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
                foreach (range(1,31) as $v) { $cfp_start_day[$v]=sprintf('%02d', $v); }

                foreach (range(date('Y'), date('Y')+5) as $v) { $cfp_start_yr[$v]=$v; }
                
                if($this->validation->cfp_start_yr < date('Y')) {
                    $difference = date('Y') - $this->validation->cfp_start_yr;
                    $add_year = $this->validation->cfp_start_yr;
                    do {
                        array_unshift($cfp_start_yr, $add_year);
                        $add_year++;
                    } while(date('Y') != $add_year);
                }

                echo form_dropdown('cfp_start_mo', $cfp_start_mo, $this->validation->cfp_start_mo, 'id="cfp_start_mo" ' . $js);
                echo form_dropdown('cfp_start_day', $cfp_start_day, $this->validation->cfp_start_day, 'id="cfp_start_day" ' . $js);
                echo form_dropdown('cfp_start_yr', $cfp_start_yr, $this->validation->cfp_start_yr, 'id="cfp_start_yr" ' . $js);
                echo form_datepicker('cfp_start_day', 'cfp_start_mo', 'cfp_start_yr');
                ?>
            </td>
            <td>
                <?php
                foreach (range(1,12) as $v) { $cfp_end_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
                foreach (range(1,31) as $v) { $cfp_end_day[$v]=sprintf('%02d', $v); }
                foreach (range(date('Y'), date('Y')+5) as $v) { $cfp_end_yr[$v]=$v; }

                if($this->validation->cfp_end_yr < date('Y')) {
                    $difference = date('Y') - $this->validation->cfp_end_yr;
                    $add_year = $this->validation->cfp_end_yr;
                    do {
                        array_unshift($cfp_end_yr, $add_year);
                        $add_year++;
                    } while(date('Y') != $add_year);
                }

                echo form_dropdown('cfp_end_mo', $cfp_end_mo, $this->validation->cfp_end_mo, 'id="cfp_end_mo" ' . $js);
                echo form_dropdown('cfp_end_day', $cfp_end_day, $this->validation->cfp_end_day, 'id="cfp_end_day" ' . $js);
                echo form_dropdown('cfp_end_yr', $cfp_end_yr, $this->validation->cfp_end_yr, 'id="cfp_end_yr" ' . $js);
                echo form_datepicker('cfp_end_day', 'cfp_end_mo', 'cfp_end_yr');
                ?>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
</div>

<div class="row cfp">
    <label for="cfp-url">Call for Papers URL</label>
    <?php echo form_input('cfp_url', $this->validation->cfp_url, 'id="cfp_url" placeholder="http://www.example.com"' . $js); ?>
    <div class="clear"></div>
</div>

<div class="row last">
    <label for="is_private">Is this event private?</label>
    <?php
    echo form_radio('is_private','Y', $this->validation->is_private == 'Y') . ' Yes';
    echo form_radio('is_private','N', $this->validation->is_private == 'N') . ' No';
    ?><br/>
</div>

<div class="row row-buttons">
    <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit event'); ?>
</div>

    <?php echo form_close(); ?>
</div>
<?php endif;
