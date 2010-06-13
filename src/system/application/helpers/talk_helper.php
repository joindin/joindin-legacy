<?php

function buildClaimData($talk_detail,$talk_claims,&$ftalk){
	$speaker=array();
	foreach($talk_claims as $k=>$claim){
		// Be sure we're only looking at the ones we need
		if($claim->rid!=$talk_detail->ID){ continue; }else{ $ftalk++; }

		// Get the claim code
		$cd=$claim->rcode;

		// Break up the speakers
		$sp=explode(',',$claim->tdata['speaker']);

		// Now, check to see if any of the codes match the $cd
		$ct=0;
		$matched=array();
		foreach($claim->tdata['codes'] as $ck=>$claim_code){
			// This was so that, if there's one speaker claim, so ahead and link it...
			// seems to have backfired a little
			//$iscl=(count($sp)==1 && count($v->tdata['codes'])==1) ? true : false;
			$iscl=false;
			
		    if($claim_code==$cd || $iscl){
			   $speaker[$sp[$ct]]='<a href="/user/view/'.$claim->uid.'">'.$sp[$ct].'</a>';
		    }else{
				if(!isset($speaker[$sp[$ct]])){ $speaker[$sp[$ct]]=$sp[$ct]; }
		    }
		    $ct++;
		}
	}
	return $speaker;
}

/**
 * Split out the comment types based on the inputted array (comment, keynote, etc)
 * @param array $talk_comments Full listing of all talks for an event
 *
 * @return array $comments Sorted list of sessions
 */
function splitCommentTypes($talk_comments){
	$comments=array();
	foreach($talk_comments as $k=>$comment){
		$type=($comment->comment_type===NULL) ? 'comment' : $comment->comment_type; 
		$comments[$type][]=$comment;
	}
	return $comments;
}

/**
 * Create the links for the speakers, matching by name
 */
function buildClaimedLinks($speakers,$claim_detail){
	
	$speaker_data	= array();
	$speaker_links	= array();
	
	foreach($claim_detail as $claim){
		$speaker_data[$claim->full_name]=$claim->uid;
	}
	foreach($speakers as $speaker){
		$name=$speaker->speaker_name;
		if(array_key_exists($name,$speaker_data)){
			$speaker_links[]='<a href="/user/view/'.$speaker_data[$name].'">'.$name.'</a>';
		}else{ $speaker_links[]=$name; }
	}
	
	//Check the claim...if there's only one claim, assign no matter what
	if(count($speakers) && count($claim_detail)){
		$speaker_links	= array();
		$speaker_links[]= '<a href="/user/view/'.$claim_detail[0]->uid.'">'.$speakers[0]->speaker_name.'</a>';
	}
	
	return implode(', ',$speaker_links);
}


/**
 * Takes an array of talks, and attempts to add a flag to each one to say whether the talk is on
 * now or whether it is on next.
 * 
 * This logic *WILL* be broken until talks have an end time.  Live with it, or add end times.
 */
function talk_listDecorateNowNext($talks) {
	$now = time();
	
	// set the default
	foreach ($talks as $talk) {
		$talk->now_next = "";
	}

	// check the event dates - any talk element will do for this
	// if the event is not in progress, nothing is either now or next
	if($talks[0]->event_start > $now || $talks[0]->event_end <= $now) {
		return;
	}

	// firstly sort the talks into time slots
	$talks_keyed_on_time = array();
	foreach ($talks as $key=>$talk) {
		$talks_keyed_on_time[$talk->date_given][] = $talk;
	}

	// sort this so we get things in time order
	ksort($talks_keyed_on_time);

	// now work out which slot is most recent - this becomes "now" and the next slot is "next"
	$old_slot_time = 0;
	$new_slot_time = 0;
	foreach($talks_keyed_on_time as $time=>$talk_list) {
		// the time in this iteration becomes our new time
		$new_slot_time = $time;
		if($new_slot_time > $now) {
			break;
		}
		// store this time in the old time slot for the next iteration
		$old_slot_time = $time;
	}

	// our slot times identify our now and next talk sets
	foreach($talks_keyed_on_time[$old_slot_time] as $talk) {
		$talk->now_next = "now";
	}
	foreach($talks_keyed_on_time[$new_slot_time] as $talk) {
		$talk->now_next = "next";
	}

	return $talks;
}

?>
