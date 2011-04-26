<?php

/**
 * Model for handling the "tags" table for tagging events and talks
 *
 * @package Joind.in
 * @author Chris Cornutt <ccornutt@phpdeveloper.org>
 */
class Tags_model extends Model
{
	/**
	 * Check to see if tag exists in the table
	 *
	 * @param string $tagValue Tag value
	 * @return mixed $tagDetail If tag found, return details. Otherwise, false.
	 */
	public function tagExists($tagValue)
	{
		$result = $this->db->select('tags',array(
			'tag_value' => $tagValue
		))->result();
		
		var_dump($result);
		return false;
	}
	
	/**
	 * Add a tag to the tags table
	 * Duplicate check done prior to insert
	 *
	 * @param string $tagValue Tag value
	 * @return integer $insertId Last insert ID (or found ID)
	 */
	public function addTag($tagValue)
	{
		$result = $this->tagExists($tagValue);
		if($result){
			return $result->id;
		}else{
			$this->db->insert('tags',array(
				'tag_value' => $tagValue
			));
			return $this->db->insert_id();
		}
	}
}

?>