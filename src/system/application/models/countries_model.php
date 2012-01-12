<?php

class Countries_model extends Model {

    function Countries_model() {
        parent::Model();
    }
    //--------------
    function getCountries() {
        $this->db->from('countries');
        $this->db->order_by('name', 'asc');
        $q=$this->db->get();
        return $q->result();
    }

}
?>
