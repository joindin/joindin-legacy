<?php

class Countries_model extends CI_Model {
	//--------------
	public function getCountries(){
		$this->db->from('countries');
        $this->db->order_by('name', 'asc');
		$q=$this->db->get();
		return $q->result();
	}

}
?>
