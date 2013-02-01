<?php
/**
 * Invite list model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Invite list model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Invite_list_model extends Model
{
    /**
     * Get the current invites for an event and their status
     *
     * @param integer $eid Event id
     *
     * @return mixed
     */
    public function getEventInvites($eid)
    {
        $sql = sprintf(
            "
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
        ", $this->db->escape($eid)
        );
        $q   = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Checks if a user is invited to an event
     *
     * @param integer $eid         Event id
     * @param integer $uid         User id
     * @param boolean $only_accept If only accepted invites should be found
     *
     * @return bool
     */
    public function isInvited($eid, $uid, $only_accept = true)
    {
        $arr = array('eid' => $eid, 'uid' => $uid);
        if ($only_accept) {
            $arr['accepted'] = 'Y';
        }
        $q   = $this->db->get_where('invite_list', $arr);
        $ret = $q->result();

        return (isset($ret[0])) ? true : false;
    }

    /**
     * Retrieves an invite from the database
     *
     * @param integer $eid Event id
     * @param integer $uid User id
     *
     * @return mixed
     *
     */
    public function getInvite($eid, $uid)
    {
        $q = $this->db->get_where(
            'invite_list',
            array('eid' => $eid, 'uid' => $uid)
        );

        return $q->result();
    }

    /**
     * Adds an invite to the database
     *
     * @param integer $eid    Event id
     * @param integer $uid    User id
     * @param boolean $status Accepted status
     *
     * @return void
     */
    public function addInvite($eid, $uid, $status = null)
    {
        // Be sure there's not another one first...
        if ($this->getInvite($eid, $uid)) {
            return false;
        }
        $arr = array(
            'eid'        => $eid,
            'uid'        => $uid,
            'date_added' => time(),
            'accepted'   => $status
        );
        $this->db->insert('invite_list', $arr);
    }

    /**
     * Deletes an invite from the database
     *
     * @param integer $eid Event id
     * @param integer $uid User id
     *
     * @return void
     */
    public function removeInvite($eid, $uid)
    {
        $this->db->delete('invite_list', array('eid' => $eid, 'uid' => $uid));
    }

    /**
     * Updates an invite status in the database
     *
     * @param integer $eid  Event id
     * @param integer $uid  User id
     * @param boolean $stat Invite status
     *
     * @return void
     */
    public function updateInviteStatus($eid, $uid, $stat)
    {
        $arr = array('accepted' => $stat);
        $this->db->where('eid', $eid);
        $this->db->where('uid', $uid);
        $this->db->update('invite_list', $arr);
    }

    /**
     * Accepts an invitation on behalf of a user
     *
     * @param integer $eid Event id
     * @param integer $uid User id
     *
     * @return void
     */
    public function acceptInvite($eid, $uid)
    {
        $this->updateInviteStatus($eid, $uid, 'Y');
    }
}
