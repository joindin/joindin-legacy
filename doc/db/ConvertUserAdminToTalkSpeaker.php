<?php

/**
 * This script will take the current claims in the user_admin table
 * and apply their relationship to the talk_speaker table (setting speaker_id)
 */
define('IS_CRON', true);
// output buffering catches all of the HTML output from the inclusion
ob_start(); include('../../src/index.php'); ob_end_clean();

$ci = &get_instance();

// Get current claims
$ci->db->select('rid,uid,rtype,rcode');
$ci->db->from('user_admin');
$ci->db->where('rcode !=','pending');
$ci->db->where('rtype','talk');
$query =$ci->db->get();
$currentClaims = $query->result();

// For each of our results, loop and match them with talk_id
// We're going to have to match on name, but that's nothing new...

$notMatched = array();

// Sort back through the claims and organize by talk ID
$claimsByTalk = array();
foreach($currentClaims as $claim){
	$claimsByTalk[$claim->rid][]=$claim;
}

foreach($claimsByTalk as $talkId => $claims){
	
	// get the talk_speaker info for the talk
	$query = $ci->db->get_where('talk_speaker',array('talk_id' => $talkId));
	$talkDetail = $query->result();
	
	//var_dump($talkDetail);
	//var_dump($claims);
	
	// So, if there's only one claim and only one talk_speaker row, match
	if(count($claims)==1 && count($talkDetail)==1){
		$ci->db->where('ID',$talkDetail[0]->ID);
		$ci->db->update('talk_speaker',array('speaker_id' => $claims[0]->uid));
		
	}else{
		// we'll have to try to match by name
		foreach($claims as $claim){
			
			foreach($talkDetail as $detail){
				
				$speakerName = $detail->speaker_name;
				
				// get the information for the claim's UID
				$query = $ci->db->get_where('user',array('ID'=>$claim->uid));
				$userDetail = $query->result();

				if($speakerName==$userDetail[0]->full_name){
					$ci->db->where('ID',$detail->ID);
					$ci->db->update('talk_speaker',array('speaker_id' => $claim->uid));
				}
			}
			
		}
		
	}
}

?>