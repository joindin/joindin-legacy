<?php

class Categories_model extends CI_Model {
	//--------------
	public function getCats(){
		$this->db->from('categories');
		$q=$this->db->get();
		return $q->result();
	}
	public function getTalkCat($tid){
		
	}
	public function setTalkCat($tid,$cid){
		$arr=array(
			'cat_id'	=> $cid,
			'talk_id'	=> $tid
		);
	}
	
}
?>