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
