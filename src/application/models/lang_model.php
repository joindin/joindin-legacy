<?php

class Lang_model extends MY_Model {

        function  __construct()
        {
            parent::__construct();
        }
	//--------------
	public function getLangs(){
		$this->db->from('lang');
		$this->db->order_by('lang_name');
		$q=$this->db->get();
		return $q->result();
	}
	
	/**
	 * Check to see if two-letter abbreviation is valid
	 */
	public function isLang($lang){
		$q=$this->db->get_where('lang',array('lower(lang_abbr)'=>strtolower($lang)));
		$ret=$q->result();
		return (empty($ret)) ? false : $ret[0]->ID;
	}
}
?>