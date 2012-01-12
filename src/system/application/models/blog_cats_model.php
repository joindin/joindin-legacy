<?php

class Blog_cats_model extends Model {

    function Blog_cats_model() {
        parent::Model();
    }
    //-------------------
    function getCategories() {
        $q=$this->db->get('blog_cats');
        return $q->result();
    }
}

?>
