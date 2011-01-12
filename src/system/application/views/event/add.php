<?php
//$tz_list=array('Select Continent');
//foreach($tz as $k=>$v){ $tz_list[(string)$v->offset]=floor((string)$v->offset/3600); }

if(isset($this->edit_id) && $this->edit_id){
	echo form_open_multipart('event/edit/'.$this->edit_id);
	$sub='Save Edits';
	$title='Edit Event: <a style="text-decoration:none" href="/event/view/'.$detail[0]->ID.'">'.$detail[0]->event_name.'</a>';
	$curr_img=$detail[0]->event_icon;
	menu_pagetitle('Edit Event: '.$detail[0]->event_name);
}else{ 
	echo form_open_multipart('event/add'); 
	$sub	= 'Add Event';
	$title	= 'Add Event';
	$curr_img='none.gif';
	menu_pagetitle('Add an Event');
}

echo '<h2>'.$title.'</h2>';
?>
<?php if (!empty($msg) || !empty($this->validation->error_string)): ?>
<?php 
	if(!empty($this->validation->error_string)){ $msg.=$this->validation->error_string; }
	$this->load->view('msg_info', array('msg' => $msg)); 
?>
<?php endif; ?>

<div class="box">
    <div class="row">
    	<label for="event_name">Event Name:</label>
	<?php echo form_input('event_name',$this->validation->event_name); ?>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_start">Event Start:</label>
	<?php
	foreach(range(1,12) as $v){
	    $m=date('M',mktime(0,0,0,$v,1,date('Y')));
	    $start_mo[$v]=$m; }
	foreach(range(1,32) as $v){ $start_day[$v]=$v; }
	foreach(range($min_start_yr,date('Y')+5) as $v){ $start_yr[$v]=$v; }
	echo form_dropdown('start_mo',$start_mo,$this->validation->start_mo);
	echo form_dropdown('start_day',$start_day,$this->validation->start_day);
	echo form_dropdown('start_yr',$start_yr,$this->validation->start_yr);
	?>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_end">Event End:</label>
	<?php
	foreach(range(1,12) as $v){
	    $m=date('M',mktime(0,0,0,$v,1,date('Y')));
	    $end_mo[$v]=$m; }
	foreach(range(1,32) as $v){ $end_day[$v]=$v; }
	foreach(range($min_end_yr,date('Y')+5) as $v){ $end_yr[$v]=$v; }
	echo form_dropdown('end_mo',$end_mo,$this->validation->end_mo);
	echo form_dropdown('end_day',$end_day,$this->validation->end_day);
	echo form_dropdown('end_yr',$end_yr,$this->validation->end_yr);
	?>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_location">Venue name:</label>
	<?php echo form_input('event_loc',$this->validation->event_loc); ?>
    </div>
    <div class="clear"></div>

	<div class="row">
        <label for="geo">Event location</label>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		Address search: 
		<table>
			<tr>
				<td>
					<input type="text" name="addr" id="addr" />
				</td>
				<td>
					<button type="button" onclick="addr_search();">Search</button>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>
					Latitude:  <input type="text" name="event_lat" id="event_lat" style="width:200px;" />
				</td>
				<td>
					Longitude: <input type="text" name="event_long" id="event_long" style="width:200px;" />
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
						<?php
							if (is_numeric($this->validation->event_lat) && $this->validation->event_lat != '') {
								$lat  = $this->validation->event_lat;
								$long = $this->validation->event_long;
								$zoom = 13;
							} else {
								$lat  = 0;
								$long = 0;
								$zoom = 0;
							}
						?>
				  zoom: <?php echo $zoom; ?>,
				  center: new google.maps.LatLng(<?php echo $lat?>, <?php echo $long?>), // UK
				  mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				google.maps.event.addListener(map, 'click', function(event) {
				  placeMarker(event.latLng);
				});
				placeMarker(new google.maps.LatLng(<?php echo $lat?>, <?php echo $long?>));
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
							alert("Geocode was not successful for the following reason: " + status);
						}
					});
				}
			}
			window.onload = load_map;
		</script>
	  <div class="clear"></div>
    </div>





    <div class="row">
    	<label for="event_tz_cont">Event Timezone:</label>
		<?php echo custom_timezone_menu('event_tz', $this->validation->event_tz_cont, $this->validation->event_tz_place ); ?>
	<span style="color:#3567AC;font-size:11px">For more information on locations and 
	their time zone, see <a href="http://en.wikipedia.org/wiki/List_of_time_zones">this
	page on Wikipedia</a></span>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_description">Event Description:</label>
	<?php
	$arr=array(
		'name'	=> 'event_desc',
		'cols'	=> 45,
		'rows'	=> 12,
		'value'	=> $this->validation->event_desc
	);
	echo form_textarea($arr);
	?>
    </div>
    <div class="clear"></div>
	<div class="row">
    	<label for="event_stub">Event Stub</label>
    	<?php echo form_input(array('name' => 'event_stub', 'id' => 'event_stub'), $this->validation->event_stub); ?>
    	<span style="color:#3567AC;font-size:11px">What's a <b>stub</b>? It's the "shortcut" part of the URL to help visitors get to your event faster. An example might be "phpevent" in the address "<?php echo $this->config->site_url(); ?>event/phpevent". If no stub is given, you can still get to it via the event ID.</span>
        <div class="clear"></div>
    </div>
	<div class="clear"></div>
	<div class="row">
	<label for="event_icon">Is event private?</label>
	<?php
		$ev_y=($this->validation->event_private=='Y') ? true : false;
		$ev_n=($this->validation->event_private=='N') ? true : false;
		if(empty($this->validation->event_private)){ $ev_n=true; }

		echo form_radio('event_private','Y',$ev_y).' Yes'; 
		echo form_radio('event_private','N',$ev_n).' No'; 
	?>
	</div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_icon">Event Icon:</label>
	<input type="file" name="event_icon" size="20" /><br/><br/>
	<img src="/inc/img/event_icons/<?php echo $curr_img; ?>"/>
	<span style="color:#3567AC;font-size:11px">
		<b>Please Note:</b> Only icons that are 90 pixels by 90 pixels will be accepted!<br/>
		Allowed types: gif, jpg, png
	</span>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_link">Event Link(s):</label>
	<?php echo form_input('event_href',$this->validation->event_href); ?><br/>
    </div>
    <div class="clear"></div>
    <div class="row">
    	<label for="event_hashtag">Event Hashtag(s):</label>
	<?php echo form_input('event_hashtag',$this->validation->event_hashtag); ?>
	<span style="color:#3567AC;font-size:11px">Seperate tags with commas</span>
    </div>
    <div class="clear"></div>

	<div class="row">
		<label for="start">Call for Papers</label>
		<?php 
			$js='onClick="toggleCfpDates()"';
			echo form_checkbox('is_cfp','1',$this->validation->cfp_checked,$js); 
		?> Yes, we're going to have a Call for Papers
		<br/><br/>
        <label for="start">Call for Papers Start Date</label>
	<?php
		/*foreach(range(1,12) as $v){ $end_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $end_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $end_yr[$v]=$v; }*/

	    foreach(range(1,12) as $v){ $cfp_start_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
    	foreach(range(1,31) as $v){ $cfp_start_day[$v]=sprintf('%02d', $v); }
    	foreach(range(date('Y'),date('Y')+5) as $v){ $cfp_start_yr[$v]=$v; }

		$js=($this->validation->cfp_checked==1) ? '' : 'disabled';
		
		echo form_dropdown('cfp_start_mo',$cfp_start_mo,date('m',$this->validation->event_cfp_start),'id="cfp_start_mo" '.$js);
		echo form_dropdown('cfp_start_day',$cfp_start_day,date('d',$this->validation->event_cfp_start),'id="cfp_start_day" '.$js);
		echo form_dropdown('cfp_start_yr',$cfp_start_yr,date('Y',$this->validation->event_cfp_start),'id="cfp_start_yr" '.$js);
		?>
	 <div class="clear"></div>
    </div>
 	<div class="row">
        <label for="start">Call for Papers End Date</label>
	<?php
		/*foreach(range(1,12) as $v){ $end_mo[$v]=$v; }
		foreach(range(1,32) as $v){ $end_day[$v]=$v; }
		foreach(range(date('Y'),date('Y')+5) as $v){ $end_yr[$v]=$v; }*/

	    foreach(range(1,12) as $v){ $cfp_end_mo[$v]=strftime('%B', strtotime('2000-' . $v . '-01')); }
    	foreach(range(1,31) as $v){ $cfp_end_day[$v]=sprintf('%02d', $v); }
    	foreach(range(date('Y'),date('Y')+5) as $v){ $cfp_end_yr[$v]=$v; }

		echo form_dropdown('cfp_end_mo',$cfp_end_mo,date('m',$this->validation->event_cfp_end),'id="cfp_end_mo" '.$js);
		echo form_dropdown('cfp_end_day',$cfp_end_day,date('d',$this->validation->event_cfp_end),'id="cfp_end_day" '.$js);
		echo form_dropdown('cfp_end_yr',$cfp_end_yr,date('Y',$this->validation->event_cfp_end),'id="cfp_end_yr" '.$js);
		?>
	 <div class="clear"></div>
    </div>

	<div class="row">
		<label for="cfp-url-location">Call for Papers URL Location</label>
		<?php echo form_input('cfp_url',$this->validation->cfp_url,'id="cfp_url"'); ?>
		<div class="clear"></div>
	</div>
	

    <div class="row">
    	<?php echo form_submit('sub',$sub); ?>
    </div>
</div>
<?php echo form_close(); ?>
