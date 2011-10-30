<?php

class Event_track_model extends Model {

    function Event_track_model() {
        parent::Model();
    }
    //---------------------
    
    function getEventTracks($eid) {
        $q=$this->db->get_where('event_track', array('event_id'=>$eid));
        $ret=$q->result();
        foreach ($ret as $k=>$tr) {
            $q=$this->db->query('select count(ID) ct from talk_track where track_id='.$this->db->escape($tr->ID));
            $u=$q->result();
            $ret[$k]->used=$u[0]->ct;
        }
        return $ret;
    }
    function getTrackSessions($tid) {

        $sql=sprintf("
            select
                t.talk_title,
                t.ID
            from
                talks t,
                talk_track tt
            where
                tt.track_id=%s and
                tt.talk_id=t.ID
        ", $this->db->escape($tid));
        
        //$q=$this->db->get_where('talks', array('event_track_id'=>$tid));
        $q=$this->db->query($sql);
        return $q->result();
    }
    
    //---------------------
    function addEventTrack($eid, $name, $desc) {
        $arr=array(
            'event_id'	=> $eid,
            'track_name'=> $name,
            'track_desc'=> $desc
        );
        $this->db->insert('event_track', $arr);
    }
    function updateEventTrack($tid, $arr) {
        $this->db->where('ID', $tid);
        $this->db->update('event_track', $arr);
    }
    function deleteEventTrack($tid) {
        //Be sure there's no sessions associated with it first
        if (count($this->getTrackSessions($tid))>0) {
            return false;
        } else {
            $this->db->where_in('ID', $tid);
            $this->db->delete('event_track');
            return true;
        }
    }	
}

?>
