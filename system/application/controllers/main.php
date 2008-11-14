<?php

class Main extends Controller {
	
	function Main(){
		parent::Controller();		
		$this->user_model->logStatus();
	}
	function index()){
		$this->load->helper('form');
		$this->load->model('talks_model');
		$this->load->model('event_model');
		
		$arr=array(
			'talks'	=> $this->talks_model->getPopularTalks(),
			'events'=> $this->event_model->getUpcomingEvents(true),
			'logged'=> $this->user_model->isAuth()
		);
		
		$this->template->write_view('content','main/index',$arr,TRUE);
		$this->template->render();
	}
}

?>