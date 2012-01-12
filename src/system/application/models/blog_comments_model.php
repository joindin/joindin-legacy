<?php

class Blog_comments_model extends Model {

        function Blog_comments_model() {
                parent::Model();
        }
        //-------------------
        function getCommentsByPostId($postId) {
        $query = $this->db->get_where('blog_comments', array('blog_post_id'=>$postId));
                return $query->result();
        }
}

?>
