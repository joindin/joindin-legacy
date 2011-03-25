<?php

class Pending_talk_claims_model extends Model
{
	
	public function addClaim($talkId,$claimId)
	{	
		$data = array(
			'talk_id' 		=> $talkId,
			'claim_id'		=> $claimId,
			'speaker_id' 	=> $this->session->userdata('ID'),
			'date_added' 	=> time()
		);
		var_dump($data);
		$this->db->insert('pending_talk_claims',$data);
	}
	
	/**
	 * Given the event ID, find the claims for the talks in the event
	 * 
	 * @param integer $eventId Event ID
	 * @return array $result Pending claims found
	 */
	public function getEventTalkClaims($eventId)
	{
		$CI=&get_instance();
		$CI->load->model('event_model','eventModel');
		
		$eventTalks = $CI->eventModel->getEventTalks($eventId);
		
		$talkIds = array();
		foreach($eventTalks as $talk){ $talkIds[] = $talk->ID; }
		
		$results = $this->db->select('*')
			->from('pending_talk_claims')
			->join('talks','pending_talk_claims.talk_id = talks.id')
			->join('user','pending_talk_claims.speaker_id = user.id')
			->where_in('pending_talk_claims.talk_id',$talkIds)
			->get()->result();
			
		
		foreach($results as &$result){
			$result->claim_detail 	= $this->db->get_where('talk_speaker',array('ID'=>$result->claim_id))->result();
		}
		
		return $results;
	}
	
}

?>