<?php

class Categories_model extends Model {

    function Categories_model() {
        parent::Model();
    }
    //--------------
    function getCats() {
        $this->db->from('categories');
        $q=$this->db->get();
        return $q->result();
    }
    function getTalkCat($tid) {
        
    }
    function setTalkCat($tid, $cid) {
        $arr=array(
            'cat_id'	=> $cid,
            'talk_id'	=> $tid
        );
    }
    
}
?>
