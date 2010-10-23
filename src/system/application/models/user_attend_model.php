<?php
/**
 * User attend model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Models
 * @author    Chris Cornutt <chris@joind.in>
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/**
 * User attend model
 *
 * Model for user attend data
 *
 * @category  Joind.in
 * @package   Models
 * @author    Chris Cornutt <chris@joind.in>
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */
class User_Attend_Model extends Model
{

    /**
     * Constructor
     *
     * @return void
     */
    public function User_Attend_Model()
    {
        parent::Model();
    }

    /**
     * Check whether a user attends an event
     *
     * @param integer $uid user id
     * @param integer $eid event id
     *
     * @return bool
     */
    public function chkAttend($uid, $eid)
    {
        $params = array('uid' => $uid, 'eid' => $eid);
        $q = $this->db->get_where('user_attend', $params);
        $ret = $q->result();
        return (empty($ret)) ? false : true;
    }

    /**
     * Change the attendance status for a user
     *
     * @param integer $uid user id
     * @param integer $eid event id
     *
     * @return void
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
     * Get the amount of users that attends an event
     *
     * @param integer $eid event id
     *
     * @return integer
     */
    public function getAttendCount($eid)
    {
        $sql = 'select
                    count(ID) attend_ct
                from
                    user_attend
                where
                    eid=' . $this->db->escape($eid);
        $q = $this->db->query($sql);
        $res = $q->result();
        return (isset($res[0]->attend_ct)) ? $res[0]->attend_ct : 0;
    }

    /**
     * Get the users that attend the event
     *
     * @param integer $eid event id
     *
     * @return array
     */
    public function getAttendUsers($eid)
    {
        $this->db->distinct();
        $this->db->select('user.ID,user.username,user.full_name');
        $this->db->from('user');
        $this->db->where('user_attend.eid', $eid);
        $this->db->join('user_attend', 'user.ID=user_attend.uid');
        $q = $this->db->get();
        $ret = $q->result();
        return $ret;
    }

    /**
     * Get the attendees
     *
     * @param integer $eid event id
     *
     * @return array
     */
    public function getAttendees($eid)
    {
        $this->db->select('user.*');
        $this->db->from('user_attend');
        $this->db->join('user', 'user.ID = user_attend.uid', 'inner');
        $this->db->where('user_attend.eid', (int)$eid);
        $this->db->order_by('user_attend.ID', 'asc');

        //$q=$this->db->get();
        //return $q->result();

        $sql = sprintf(
            'select
				usr.ID,
				usr.username,
				usr.full_name,
				(select
					count(uad.ID)
				from
					talks t,
					user_admin uad
				where
					uad.uid=usr.ID and
					t.event_id=%s and
					t.ID=uad.rid and uad.rtype=\'talk\' and
					uad.rcode!=\'pending\'
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
        $q = $this->db->query($sql);
        return $q->result();
    }

    /**
     * Get the attending user
     *
     * @param integer $uid user id
     *
     * @return array
     */
    public function getUserAttending($uid)
    {
        $this->db->select(
            'events.event_name,
            events.ID,
            events.event_start,
            events.event_end'
        );
        $this->db->from('events');
        $this->db->join('user_attend', 'user_attend.eid=events.ID');
        $this->db->where('user_attend.uid', (int)$uid);
        $this->db->order_by('events.event_start', 'desc');

        $q = $this->db->get();
        return $q->result();
    }
}

?>