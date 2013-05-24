<?php
/**
 * Tags model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Tags model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Tags_model extends Model
{
    /**
     * Check to see if tag exists in the table
     *
     * @param string $tagValue Tag value
     *
     * @return mixed If tag found, return details. Otherwise, false.
     */
    public function tagExists($tagValue)
    {
        $result = $this->db->get_where(
            'tags',
            array(
                'tag_value' => $tagValue
            )
        )->result();
        
        return (empty($result)) ? false : $result;
    }
    
    /**
     * Add a tag to the tags table
     * Duplicate check done prior to insert
     *
     * @param string $tagValue Tag value
     *
     * @return integer $insertId Last insert ID (or found ID)
     */
    public function addTag($tagValue)
    {
        $result = $this->tagExists($tagValue);
        if ($result) {
            return $result->id;
        } else {
            $this->db->insert(
                'tags',
                array(
                    'tag_value' => $tagValue
                )
            );
            return $this->db->insert_id();
        }
    }

    /**
     * Deletes the association of a tag to an event.
     *
     * @param integer $eventId Event ID
     * @param integer $tagId   Tag ID
     *
     * @return void
     */
    public function removeTag($eventId, $tagId)
    {
        $where = array('event_id'=>$eventId, 'tag_id'=>$tagId);
        $this->db->delete('tags_events', $where);
    }
}

