<?php

class Event_comments_model extends CI_Model {
	//------------------
	public function isUnique($data){
		$q=$this->db->get_where('event_comments',$data);
		$ret=$q->result();
		return (empty($ret)) ? true : false;
	}
	public function getEventComments($eid){
		$this->db->from('event_comments');
		$this->db->where('event_id',$eid);
		$q=$this->db->get();
		return $q->result();
	}
	public function getUserComments($uid){
		$this->db->from('event_comments');
		$this->db->where('user_id',$uid);
		$q=$this->db->get();
		return $q->result();
	}
	public function deleteComment($cid){
		$this->db->delete('event_comments',array('id'=>$cid));
	}
	public function getCommentDetail($cid){
		$q=$this->db->get_where('event_comments',array('ID'=>$cid));
		return $q->result();	
	}
}