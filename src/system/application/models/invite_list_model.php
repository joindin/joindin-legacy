<?php

class Invite_list_model extends Model {
    
    function Invite_list_model() {
        parent::Model();
    }
    //------------------
    /**
    * Get the current invites for an event and their status
    */
    function getEventInvites($eid) {
        $sql=sprintf("
            select
                u.username,
                u.ID uid,
                u.full_name,
                il.eid,
                il.date_added,
                il.ID ilid,
                il.accepted
            from
                invite_list il,
                user u
            where
                il.uid=u.ID and
                il.eid=%s
        ", $this->db->escape($eid));
        $q=$this->db->query($sql);
        return $q->result();
    }
    function isInvited($eid, $uid, $only_accept=true) {
        $arr=array('eid'=>$eid,'uid'=>$uid);
        if ($only_accept) { $arr['accepted']='Y'; }
        $q=$this->db->get_where('invite_list', $arr);
        $ret=$q->result();
        return (isset($ret[0])) ? true : false;
    }
    function getInvite($eid, $uid) {
        $q=$this->db->get_where('invite_list', array('eid'=>$eid,'uid'=>$uid));
        return $q->result();
    }
    function addInvite($eid, $uid, $status=null) {
        // Be sure there's not another one first...
        if ($this->getInvite($eid, $uid)) { return false; }
        $arr=array(
            'eid'			=>$eid,
            'uid'			=>$uid,
            'date_added'	=>time(),
            'accepted'		=>$status
        );
        $this->db->insert('invite_list', $arr);
    }
    function removeInvite($eid, $uid) {
        $this->db->delete('invite_list', array('eid'=>$eid,'uid'=>$uid));
    }
    function updateInviteStatus($eid, $uid, $stat) {
        $arr=array('accepted'=>$stat);
        $this->db->where('eid', $eid);
        $this->db->where('uid', $uid);
        $this->db->update('invite_list', $arr);
    }
    function acceptInvite($eid, $uid) {
        $this->updateInviteStatus($eid, $uid,'Y');
    }
}
