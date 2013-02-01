<?php
/**
 * User attend model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * User attend model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class User_attend_model extends Model
{
    /**
     * Check to see if the given user ID is attending the event
     *
     * @param integer $uid User ID
     * @param integer $eid Event ID
     *
     * @return boolean
     */
    public function chkAttend($uid, $eid)
    {
        $q   = $this->db->get_where(
            'user_attend',
            array('uid' => $uid, 'eid' => $eid)
        );
        $ret = $q->result();

        return (empty($ret)) ? false : true;
    }

    /**
     * Toggle the attending status for a user on an event
     *
     * @param integer $uid User ID
     * @param integer $eid Event ID
     *
     * @return null
     */
    public function chgAttendStat($uid, $eid)
    {
        if ($this->chkAttend($uid, $eid)) {
            //they are attending, remove them
            $this->db->delete('user_attend', array('uid' => $uid, 'eid' => $eid));
        } else {
            //they're not attending, add them
            $this->db->insert('user_attend', array('uid' => $uid, 'eid' => $eid));
        }
    }

    /**
     * Get a total count of those marked as attending on the event
     *
     * @param integer $eid Event ID
     *
     * @return integer Count of attendees
     */
    public function getAttendCount($eid)
    {
        $sql         = 'select count(ID) attend_ct from user_attend where eid=' .
            $this->db->escape($eid);
        $query       = $this->db->query($sql);
        $countResult = $query->result();

        return (isset($countResult[0]->attend_ct)) ? $countResult[0]->attend_ct : 0;
    }

    /**
     * Get the list of users attending an event
     *
     * @param integer $eid Event ID
     *
     * @return array User details
     */
    public function getAttendUsers($eid)
    {
        $this->db->distinct();
        $this->db->select('user.ID, user.username, user.full_name');
        $this->db->from('user');
        $this->db->where('user_attend.eid', $eid);
        $this->db->join('user_attend', 'user.ID=user_attend.uid');

        $query = $this->db->get();

        return $query->result();
    }

    /**
     * Given an event ID, find those that are marked as attended/attended
     *
     * @param integer $eid Event Id
     *
     * @return array List of attending users
     */
    public function getAttendees($eid)
    {

        $sql = sprintf(
            '
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
                    t.event_id = %s
                ) is_speaker
            from
                user_attend ua,
                user usr
            where
                ua.uid=usr.ID and
                ua.eid=%s
            order by
                usr.full_name asc
        ', $this->db->escape((int)$eid), $this->db->escape((int)$eid)
        );

        $query = $this->db->query($sql);

        return $query->result();
    }

    /**
     * Get the list of events that the user is attending/has attended
     *
     * @param integer $uid User ID
     *
     * @return array Event details
     */
    public function getUserAttending($uid)
    {
        $this->db->select(
            'events.event_name, events.ID, events.event_start, events.event_end'
        );
        $this->db->from('events');
        $this->db->join('user_attend', 'user_attend.eid=events.ID');
        $this->db->where('user_attend.uid', (int)$uid);
        $this->db->order_by('events.event_start', 'desc');

        $query = $this->db->get();

        return $query->result();
    }

}
