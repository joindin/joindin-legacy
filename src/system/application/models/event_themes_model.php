<?php
/**
 * Event themes model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Event themes model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Event_themes_model extends Model
{
    /**
     * Check to see if the given user has access to a certain theme/event
     *
     * @param integer $uid      User ID
     * @param integer $theme_id Theme ID
     *
     * @return boolean Allowed/not allowed
     */
    public function isAuthTheme($uid, $theme_id)
    {
        foreach ($this->getUserThemes($uid) as $theme) {
            if ($theme->ID == $theme_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Grab all themes that are linked to an event this user
     * is an admin for
     *
     * @param integer $uid [optional] User ID (if not given, tries to pull from
     *                     session)
     *
     * @return array Theme information
     */
    public function getUserThemes($uid = null)
    {
        $event_ids = array();
        if (!$uid) {
            //try to get the user info from the session
            $uid = $this->session->userdata('ID');
            if (empty($uid)) {
                return false;
            }
        }
        // get the events the user is an admin for
        $q = $this->db->get_where(
            'user_admin',
            array('uid' => $uid, 'rtype' => 'event')
        );
        foreach ($q->result() as $event) {
            $event_ids[] = $event->rid;
        }

        if (empty($event_ids)) {
            return array();
        }
        $this->db->select('event_themes.*, events.event_name')
            ->from('event_themes')
            ->join('events', 'events.ID=event_themes.event_id')
            ->where_in('event_id', $event_ids);
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Get the active theme for an event
     *
     * @param integer $event_id Event ID
     *
     * @return mixed Returns either the theme information or false
     *   if no theme is found
     */
    public function getActiveTheme($event_id)
    {
        $arr = array(
            'active'   => 1,
            'event_id' => $event_id
        );
        $q   = $this->db->get_where('event_themes', $arr);
        $ret = $q->result();

        return (!empty($ret)) ? $ret : false;
    }

    /**
     * Add a new theme for a given event
     * Involves database change and file(s) upload
     *
     * @param array $data Event date from the frontend
     *
     * @return integer Last insert ID
     */
    public function addEventTheme($data)
    {
        $this->db->insert('event_themes', $data);

        return $this->db->insert_id();
    }

    /**
     * Update the given theme with new data/file(s)
     *
     * Does nothing, completely empty
     *
     * @param integer $theme_id Theme ID
     * @param array   $data     Theme data to update record with
     *
     * @return void
     */
    public function saveEventTheme($theme_id, $data)
    {

    }

    /**
     * Remove the given theme
     *
     * @param integer $theme_id Theme ID number to remove
     *
     * @return void
     */
    public function deleteEventTheme($theme_id)
    {
        $this->db->delete('event_themes', array('ID' => $theme_id));
    }

    /**
     * Turns on a given theme for an event
     * NOTE: All others for the event will be disabled
     *
     * @param integer $theme_id Theme ID number to enable
     * @param integer $event_id Event Id
     *
     * @return void
     */
    public function activateTheme($theme_id, $event_id)
    {
        $this->db->where('ID', $theme_id);
        $this->db->update('event_themes', array('active' => 1));

        // deactivate all the rest
        $this->db->where(array('ID !=' => $theme_id, 'event_id' => $event_id));
        $this->db->update('event_themes', array('active' => 0));
    }
}

