<?php if(is_array($attend_list) && count($attend_list) > 0) {
?>
<div class="box">
	<h4>Check out who's attending!</h4>
	<div class="ctn">
	<?php
	
	$has_icons=false;
	foreach($attend_list as $attendee){
		$icon_file=$gravatar_cache_dir.'/user'.$attendee->ID.'.jpg';
		if(is_file($icon_file)){
			$has_icons=true;
			echo '<a href="/user/view/'.$attendee->ID.'"><img src="/inc/img/user_gravatar/user'.$attendee->ID.'.jpg" height="20" style="margin:2px"i alt="'.$attendee->full_name.'"/></a>';
		}
	}
	if(!$has_icons && count($attend_list)>0){
		echo '<ul>';
		$end=(count($attend_list)>10) ? 10 : count($attend_list);
		$rand=array_rand($attend_list,$end);
		if(!is_array($rand)){
			$attendee=$attend_list[$rand];
			echo '<li><a href="">'.$attendee->full_name.'</a><br/>';
		}else{
			foreach($rand as $rand_id){ 
				$attendee=$attend_list[$rand_id];
				echo '<li><a href="">'.$attendee->full_name.'</a><br/>';
			}
		}
		echo '</ul>';
	}
	?>
	</div>

</div>
<?php
} ?>
