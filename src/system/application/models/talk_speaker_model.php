<?php

class Talk_speaker_model extends Model {

	function Talk_speaker_model(){
		parent::Model();
	}
	//----------------
	
	/**
	 * Locate a speaker by talk ID and speaker name (string match)
	 *
	 * @param integer $talk_id Talk ID #
	 * @param string $speaker_name Speaker name
	 */
	private function _speakerExists($talk_id,$speaker_name){
		$find=array(
			'talk_id'		=> $talk_id,
			'speaker_name'	=> $speaker_name
		);
		$q=$this->db->get_where('talk_speaker',$find);
		return $q->result();
	}
	
	/**
	 * Add or update speaker information to the table
	 * If speaker is found, consider it an update
	 *
	 * @param integer $talk_id Talk ID #
	 * @param array $speaker_data Speaker information to insert
	 * 
	 * @return null
	 */
	public function handleSpeakerData($talk_id, array $speaker_data=null){
		if(!is_array($speaker_data)){ $speaker_data=array($speaker_data); }
		$speaker_names=array();
		
		foreach($speaker_data as $speaker){
			$data=array(
				'talk_id'		=> $talk_id,
				'speaker_name'	=> $speaker
			);
			if(!empty($speaker)){ $speaker_names[]=$speaker; }
			$speaker_row = $this->_speakerExists($talk_id,$speaker);
			if($speaker_row){
				//Update the current information
				$this->db->update('talk_speaker',$data,array('ID'=>$speaker_row[0]->ID));
			}else{
				// Add the new speaker
				$this->db->insert('talk_speaker',$data);
			}
		}
		
		// Now lets find the ones that aren't in our list and remove them
		if(!empty($speaker_names)){
			$this->db->where_not_in('speaker_name',$speaker_names);
			$this->db->where('talk_id',$talk_id);
			$this->db->delete('talk_speaker');
		}
	}
	
	public function getTalkSpeakers($talk_id){
		$q=$this->db->get_where('talk_speaker',array('talk_id'=>$talk_id));
		return $q->result();
	}
	
	/**
	 * Delete a speaker from the table
	 *
	 * @param integer Talk ID #
	 * @param string $speaker_name Speaker name
	 *
	 * @return null
	 */
	public function deleteSpeaker($talk_id,$speaker_name){
		$where=array(
			'talk_id'		=> $talk_id,
			'speaker_name'	=> $speaker_name
		);
		$this->db->delete('talk_speaker',$where);
	}
	
	/**
	 * Find all speakers for a given talk ID #
	 *
	 * @param integer $talk_id Talk ID #
	 * 
	 * @return array Speaker information
	 */
	public function getSpeakerByTalkId($talk_id){
		
		$q=$this->db->get_where('talk_speaker','talk_id ='.$talk_id);
		$ret=$q->result();
		
		return $ret;
	}
	
	
}

?>
