<div id="claim_select_div" style="text-align:center;">
	Who are you?<br/>
	<form action="<?php echo '/talk/claim/'.$detail->tid ?>" method="POST">
	<?php
	$speaker_list = array();
	foreach($speakers as $speaker){
		if(empty($speaker->speaker_id)){
			$speaker_list[$speaker->ID]=$speaker->speaker_name;
		}
	}
	echo form_dropdown('claim_name_select', $speaker_list,null,'id="claim_name_select"');
	?>
	<!--<input type="button" value="claim" id="claim-btn"/>-->
	<input type="submit" value="claim" id="claim-btn"/>
	<input type="button" value="cancel" id="claim-cancel-btn"/>
	</form>
</div>
<p class="admin">
<?php if($admin):?>
	<a class="btn-small" href="/talk/delete/<?php echo $detail->tid; ?>">Delete talk</a>	
	<a class="btn-small" href="/talk/edit/<?php echo $detail->tid; ?>">Edit talk</a>
<?php endif; ?>
<?php if(count($speaker)>$is_claimed): 
	if(!isset($user_id)){
		$link = '/user/login';
		$class = '';
	}elseif(count($speaker)==1){
		$link = '/talk/claim/'.$detail->tid.'/'.$speaker->ID;
		$class = 'single';
	}else{
		// multiple speakers, still show the dropdown
		$class = 'multi';
	}
	?>
	<a class="btn-small" href="<?php echo $link ?>" id="claim_btn" name="<?php echo $class; ?>">Claim This Talk</a>	
<?php endif; ?>
</p>
<script type="text/javascript">
$('#claim_select_div').css('display','none');
</script>