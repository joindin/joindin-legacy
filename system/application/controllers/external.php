<?php

class External extends Controller {
	
	public function External(){
		parent::Controller();
	}
	
	public function twitter_event_add(){
		if(!defined('IS_CRON')){ return false; }
		
		//http://conf.localhost/event/hot
		$this->load->library('twitter');
		$this->load->model('event_model');
		
		$events=$this->event_model->getUpcomingEvents(null);
		
		$msg="Joind.in Update: There's ".count($events)." great events coming up soon! ";
		$msg.="Check them out! http://joind.in/event/upcoming";
		
		$resp=$this->twitter->sendMsg($msg);
	}
}

?>