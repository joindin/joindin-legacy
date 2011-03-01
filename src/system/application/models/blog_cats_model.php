<?php

class Blog_cats_model extends CI_Model {
	//-------------------
	public function getCategories(){
		$q=$this->db->get('blog_cats');
		return $q->result();
	}
}