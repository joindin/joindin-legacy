<?php

class Countries_model extends MY_Model {

        function  __construct()
        {
            parent::__construct();
        }
	//--------------
	function getCountries(){
		$this->db->from('countries');
        $this->db->order_by('name', 'asc');
		$q=$this->db->get();
		return $q->result();
	}

}
?>
