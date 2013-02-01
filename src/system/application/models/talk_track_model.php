<?php
/**
 * Talk track model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Talk track model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Talk_track_model extends Model
{

    /** constructor */
    function Talk_track_model()
    {
        parent::Model();
    }

    /**
     * Fetch the track information for a given talk
     *
     * @param integer $sid Talk ID
     *
     * @return array Track Info
     */
    public function getSessionTrackInfo($sid)
    {
        $sql = sprintf(
            '
            select
                et.track_name,
                et.ID,
                et.track_desc,
                et.track_color
            from
                talk_track tt,
                event_track et
            where
                tt.talk_id=%s and
                tt.track_id=et.ID
        ', $this->db->escape($sid)
        );
        $q   = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Add a track record to the talk
     *
     * @param integer $sid Talk ID
     * @param integer $tid Track ID
     *
     * @return void
     */
    public function setSessionTrack($sid, $tid)
    {
        $arr = array(
            'talk_id'  => $sid,
            'track_id' => $tid
        );
        $this->db->insert('talk_track', $arr);
    }

    /**
     * Update track information for a talk
     *
     * @param integer $sid      Talk ID
     * @param integer $curr_tid Current Track ID
     * @param integer $tid      Track ID
     *
     * @return void
     */
    public function updateSessionTrack($sid, $curr_tid, $tid)
    {
        // first be sure we have one to begin with
        $st = $this->getSessionTrackInfo($sid);
        if (empty($st) || $curr_tid == null) {
            $this->setSessionTrack($sid, $tid);
        } else {
            $this->db->where('talk_id', $sid);
            $this->db->where('track_id', $curr_tid);
            $this->db->update('talk_track', array('track_id' => $tid));
        }
    }

    /**
     * Delete the track for a given talk ID
     * If the track ID is not specified, removes all tracks for a talk
     *
     * @param integer $sid Talk ID
     * @param integer $tid [optional] Track ID
     *
     * @return void
     */
    public function deleteSessionTrack($sid, $tid = null)
    {
        $arr = array(
            'talk_id' => $sid
        );
        if ($tid) {
            $arr['track_id'] = $tid;
        }
        $this->db->delete('talk_track', $arr);
    }
}

