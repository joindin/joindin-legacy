<?php

/**
* Build the code for a session, used in determining the claim status
*/
function buildCode($tid,$eid,$title,$speaker_name){
	$speaker_name=trim($speaker_name);
	$str='ec'.str_pad(substr($tid,0,2),2,0,STR_PAD_LEFT).str_pad($eid,2,0,STR_PAD_LEFT);
	$str.=substr(md5($title.$speaker_name),5,5);
	return $str;
}
/**
* Given the full list of claimed talks (event_model->getClaimedTalks), find the number of times 
* they've been claimed
*/
function buildTimesClaimed($claimed_talks){
	$times_claimed=array();
	foreach($claimed_talks as $k=>$v){ 
		if(isset($times_claimed[$v->rid])){ $times_claimed[$v->rid]++; }else{ $times_claimed[$v->rid]=1; }
	}
	return $times_claimed;
}
/**
* Given the full list of claimed talks (event_model->getClaimedTalks), find the user IDs with claims
*/
function buildClaimedUids($claimed_talks){
	$claimed_uids=array();
	foreach($claimed_talks as $k=>$v){ $claimed_uids[$v->rid]=$v->uid; }
	return $claimed_uids;
}
/**
* Given the full list of claimed talks (event_model->getClaimedTalks), figure out the
* user IDs that have claims in there and the count of claims on a session
*/
function buildClaimDetail($claimed_talks){
	$claim_detail=array(
		'uids'			=> array(),
		'claim_count'	=> array()
	);
	foreach($claimed_talks as $k=>$v){ 
		$claim_detail['uids'][$v->rid]=$v->uid;
		if(isset($times_claimed[$v->rid])){ 
			$claim_detail['claim_count'][$v->rid]++; }else{ $claim_detail['claim_count'][$v->rid]=1; 
		}
	}
	return $claim_detail;
}

function buildClaims($claimed_talks){
	$claims=array();
	
	foreach($claimed_talks as $talk){
		$claims[$talk->talk_id][$talk->full_name]=$talk->user_id;
	}
	return $claims;
}

/**
* Given the full list of sessions, finds which of them given have slides
*/
function buildSlidesList($sessions){
	$slides_list=array();
	foreach($sessions as $s){
		if(!empty($s->slides_link)){
			$slides_list[$s->ID]=array('link'=>$s->slides_link,'speaker'=>$s->speaker,'title'=>$s->talk_title);
    	}
	}
	return $slides_list;
}

/**
* Return true or false depending on whether the event is currently on
*/
function event_isNowOn($event_start, $event_end) {
	$time = time();
	return ($time > $event_start && $time < $event_end);
}

/**
 * Takes an event, and attempts to add a flag to say whether the event is on
 * now.
*/
function event_decorateNow($event) {
	$time = time();
	if (event_isNowOn($event->event_start, $event->event_end)) {
		$event->now = "now";
	} else {
		$event->now = "";
	}
	return $event;
}

/**
 * Takes an array of events, and attempts to add a flag to each one to say whether the event is on
 * now.
*/
function event_listDecorateNow($events) {
	$time = time();
	foreach ($events as $key=>$event) {
		$events[$key] = event_decorateNow($events[$key]);
	}
	return $events;
}

function buildTalkStats($talks){
	$stats=array('comments_total'=>0,'rating_avg'=>0);
	foreach($talks as $talk){ 
		$stats['comments_total']+=$talk->comment_count; 
		if(!empty($talk->rank)){ $stats['rating_avg']+=$talk->rank; }
	}
	// Average the stats out
	$stats['rating_avg']=$stats['rating_avg']/$stats['comments_total'];
	return $stats;
}
