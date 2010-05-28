<div id="talks">
<?php if (count($by_day) == 0): ?>
	<?php $this->load->view('msg_info', array('msg' => 'No talks available at the moment.')); ?>
<?php else: 
	if(isset($track_filter)){
		echo '<span style="font-size:13px">Sessions for track <b>'.$track_data->track_name.'</b></span>';
		echo ' <span style="font-size:11px"><a href="/event/view/'.$event_detail->ID.'">[show all sessions]</a></span>';
		echo '<br/><br/>';
	}
	?>
	<table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
    <?php
	$total_comment_ct   = 0;
	$session_rate	    = 0;
    foreach ($by_day as $talk_section_date=>$talk_section_talks): // was $k=>$v
        $ct = 0;
    ?>
    	<tr>
    		<th colspan="4">
    			<h4 id="talks"><?php echo date('M d, Y', $talk_section_date); ?></h4>
    		</th>
    	</tr>
    	<?php foreach($talk_section_talks as $ik=>$talk): 
	    $session_rate+=$talk->rank;
		
		if(isset($track_filter)){
			//Filter to the track ID
			if(empty($talk->tracks)){ 
				// If there's no track ID on the talk, don't show it
				continue; 
			}else{
				// There are tracks on the session, let's see if any match...
				$filter_pass=false;
				foreach($talk->tracks as $talk_track){
					if($talk_track->ID==$track_filter){ $filter_pass=true; }
				}
				if(!$filter_pass){ continue; }
			}
		}
	?>
    	<tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
    		<td>
    			<?php $type = !empty($talk->tcid) ? $talk->tcid : 'Talk'; ?>
    			<span class="talk-type talk-type-<?php echo strtolower(str_replace(' ', '-', $type)); ?>" title="<?php echo escape($type); ?>"><?php echo escape(strtoupper($type)); ?></span>
    		</td>
    	    <?php 
				$sp_names=array();
				foreach($talk->codes as $ck => $cv){
					
					$iscl=(array_key_exists($talk->ID, $times_claimed)) ? true : false;
				
					//If there's an exactly matching claim (name too) or... 
					if(array_key_exists($cv,$cl) || $iscl){
						//we match the code, but we need to find the speaker...

						$spk_split=explode(',',$talk->speaker);
						foreach($spk_split as $spk=>$spv){
							if(trim($spv)==trim($ck)){
								if(isset($cl[$cv])){ 
									$uid=$cl[$cv]['uid']; 
								}else{ 
									if(count($spk_split)>1){ $sp_names[]=escape($ck); continue; }
									$uid=$claimed_uids[$talk->ID];
								}
								$sp_names[]='<a href="/user/view/'.$uid.'">'.escape($spv).'</a>';
							}else{
								
							}
						}
					}else{ $sp_names[]=escape($ck); }
					$sp=implode(', ',$sp_names);
				}
				?>
    		<td>
    			<a href="/talk/view/<?php echo $talk->ID; ?>"><?php echo escape($talk->talk_title); ?></a>
				<?php
					if($talk->display_time != '00:00') {echo '(' . $talk->display_time . ')';}
				?>
    		</td>
    		<td>
    			<?php echo $sp; ?>
    		</td>
    		<td>
				<a class="comment-count" href="/talk/view/<?php echo $talk->ID; ?>/#comments"><?php echo $talk->comment_count; ?></a>
			</td>
    	</tr>
    <?php
    	    $ct++;
	    $total_comment_ct+=$talk->comment_count;
        endforeach;
    endforeach;
    ?>
    </table>
<?php endif; ?>
</div>