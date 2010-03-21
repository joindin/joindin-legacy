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

function splitCommentTypes($talk_comments){
	$comments=array();
	foreach($talk_comments as $k=>$comment){
		$type=($comment->comment_type===NULL) ? 'comment' : $comment->comment_type; 
		$comments[$type][]=$comment;
	}
	return $comments;
}

/**
 * Takes a talk, and attempts to add a flag to say whether the talk is on
 * now or whether it is on next.
 * 
 * This logic *WILL* be broken until talks have an end time.  Live with it, or add end times.
 */
function talk_decorateNowNext($talk) {
	$time = time();

	// Define some heuristic time windows for the start time of the "now" and "next" talks
	$now_start  = $time - 3600;
	$now_end    = $time;
	$next_start = $time;
	$next_end   = $time + 3600;

	if ($talk->date_given > $now_start && $talk->date_given < $now_end) {
		$talk->now_next = "now";
	} else if ($talk->date_given > $next_start && $talk->date_given < $next_end) {
		$talk->now_next = "next";
	} else {
		$talk->now_next = "";
	}

	return $talk;
}

/**
 * Takes an array of talks, and attempts to add a flag to each one to say whether the talk is on
 * now or whether it is on next.
 * 
 * This logic *WILL* be broken until talks have an end time.  Live with it, or add end times.
 */
function talk_listDecorateNowNext($talks) {
	foreach ($talks as $key=>$talk) {
		$talks[$key] = talk_decorateNowNext($talks[$key]);
	}

	return $talks;
}

?>
