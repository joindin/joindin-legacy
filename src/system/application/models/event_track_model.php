<?php
/**
 * Event track model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Event track model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Event_track_model extends Model
{
    /**
     * Retrieves event tracks from the database
     *
     * @param integer $eid Event Id
     *
     * @return mixed
     */
    public function getEventTracks($eid)
    {
        $q   = $this->db->get_where('event_track', array('event_id' => $eid));
        $ret = $q->result();
        foreach ($ret as $k => $tr) {
            $q             = $this->db->query(
                'select count(ID) ct from talk_track where track_id=' .
                $this->db->escape($tr->ID)
            );
            $u             = $q->result();
            $ret[$k]->used = $u[0]->ct;
        }

        return $ret;
    }

    /**
     * Retrieves the sessions for a track
     *
     * @param integer $tid Track id
     *
     * @return mixed
     */
    public function getTrackSessions($tid)
    {

        $sql = sprintf(
            "
            select
                t.talk_title,
                t.ID
            from
                talks t,
                talk_track tt
            where
                tt.track_id=%s and
                tt.talk_id=t.ID
        ", $this->db->escape($tid)
        );

        //$q=$this->db->get_where('talks', array('event_track_id'=>$tid));
        $q = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Adds an event track to the database
     *
     * @param integer $eid  Event id
     * @param string  $name Track name
     * @param string  $desc Track description
     *
     * @return void
     */
    public function addEventTrack($eid, $name, $desc)
    {
        $arr = array(
            'event_id'   => $eid,
            'track_name' => $name,
            'track_desc' => $desc
        );
        $this->db->insert('event_track', $arr);
    }

    /**
     * Updates an event track
     *
     * @param integer $tid Track id
     * @param array   $arr Array of stuff
     *
     * @return void
     */
    public function updateEventTrack($tid, $arr)
    {
        $this->db->where('ID', $tid);
        $this->db->update('event_track', $arr);
    }

    /**
     * Deletes an event track
     *
     * @param integer $tid Track id
     *
     * @return bool
     */
    public function deleteEventTrack($tid)
    {
        //Be sure there's no sessions associated with it first
        if (count($this->getTrackSessions($tid)) > 0) {
            return false;
        } else {
            $this->db->where_in('ID', $tid);
            $this->db->delete('event_track');

            return true;
        }
    }
}

