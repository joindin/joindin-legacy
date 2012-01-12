<?php

class Blog_post_cat_model extends Model {

    function Blog_post_cat_model() {
        parent::Model();
    }
    //-------------------
    function getPostCats($pid) {
        $this->db->select('cat_id, name')
            ->from('blog_post_cat')
            ->join('blog_cats','blog_post_cat.cat_id=blog_cats.ID');
        $q=$this->db->get();
        return $q->result();
    }
    function setPostCat($pid, $cid) {
        //remove any associations we have so far
        $this->db->delete('blog_post_cat', array('post_id'=>$pid));
        
        //now we put the category in
        $arr=array('post_id'=>$pid,'cat_id'=>$cid);
        $this->db->insert('blog_post_cat', $arr);
    }
}

?>
