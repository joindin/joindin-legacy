<?php

/**
 * Model for handling the tagging for talks
 *
 * @package Joind.in
 * @author Chris Cornutt <ccornutt@phpdeveloper.org>
 */
class Tags_talks_model extends Model
{

	/**
	 * Add a tag for the given talk
	 *
	 * @param integer $talkId Talk ID
	 * @param string $tagValue Tag text
	 */
	public function addTag($talkId,$tagValue)
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
			'talk_id' 	=> $talkId,
			'tag_id'	=> $tagId
		));
		return $this->db->insert_id();
	}
	
	/**
	 * Remove a tag (either just the link or the tag too)
	 * If $tagId is not specified, all tags for a talk will be unlinked/removed
	 *
	 * @param integer $talkId Talk ID
	 * @param integer $tagId[optional] Tag ID
	 */
	public function removeTag($talkId,$tagId=null)
	{
		
	}
	
	/**
	 * Check to see if the tag exists in the talks table
	 *
	 * @param string $tagValue Tag text
	 * @param mixed $excludeTalkId[optional] String/array of talk IDs to exclude from find
	 * @return mixed If found, returns tag row in $tagDetail. If not, returns false
	 */
	public function isTagInUse($tagValue, $excludeTalkId=null)
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