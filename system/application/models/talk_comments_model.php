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
	
}
?>