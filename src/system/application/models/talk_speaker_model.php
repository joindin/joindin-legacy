<?php
/**
 * Talk speaker model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Talk speaker model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Talk_speaker_model extends Model
{
    /**
     * Locate a speaker by talk ID and speaker name (string match)
     *
     * @param integer $talk_id      Talk ID #
     * @param string  $speaker_name Speaker name
     *
     * @return mixed
     */
    private function _speakerExists($talk_id, $speaker_name)
    {
        $find = array(
            'talk_id'      => $talk_id,
            'speaker_name' => $speaker_name
        );
        $q    = $this->db->get_where('talk_speaker', $find);

        return $q->result();
    }

    /**
     * Add or update speaker information to the table
     * If speaker is found, consider it an update
     *
     * @param integer $talk_id      Talk ID #
     * @param array   $speaker_data Speaker information to insert
     *
     * @return null
     */
    public function handleSpeakerData($talk_id, array $speaker_data = null)
    {
        if (!is_array($speaker_data)) {
            $speaker_data = array($speaker_data);
        }
        $speaker_names = array();

        foreach ($speaker_data as $speaker) {

            $data = array(
                'talk_id'      => $talk_id,
                'speaker_name' => $speaker
            );
            if (empty($speaker)) {
                continue;
            }

            if (!empty($speaker)) {
                $speaker_names[] = $speaker;
            }
            $speaker_row = $this->_speakerExists($talk_id, $speaker);
            if ($speaker_row) {
                //Update the current information
                $this->db->update(
                    'talk_speaker',
                    $data,
                    array('ID' => $speaker_row[0]->ID)
                );
            } else {
                // Add the new speaker
                $this->db->insert('talk_speaker', $data);
            }
        }

        // Now lets find the ones that aren't in our list and remove them
        // This means we can't delete the last speaker on a
        // talk...that's a good thing!
        if (!empty($speaker_names)) {
            $this->db->where_not_in('speaker_name', $speaker_names);
            $this->db->where('talk_id', $talk_id);
            $this->db->delete('talk_speaker');
        }
    }

    /**
     * Return the information for the given talk ID
     *
     * @param integer $talk_id Talk ID #
     * @param boolean $showAll [optional] Switch to have method return
     *                         all, no matter the status
     *
     * @return array $speaker Speaker data
     */
    public function getTalkSpeakers($talk_id, $showAll = false)
    {
        $this->db->select('talk_speaker.*, user.email')
            ->from('talk_speaker')
            ->join('user', 'talk_speaker.speaker_id=user.ID', 'LEFT')
            ->where(array('talk_id' => $talk_id));
        $query    = $this->db->get();
        $speakers = $query->result();

        if ($showAll == true) {
            return $speakers;
        } else {
            // if the status isn't null, remove the speaker_id
            foreach ($speakers as $speakerIndex => $speaker) {
                if ($speaker->status != null) {
                    $speakers[$speakerIndex]->speaker_id = null;
                }
            }
        }

        return $speakers;
    }

    /**
     * Delete a speaker from the table
     *
     * @param integer $talk_id      Talk ID #
     * @param string  $speaker_name Speaker name
     *
     * @return null
     */
    public function deleteSpeaker($talk_id, $speaker_name)
    {
        $where = array(
            'talk_id'      => $talk_id,
            'speaker_name' => $speaker_name
        );
        $this->db->delete('talk_speaker', $where);
    }

    /**
     * Unlink a speaker claim from a talk
     *
     * @param int $talk_id    Talk ID
     * @param int $speaker_id Speaker ID
     *
     * @return null
     */
    public function unlinkSpeaker($talk_id, $speaker_id)
    {
        $data = array('speaker_id' => null);
        $this->db->update(
            'talk_speaker', $data,
            array('speaker_id' => $speaker_id, 'talk_id' => $talk_id)
        );
    }

    /**
     * Find all speakers for a given talk ID #
     *
     * @param integer $talk_id Talk ID #
     * @param boolean $showAll Not used
     *
     * @return array Speaker information
     */
    public function getSpeakerByTalkId($talk_id, $showAll = false)
    {

        $this->db->select(
            'talk_id, speaker_name, talk_speaker.ID, email, ' .
            'speaker_id, status, full_name'
        );
        $this->db->from('talk_speaker');
        $this->db->where('talk_id', $talk_id);
        $this->db->distinct();

        $this->db->join('user', 'user.ID=talk_speaker.speaker_id', 'left');
        $result = $this->db->get();
        $ret    = $result->result();

        // For some reason there's no matching names....just get the speakers
        if (empty($ret)) {
            $result = $this->db
                ->get_Where('talk_speaker', array('talk_id' => $talk_id));
            $ret    = $result->result();
        }

        return $ret;
    }

    /**
     * Find if a talk has been claimed, returns false if not
     * otherwise, returns a count of current claims
     *
     * @param integer $talk_id       Talk ID #
     * @param boolean $claimComplete Is claim complete
     *
     * @return mixed Either boolean or integer
     */
    public function isTalkClaimed($talk_id, $claimComplete = false)
    {
        $query  = $this->db->get_where('talk_speaker', array('talk_id' => $talk_id));
        $result = $query->result();

        $totalCount   = count($result);
        $totalClaimed = 0;

        foreach ($result as $speaker) {
            if ($speaker->speaker_id != null && $speaker->status != 'pending') {
                $totalClaimed++;
            }
        }
        if ($claimComplete == true) {
            return ($totalClaimed == $totalCount) ? true : false;
        } else {
            return ($totalClaimed > 0) ? $totalClaimed : false;
        }
    }

    /**
     * Check to see if the given user has permissions (claimed)
     * the talk ID
     *
     * @param integer $user_id User ID
     * @param integer $talk_id Talk ID
     *
     * @return boolean
     */
    public function hasPerm($user_id, $talk_id)
    {
        $query  = $this->db->get_where(
            'talk_speaker', array(
                                 'talk_id'             => $talk_id,
                                 'speaker_id'          => $user_id,
                                 'IFNULL(status,0) !=' => 'pending'
                            )
        );
        $result = $query->result();

        return (count($result) > 0) ? true : false;
    }

}

