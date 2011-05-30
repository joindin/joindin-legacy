<?php

class Blog_cats_model extends MY_Model {

        function  __construct()
        {
            parent::__construct();
        }
	//-------------------
	function getCategories(){
		$q=$this->db->get('blog_cats');
		return $q->result();
	}
}

?>