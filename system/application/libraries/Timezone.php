<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* Class to work with data/time issues for events/talks
*/
class Timezone {
	
	private $CI	= null;
	
	public function __construct(){
		// Create an instance of our CI object
		$this->CI=&get_instance();
	}
	
	/**
	* Return the difference from UTC for my machine
	* @param $tz integer Timezone string
	*/
	private function _getMyTimeDiff($tz=null){
		if(!$tz){ $tz=date_default_timezone_get(); }
		$here	= new DateTimeZone($tz);
		$there	= new DateTimeZone('UTC');
		$offset	= $here->getOffset(new DateTime("now",$here))-$there->getOffset(new DateTime("now",$there));
		
		return $offset;
	}
	/** 
	* Find the local time at the event...
	* @param $evt_offset integer Event's offset from UTC
	*/
	private function _getEvtTime($evt_offset){
		$here	= new DateTimeZone(date_default_timezone_get());
		$hoffset= $here->getOffset(new DateTime("now",$here));
		$off	= (time()-$hoffset)+($evt_offset*3600); 
		return $off;
	}
	//----------------------------
	
	/**
	* Determine if an event has started based on the start time(stamp)
	* and the timezone of the event
	* @param $eid integer Event ID
	* @param $edata[optional] array Event Data
	*/
	public function hasEvtStarted($eid,$edata=null){
		if(!$edata){ 
			$this->CI->load->model('event_model','em');
			$edata=$this->CI->em->getEventDetail($eid);
		}
		$at_event=$this->_getEvtTime($edata[0]->event_tz);
		return ($at_event>=$edata[0]->event_start) ? true : false;
	}
	
	/**
	* Determine if a talk for an event has started based on the time(stamp)
	* of the talk and the timezone of the event it belongs to
	* @param $tid integer Talk ID
	* @param $tdata[optional] array Talk Data
	*/
	public function talkEvtStarted($tid,$tdata=null){
		if(!$tdata){ 
			$this->CI->load->model('talks_model','tm');
			$tdata=$this->CI->tm->getTalks($tid); 
		}
		return $this->hasEvtStarted($tdata[0]->event_id);
	}
	
}

?>