<?php

class Event_themes_model extends Model {

	public function Event_themes_model(){
		parent::Model();
	}

	/**
	 * Grab all themes that are linked to an event this user
	 * is an admin for
	 * @param integer uid[optional] User ID (if not given, tries to pull from session)
	 */
	public function getUserThemes($uid=null){
		$event_ids	= array();
		if(!$uid){
			//try to get the user info from the session
			$uid=$this->session->userdata('ID');
			if(empty($uid)){ return false;}
		}
		// get the events the user is an admin for
		$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rtype'=>'event'));
		foreach($q->result() as $event){ $event_ids[]=$event->rid; }

		$this->db->select('*')
			->from('event_themes')
			->where_in('event_id',$event_ids);
		$q=$this->db->get();
		return $q->result();
	}
	
	/**
	 * Add a new theme for a given event
	 * Involves database change and file(s) upload
	 */
	public function addEventTheme($data){
		
	}
	
	/**
	 * Update the given theme with new data/file(s)
	 */
	public function saveEventTheme($theme_id,$data){
		
	}
	
	/**
	 * Remove the given theme
	 * @param integer $theme_id Theme ID number to remove
	 */
	public function deleteEventTheme($theme_id){
		
	}
	
	/**
	 * Turns on a given theme for an event
	 * NOTE: All others for the event will be disabled
	 * 
	 * @param integer $theme_id Theme ID number to enable
	 */
	public function activateTheme($theme_id,$event_id){
		$this->db->where('ID',$theme_id);
		$this->db->update('event_themes',array('active'=>1));
		
		// deactivate all the rest
		$this->db->where(array('ID 1='=>$theme_id,'event_id'=>$event_id));
		$this->db->update('event_themes',array('active'=>0));
	}
}

?>