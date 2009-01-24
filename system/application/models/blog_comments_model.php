<?php

class Blog_comments_model extends Model {

	function Blog_comments_model(){
		parent::Model();
	}
	//-------------------
	function getPostComments($pid){
		$q=$this->db->get_where('blog_comments',array('blog_post_id'=>$pid));
		return $q->result();
	}
}

?>