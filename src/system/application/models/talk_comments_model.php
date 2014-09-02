<?php
/**
 * Talk comments model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Talk comments model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Talk_comments_model extends Model
{
    /**
     * Check to see if a talk with the given data already exists
     *
     * @param array $data Talk data
     *
     * @return boolean Is or Isn't Unique
     */
    public function isUnique($data)
    {
        $q   = $this->db->get_where('talk_comments', $data);
        $ret = $q->result();

        return (empty($ret)) ? true : false;
    }

    /**
     * Check to see if the user has commented on a given talk
     *
     * @param integer $talk_id Talk ID
     * @param integer $user_id User ID
     *
     * @return boolean
     */
    public function hasUserCommented($talk_id, $user_id)
    {
        $data = array('talk_id' => $talk_id, 'user_id' => $user_id);
        $q    = $this->db->get_where('talk_comments', $data);
        $ret  = $q->result();

        return (!empty($ret)) ? true : false;
    }

    /**
     * Get the comments for a specific user ID
     *
     * @param integer $uid User ID
     *
     * @return array Comment data
     */
    public function getUserComments($uid)
    {
        $this->db->from('talk_comments');
        $this->db->where('user_id', $uid);
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Remove a comment given its ID in the table
     *
     * @param integer $cid Comment ID
     *
     * @return null
     */
    public function deleteComment($cid)
    {
        $this->db->delete('talk_comments', array('id' => $cid));
    }

    /**
     * Get the details on a comment given its ID
     *
     * @param integer $cid Comment ID
     *
     * @return array Comment data
     */
    public function getCommentDetail($cid)
    {
        $q = $this->db->get_where('talk_comments', array('ID' => $cid));

        return $q->result();
    }

    /**
     * Fetch comments for a given event ID
     *
     * @param integer $event_id Event ID
     *
     * @return array Talk comment details
     */
    public function getEventComments($event_id)
    {
        $sql = sprintf(
            '
            select
                t.ID as talk_id,
                tc.ID as comment_id,
                tc.rating
            from
                talks t,
                talk_comments tc
            where
                t.event_id=%s and
                t.ID=tc.talk_id;
        ', $this->db->escape($event_id)
        );
        $q   = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Fetch comments (with all details) for all talks for a given event
     *
     * @param integer $event_id Event ID
     * @param integer $limit    Limit number of comments to fetch
     * @param integer $offset   Offset to fetch from
     *
     * @return array The comments, from database
     */
    public function getEventTalkComments($event_id, $limit = null, $offset = null)
    {
        $this->load->library('gravatar');
        $this->load->library('timezone');

        $limit_sql = 'limit ' . (int)$limit;
        if (!empty($offset)) {
            $limit_sql .= ' offset ' . (int)$offset;
        }
        $sql      = sprintf(
            '
            select
                tc.talk_id,
                tc.rating,
                tc.comment,
                tc.date_made,
                tc.ID,
                tc.private,
                tc.active,
                tc.user_id,
                u.username uname,
                u.full_name,
                u.twitter_username twitter_username,
                tc.comment_type,
                tc.source,
                e.event_tz_cont,
                e.event_tz_place,
                t.ID tid,
                t.talk_title,
                t.speaker,
                t.owner_id
            from
                talk_comments tc
            left join
                user u on u.ID = tc.user_id
            inner join
                talks t on t.ID = tc.talk_id
            inner join
                events e on e.ID = t.event_id
            where
                tc.active=1 and
                tc.private=0 and
                t.event_id=%s
            order by tc.date_made desc
            %s
        ', $this->db->escape($event_id), $limit_sql
        );
        $q        = $this->db->query($sql);
        $comments = $q->result();

        // calculate the timezone once, use repeatedly
        $tz = 'UTC'; // default
        if (is_array($comments)) {
            $pick_one = current($comments);
            if (   !empty($pick_one->event_tz_cont)
                && !empty($pick_one->event_tz_place)
            ) {
                $tz = $pick_one->event_tz_cont . '/' . $pick_one->event_tz_place;
            }
        }

        foreach ($comments as $k => $comment) {
            // add in the gravatar image
            $comments[$k]->gravatar
                = $this->gravatar->displayUserImage($comment->user_id, null, 45);

            // give a displayable date correct for event timezone
            $comment_datetime = $this->timezone
                ->getDatetimeFromUnixtime($comment->date_made, $tz);
            $comments[$k]->display_datetime
                              = $comment_datetime->format('d.M.Y \a\t H:i');
        }

        return $comments;
    }


    /**
     * Retrieves the number of comments on a talk given a user id
     *
     * @param integer $uid User id
     *
     * @return integer
     */
    public function getUserCommentCount($uid)
    {
        $this->db->select('count(*) as "n"');
        $this->db->from('talk_comments');
        $this->db->where('user_id', $uid);
        $q = $this->db->get();
        return $q->row()->n;
    }
}
