<?php

class Talk_comments_model extends Model {

	function Talk_comments_model(){
		parent::Model();
	}
	//-------------------
	function isUnique($data){
		$q=$this->db->get_where('talk_comments',$data);
		$ret=$q->result();
		return (empty($ret)) ? true : false;
	}
	function getUserComments($uid){
		$this->db->from('talk_comments');
		$this->db->where('user_id',$uid);
		$q=$this->db->get();
		return $q->result();
	}
	function deleteComment($cid){
		$this->db->delete('talk_comments',array('id'=>$cid));
	}
	function getCommentDetail($cid){
		$q=$this->db->get_where('talk_comments',array('ID'=>$cid));
		return $q->result();	
	}
}
?>