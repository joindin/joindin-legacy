<?php

class Tz_model extends Model {

    function Tz_model() {
        parent::Model();
    }
    //--------------------
    function getContInfo($cid=null) {
        $this->db->select('cont')
            ->distinct()
            ->from('tz')
            ->order_by('cont asc');
        $q=$this->db->get();
        return $q->result();
    }
    function getAreaInfo($cont) {
        $this->db->select('area')
            ->distinct()
            ->where("cont='".$cont."'")
            ->from('tz')
            ->order_by('cont asc');
        $q=$this->db->get();
        return $q->result();
    }
    function getOffsetInfo() {
        $this->db->select('offset')
            ->distinct()
            ->from('tz')
            ->order_by('offset asc');
        $q=$this->db->get();
        return $q->result();
    }
    function getTzInfo($tid=null) {
        
    }
}

?>
