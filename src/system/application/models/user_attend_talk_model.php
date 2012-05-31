<?php

class User_attend_talk_model extends Model {

    function User_attend_talk_model() {
        parent::Model();
    }
    //--------------

    /**
     * Check to see if the given user ID is attending the talk
     *
     * @param integer $uid User ID
     * @param integer $tid Talk ID
     * @return boolean
     */
    function chkAttend($uid, $tid) {
        $q=$this->db->get_where('user_attend_talk', array('uid'=>$uid,'tid'=>$tid));
        $ret=$q->result();
        return (empty($ret)) ? false : true;
    }

    /**
     * Toggle the attending status for a user on an talk
     *
     * @param integer $uid User ID
     * @param integer $tid Talk ID
     * @return null
     */
    function chgAttendStat($uid, $tid) {
        if ($this->chkAttend($uid, $tid)) {
            //they are attending, remove them
            $this->db->delete('user_attend_talk', array('uid'=>$uid,'tid'=>$tid));
        } else {
            //they're not attending, add them
            $this->db->insert('user_attend_talk', array('uid'=>$uid,'tid'=>$tid));
        }
    }

    /**
     * Get a total count of those marked as attending on the talk
     *
     * @param integer $tid Talk ID
     * @return integer Count of attendees
     */
    function getAttendCount($tid) {
        $sql='select count(ID) attend_ct from user_attend_talk where tid='.$this->db->escape($tid);
        $query = $this->db->query($sql);
        $countResult = $query->result();
        return (isset($countResult[0]->attend_ct)) ? $countResult[0]->attend_ct : 0;
    }

    /**
     * Get the list of users attending a talk
     *
     * @param integer $tid Talk ID
     * @return array User details
     */
    function getAttendUsers($tid) {
        $this->db->distinct();
        $this->db->select('user.ID, user.username, user.full_name');
        $this->db->from('user');
        $this->db->where('user_attend_talk.tid', $tid);
        $this->db->join('user_attend_talk','user.ID=user_attend_talk.uid');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Given a talk ID, find those that are marked as attended/attended
     *
     * @param integer $tid Talk Id
     * @return array List of attending users
     */
    function getAttendees($tid) {

        $sql=sprintf('
            select
                usr.ID,
                usr.username,
                usr.full_name,
                (select
                    count(ts.ID)
                from
                    talk_speaker ts,
                    talks t
                where
                    t.ID = ts.talk_id and
                    IFNULL(ts.status, 0) != \'pending\' and
                    ts.speaker_id = usr.ID and
                    t.talk_id = %s
                ) is_speaker
            from
                user_attend_talk ua,
                user usr
            where
                ua.uid=usr.ID and
                ua.tid=%s
            order by
                usr.full_name asc
        ', $this->db->escape((int)$tid), $this->db->escape((int)$tid));

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get the list of talks that the user is attending/has attended
     *
     * @param integer $uid User ID
     * @return array Talk details
     */
    function getUserAttending($uid) {
        $this->db->select('talks.talk_title, talks.ID, talks.date_given');
        $this->db->from('talks');
        $this->db->join('user_attend_talk','user_attend_talk.tid=talks.ID');
        $this->db->where('user_attend_talk.uid',(int)$uid);
        $this->db->order_by('talks.date_given','desc');

        $query = $this->db->get();
        return $query->result();
    }

}
?>
