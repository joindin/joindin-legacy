<?php

class Event_comments_model extends Model {

	function Event_comments_model(){
		parent::Model();
	}
	//------------------
	function getEventComments($eid){
		$this->db->from('event_comments');
		$this->db->where('event_id',$eid);
		$q=$this->db->get();
		return $q->result();
	}
	function getUserComments($uid){
		$this->db->from('event_comments');
		$this->db->where('user_id='.$uid);
		$q=$this->db->get();
		return $q->result();
	}
	function deleteComment($cid){
		$this->db->delete('event_comments',array('id'=>$cid));
	}
}

?>