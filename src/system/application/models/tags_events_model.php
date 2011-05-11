<?php

/**
 * Model for handling the tagging for events
 *
 * @package Joind.in
 * @author Chris Cornutt <ccornutt@phpdeveloper.org>
 */
class Tags_events_model extends Model
{
	/**
	 * Add a tag to an event. Checks against "tags" table
	 * to see if tag exists - if so, links. if not, adds.
	 *
	 * @param integer $eventId Event ID
	 * @param string $tagValue Tag value
	 * @return integer $insertId Last insert ID
	 */
	public function addTag($eventId,$tagValue)
	{
		// check to see if the tag exists first...
		if($tagRecordId = $this->isTagInUse($tagValue)){
			// if it exists, just use the tag ID to link
			$tagId = $tagRecordId->id;
		}else{
			// if not we need to add it to the "Tags" table too
			$CI = &get_instance();
			$CI->load->model('tags_model','tagsModel');
			
			$tagId = $ci->tagsModel->addTag($tagValue);
		}
		
		$this->db->insert('tags_talks',array(
			'event_id' 	=> $talkId,
			'tag_id'	=> $tagId
		));
		return $this->db->insert_id();
	}
	
	/**
	 * Removes a tag from an event. Checks with talk tags
	 * to ensure it's not in use before removing. 
	 * If $tagId value is null, *all* tags removed for given event
	 *
	 * @param integer $eventId Event ID
	 * @param integer $tagId Tag ID
	 * @return null
	 */
	public function removeTag($eventId,$tagId=null)
	{
		$where = array('event_id' => $eventId);
		if($tagId != null){
			$where['ID'] = $tagId;
		}
		$this->db->delete('tags_events',$where);
	}
	
	/**
	 * Checks event tag list to see if it's in use by an event
	 *
	 * @param string $tagValue Tag value
	 * @param mixed $excludeEventId[optional] Event ID(s) to exclude, string or array
	 * @return mixed $tagDetail If tag value is found, returns. otherwise, false
	 */
	public function isTagInUse($tagValue,$excludeEventId=null)
	{
		$CI = &get_instance();
		$CI->load->model('tags_model','tagsModel');
		
		if($tagDetail=$CI->tagsModel->tagExists($tagValue)){
			return $tagDetail;
		}else{
			return false;
		}
	}
}

?>