<?php

class Blog_comments_model extends CI_Model {
        //-------------------
        public function getCommentsByPostId($postId){
		$query = $this->db->get_where('blog_comments',array('blog_post_id'=>$postId));
                return $query->result();
        }
}