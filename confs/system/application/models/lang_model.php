<?php

class Lang_model extends Model {

	function Lang_model(){
		parent::Model();
	}
	//--------------
	function getLangs(){
		$this->db->from('lang');
		$this->db->order_by('lang_name');
		$q=$this->db->get();
		return $q->result();
	}
}
?>