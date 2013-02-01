<?php
/**
 * Event tag model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Event tag model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Tags_events_model extends Model
{
    /**
     * Add a tag to an event. Checks against "tags" table
     * to see if tag exists - if so, links. if not, adds.
     *
     * @param integer $eventId  Event ID
     * @param string  $tagValue Tag value
     *
     * @return integer $insertId Last insert ID
     */
    public function addTag($eventId, $tagValue)
    {
        // Invalid tag value, do not save tag
        if (empty($tagValue) || !preg_match('/^[a-zA-Z0-9]+$/', trim($tagValue))) {
            return false;
        }
        // normalize
        $tagValue = trim(strtolower($tagValue));

        // see if we already have the tag for this event
        $this->db->select('tags.id')
            ->from('tags_events')
            ->join('tags', 'tags_events.tag_id = tags.id')
            ->where(
                array(
                     'event_id'  => $eventId,
                     'tag_value' => $tagValue
                )
            );
        $hasTag = (bool)$this->db->get()->result();
        if ($hasTag) {
            // we already have it - don't add!
            return true;
        }

        // check to see if the tag exists first...
        if ($tagRecordId = $this->isTagInUse($tagValue)) {
            // if it exists, just use the tag ID to link
            $tagId = $tagRecordId[0]->ID;
        } else {
            // if not we need to add it to the "Tags" table too
            $CI = & get_instance();
            $CI->load->model('tags_model', 'tagsModel');

            $tagId = $CI->tagsModel->addTag(trim($tagValue));
        }

        $this->db->insert(
            'tags_events', array(
                                'event_id' => $eventId,
                                'tag_id'   => $tagId
                           )
        );

        return $this->db->insert_id();
    }

    /**
     * Removes a tag from an event. Checks with talk tags
     * to ensure it's not in use before removing.
     * If $tagId value is null, *all* tags removed for given event
     *
     * @param integer $eventId Event ID
     * @param integer $tagId   Tag ID
     *
     * @return null
     */
    public function removeTag($eventId, $tagId = null)
    {
        $where = array('event_id' => $eventId);
        if ($tagId != null) {
            $where['ID'] = $tagId;
        }
        $this->db->delete('tags_events', $where);
    }

    /**
     * Remove tags other than the ones specified
     *
     * @param int   $eventId      Event ID #
     * @param array $tagsToRemove List of tags to remove
     *
     * @return null
     */
    public function removeUnusedTags($eventId, $tagsToRemove)
    {
        $CI = & get_instance();
        $CI->load->model('tags_model', 'tagsModel');

        foreach ($tagsToRemove as $tag => $detail) {
            if ($tagData = $CI->tagsModel->tagExists($tag)) {
                $CI->tagsModel->removeTag($eventId, $tagData[0]->ID);
            }
        }
    }

    /**
     * Checks event tag list to see if it's in use by an event
     *
     * @param string $tagValue       Tag value
     * @param mixed  $excludeEventId [optional] Event ID(s) to exclude,
     *                               string or array
     *
     * @return mixed $tagDetail If tag value is found, returns. otherwise, false
     */
    public function isTagInUse($tagValue, $excludeEventId = null)
    {
        $CI = & get_instance();
        $CI->load->model('tags_model', 'tagsModel');

        if ($tagDetail = $CI->tagsModel->tagExists($tagValue)) {
            return $tagDetail;
        } else {
            return false;
        }
    }

    /**
     * Get the event's current tags
     *
     * @param int $eventId Event ID #
     *
     * @return array Tag information
     */
    public function getTags($eventId)
    {
        $this->db->select('*')
            ->from('tags_events')
            ->join('tags', 'tags_events.tag_id = tags.id')
            ->where('tags_events.event_id = ' . $eventId)
            ->order_by('tags.tag_value', 'asc');

        return $this->db->get()->result();
    }
}
