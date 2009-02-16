<?php

class Blog_comments_model extends Model {

	function Blog_comments_model(){
		parent::Model();
	}
	//-------------------
	function getPostComments($pid){

		$this->db->select('blog_comments.*, user.username AS uname');
	    $this->db->from('blog_comments');
		$this->db->join('user', 'user.ID = blog_comments.author_id', 'left');

		$this->db->where('blog_post_id = ' . (int)$pid);

		$this->db->order_by('blog_post_id','ASC');
		$q=$this->db->get();
		return $q->result();
	}
}

?>