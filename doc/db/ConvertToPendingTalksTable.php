<?php
/**
* This is to convert the PENDING entries from the talk_speaker table
* into the new pending_talk_claims table and set as pending. This script
* can be run multiple times without issue.
*/

define('IS_CRON', true);
// output buffering catches all of the HTML output from the inclusion
ob_start(); include('../../src/index.php'); ob_end_clean();

$ci = &get_instance();

// Get the records from talk_speaker that are pending
$pendingClaims = $ci->db->get_where('talk_speaker',array('status'=>'pending'))->result();
//print_r($pendingClaims);

foreach($pendingClaims as $claim){
	// insert our new row...
	$ci->db->insert('pending_talk_claims',array(
		'talk_id'		=> $claim->talk_id,
		'submitted_by'	=> null,
		'speaker_id'	=> $claim->speaker_id,
		'date_added'	=> time(),
		'claim_id'		=> $claim->ID
	));
	$ci->db->delete('pending_talk_claim',array('ID'=>$claim->ID));
	
	echo 'Inserted record to '.$ci->db->insert_id().' from '.$claim->id."\n";
}

?>