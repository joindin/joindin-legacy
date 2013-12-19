<?php
/**
 * Event comments model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Event comments model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Event_comments_model extends Model
{
    /**
     * Determines if a comment is unique
     *
     * @param array $data Comment data
     *
     * @return bool
     */
    public function isUnique($data)
    {
        $q   = $this->db->get_where('event_comments', $data);
        $ret = $q->result();

        return (empty($ret)) ? true : false;
    }

    /**
     * Retrieves the comments for an event
     *
     * @param integer $eid Event id
     *
     * @return mixed
     */
    public function getEventComments($eid)
    {
        $this->db->from('event_comments');
        $this->db->where('event_id', $eid);
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Retrieves a user's comments
     *
     * @param integer $uid User id
     *
     * @return mixed
     */
    public function getUserComments($uid)
    {
        $this->db->from('event_comments');
        $this->db->where('user_id', $uid);
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Deletes a comment
     *
     * @param integer $cid Comment id
     *
     * @return void
     */
    public function deleteComment($cid)
    {
        $this->db->delete('event_comments', array('id' => $cid));
    }

    /**
     * Retrieves comment details
     *
     * @param integer $cid Comment id
     *
     * @return mixed
     */
    public function getCommentDetail($cid)
    {
        $q = $this->db->get_where('event_comments', array('ID' => $cid));

        return $q->result();
    }

    /**
     * Retrieves the number of comments on an event from a given user id
     *
     * @param integer $uid User id
     *
     * @return mixed
     */
    public function getUserCommentCount($uid)
    {
        $this->db->select('count(*) as "n"');
        $this->db->from('event_comments');
        $this->db->where('user_id', $uid);
        $q = $this->db->get();
        return $q->row()->n;
    }
}

