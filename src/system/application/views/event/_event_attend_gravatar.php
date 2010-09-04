<div class="box">
	<h4>Check out who's attending!</h4>
	<div class="ctn">
	<?php
	//print_r($attend_list);
	
	$has_icons=false;
	foreach($attend_list as $attendee){
		$icon_file=$gravatar_cache_dir.'/user'.$attendee->ID.'.jpg';
		if(is_file($icon_file)){
			$has_icons=true;
			echo '<img src="/inc/img/user_gravatar/user'.$attendee->ID.'.jpg" height="20"/><br/>';
		}
	}
	if(!$has_icons){
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