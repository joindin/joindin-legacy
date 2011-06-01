<?php

class Categories_model extends MY_Model {

        function  __construct()
        {
            parent::__construct();
        }
	//--------------
	function getCats(){
		$this->db->from('categories');
		$q=$this->db->get();
		return $q->result();
	}
	function getTalkCat($tid){
		
	}
	function setTalkCat($tid,$cid){
		$arr=array(
			'cat_id'	=> $cid,
			'talk_id'	=> $tid
		);
	}
	
}
?>