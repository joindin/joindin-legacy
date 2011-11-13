<?php 
menu_pagetitle('Submit an event');
?>
<h1 class="icon-event">Submit an event</h1>
<?php if (!empty($msg)): ?>
<?php $this->load->view('msg_info', array('msg' => $msg)); ?>
<?php endif; ?>

<?php if (empty($msg)): ?>
<div class="box">
    <?php echo form_open('event/submit', array('class' => 'form-event-submit')); ?>
    
    <?php if (!empty($this->validation->error_string)): ?>
            <?php $this->load->view('msg_error', array('msg' => $this->validation->error_string)); ?>
    <?php endif; ?>    

    <h2>General</h2>
    
    <?php if ($is_site_admin): ?>
    <div class="row">
        <label for="spam_byass">Bypass Spam Filter</label>
        <?php echo form_checkbox('bypass_spam_filter',1); ?> Check to bypass spam filtering
        <div class="clear"></div>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <label for="event_title">Event Title</label>
        <?php echo form_input(array('name' => 'event_title', 'id' => 'event_title'), $this->validation->event_title); ?>
    
        <div class="clear"></div>
    </div>

    <div class="row">
        <label for="event_location">Venue name</label>
        <?php echo form_input(array('name' => 'event_loc', 'id' => 'event_location'), $this->validation->event_loc); ?>
    
        <div class="clear"></div>
    </div>
    

    <div class="row">
        <label for="geo">Event location</label>
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
        Address search: 
        <table>
            <tr>
                <td>
                    <input type="text" name="addr" id="addr" value="<?php echo $this->validation->addr; ?>" />
                </td>
                <td>
                    <input type="button" onclick="addr_search();" value="Search" />
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    Latitude:  <input type="text" name="event_lat" id="event_lat" style="width:200px;" value="<?php echo $this->validation->event_lat; ?>" />
                </td>
                <td>
                    Longitude: <input type="text" name="event_long" id="event_long" style="width:200px;" value="<?php echo $this->validation->event_long; ?>"/>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <div id="map_canvas" style="width: 300px; height: 300px"></div>
                </td>
                <td>
                    <ul id="addr_selection"></ul>
                </td>
            </tr>
        </table>
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
            window.onload = load_map;
        </script>
      <div class="clear"></div>
    </div>



    <h2>Contact Information</h2>
    <div class="row">
        <label for="event_contact_name">Event Contact Name</label>
        <?php echo form_input(array('name' => 'event_contact_name', 'id' => 'event_contact_name'), $this->validation->event_contact_name); ?>
    
        <div class="clear"></div>
    </div>

    <div class="row">
        <label for="event_contact_email">Event Contact Email</label>
        <?php echo form_input(array('name' => 'event_contact_email', 'id' => 'event_contact_email'), $this->validation->event_contact_email); ?>
    
        <div class="clear"></div>
    </div>

    <h2>Event Details</h2>
    <div class="row">
        <label for="event_stub">Event Stub</label>
        <?php echo form_input(array('name' => 'event_stub', 'id' => 'event_stub'), $this->validation->event_stub); ?>
        <div class="clear"></div>
    </div>
    
    <?php if ($is_auth): ?>
    <div class="row">
        <label for="is_event_admin">Event Admin</label>
        <?php 
            $is_admin=(isset($this->validation->is_admin)) ? $this->validation->is_admin : '';
            echo form_checkbox('is_admin','1', $is_admin); ?> I'm an event admin!<br/>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <label for="start">Event Start Date</label>
        <?php
        /*foreach (range(1,12) as $v) { $start_mo[$v]=$v; }
        foreach (range(1,32) as $v) { $start_day[$v]=$v; }
        foreach (range(date('Y'), date('Y')+5) as $v) { $start_yr[$v]=$v; }*/

        foreach (range(1,12) as $v) { $start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
        foreach (range(1,31) as $v) { $start_day[$v]=sprintf('%02d', $v); }
        foreach (range(date('Y'), date('Y')+5) as $v) { $start_yr[$v]=$v; }
        
        echo form_dropdown('start_mo', $start_mo, $this->validation->start_mo);
        echo form_dropdown('start_day', $start_day, $this->validation->start_day);
        echo form_dropdown('start_yr', $start_yr, $this->validation->start_yr);
        echo form_datepicker('start_day', 'start_mo', 'start_yr');
        ?>
    <div class="clear"></div>
    </div>
 <div class="row">
        <label for="start">Event End Date</label>
    <?php
        /*foreach (range(1,12) as $v) { $end_mo[$v]=$v; }
        foreach (range(1,32) as $v) { $end_day[$v]=$v; }
        foreach (range(date('Y'), date('Y')+5) as $v) { $end_yr[$v]=$v; }*/

        foreach (range(1,12) as $v) { $start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
        foreach (range(1,31) as $v) { $start_day[$v]=sprintf('%02d', $v); }
        foreach (range(date('Y'), date('Y')+5) as $v) { $start_yr[$v]=$v; }

        echo form_dropdown('end_mo', $start_mo, $this->validation->end_mo);
        echo form_dropdown('end_day', $start_day, $this->validation->end_day);
        echo form_dropdown('end_yr', $start_yr, $this->validation->end_yr);
        echo form_datepicker('end_day', 'end_mo', 'end_yr');
        ?>
     <div class="clear"></div>
    </div>
    
 <div class="row">
        <label for="event_tz_cont">Event Timezone:</label>
        <?php echo custom_timezone_menu('event_tz', $this->validation->event_tz_cont, $this->validation->event_tz_place ); ?>
     <div class="clear"></div>
    </div>
    
    <div class="row">
        <label for="start">Is the event private?</label>
        <?php
        $is_priv=(isset($this->validation->is_private)) ? $this->validation->is_private : '';
        echo form_radio('is_private','Y', $is_priv).' Yes'; 
        echo form_radio('is_private','N', $is_priv). 'No'; 
        ?><br/>
    </div>

    <div class="row">
        <label for="start">Call for Papers</label>
        <?php 
            $js='onClick="toggleCfpDates()"';
            echo form_checkbox('is_cfp','1', $this->validation->cfp_checked, $js); 
        ?> Yes, we're going to have a Call for Papers
        <br/><br/>
        <label for="start">Call for Papers Start Date</label>
    <?php
        /*foreach (range(1,12) as $v) { $end_mo[$v]=$v; }
        foreach (range(1,32) as $v) { $end_day[$v]=$v; }
        foreach (range(date('Y'), date('Y')+5) as $v) { $end_yr[$v]=$v; }*/

        foreach (range(1,12) as $v) { $cfp_start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
        foreach (range(1,31) as $v) { $cfp_start_day[$v]=sprintf('%02d', $v); }
        foreach (range(date('Y'), date('Y')+5) as $v) { $cfp_start_yr[$v]=$v; }

        $js=($this->validation->cfp_checked==1) ? '' : 'disabled';
        
        echo form_dropdown('cfp_start_mo', $cfp_start_mo, $this->validation->cfp_start_mo,'id="cfp_start_mo" '.$js);
        echo form_dropdown('cfp_start_day', $cfp_start_day, $this->validation->cfp_start_day,'id="cfp_start_day" '.$js);
        echo form_dropdown('cfp_start_yr', $cfp_start_yr, $this->validation->cfp_start_yr,'id="cfp_start_yr" '.$js);
        echo form_datepicker('cfp_start_day', 'cfp_start_mo', 'cfp_start_yr');
        ?>
     <div class="clear"></div>
    </div>
 <div class="row">
        <label for="start">Call for Papers End Date</label>
    <?php
        /*foreach (range(1,12) as $v) { $end_mo[$v]=$v; }
        foreach (range(1,32) as $v) { $end_day[$v]=$v; }
        foreach (range(date('Y'), date('Y')+5) as $v) { $end_yr[$v]=$v; }*/

        foreach (range(1,12) as $v) { $cfp_end_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
        foreach (range(1,31) as $v) { $cfp_end_day[$v]=sprintf('%02d', $v); }
        foreach (range(date('Y'), date('Y')+5) as $v) { $cfp_end_yr[$v]=$v; }

        echo form_dropdown('cfp_end_mo', $cfp_end_mo, $this->validation->cfp_end_mo,'id="cfp_end_mo" '.$js);
        echo form_dropdown('cfp_end_day', $cfp_end_day, $this->validation->cfp_end_day,'id="cfp_end_day" '.$js);
        echo form_dropdown('cfp_end_yr', $cfp_end_yr, $this->validation->cfp_end_yr,'id="cfp_end_yr" '.$js);
        echo form_datepicker('cfp_end_day', 'cfp_end_mo', 'cfp_end_yr');
        ?>
     <div class="clear"></div>
    </div>

    <div class="row">
        <label for="cfp-url-location">Call for Papers URL Location</label>
        <?php echo form_input('cfp_url', $this->validation->cfp_url,'id="cfp_url" '.$js); ?>
        <div class="clear"></div>
    </div>





    <div class="row">
        <label for="event_desc">Event Description</label>
        <?php 
        $attr=array(
            'name'	=> 'event_desc',
            'id'	=> 'event_desc',
            'cols'	=> 50,
            'rows'	=> 10,
            'value'	=> $this->validation->event_desc
        );
        echo form_textarea($attr); 
        ?>
        <div class="clear"></div>
    </div>


    <div class="row">
        <label for="cinput">Spambot check</label>
        <span>
          <?php echo form_input(array('name' => 'cinput', 'id' => 'cinput'), ""); ?>
          = <b><?php echo $captcha['text']; ?></b>
        </span>
        <div class="clear"></div>
    </div>

    <div class="row row-buttons">
        <?php echo form_submit(array('name' => 'sub', 'class' => 'btn-big'), 'Submit event'); ?>
    </div>
    
    <?php echo form_close(); ?>
</div>
<?php endif; ?>
