<?php

class Talk_track_model extends Model {

	function Talk_track_model(){
		parent::Model();
	}
	//---------------------
	function getSessionTrackInfo($sid){
		$sql=sprintf('
			select
				et.track_name,
				et.ID,
				et.track_desc,
				et.track_color
			from
				talk_track tt,
				event_track et
			where
				tt.talk_id=%s and
				tt.track_id=et.ID
		',$sid);
		$q=$this->db->query($sql);
		return $q->result();
	}
	function setSessionTrack($sid,$tid){
		$arr=array(
			'talk_id'	=> $sid,
			'track_id'	=> $tid
		);
		$this->db->insert('talk_track',$arr);
	}
	function updateSessionTrack($sid,$curr_tid,$tid){
		// first be sure we have one to begin with
		$st=$this->getSessionTrackInfo($sid);
		if(empty($st) || $curr_tid==null){ 
			$this->setSessionTrack($sid,$tid);
		}else{
			$this->db->where('talk_id',$sid);
			$this->db->where('track_id',$curr_tid);
			$this->db->update('talk_track',array('track_id'=>$tid));
		}
	}
	function deleteSessionTrack($sid,$tid){
		$arr=array(
			'talk_id'	=> $sid,
			'track_id'	=> $tid
		);
		$this->db->delete('talk_track',$arr);
	}
}

?>
