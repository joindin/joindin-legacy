<?php

class Help extends Controller {
	
	function Help(){
		parent::Controller();
	}
	function index(){
		$this->template->write_view('content','help/main');
		$this->template->render();
	}
	function write_static($view){
		$this->template->write_view('content',$view);
		$this->template->render();
	}
	//---------------------------------
	## Content pages
	function user_guide_events(){ 	$this->write_static('help/user_guide_events'); }
	function user_guide_talks(){ 	$this->write_static('help/user_guide_talks'); }
	function event_admin(){ 		$this->write_static('help/event_admin'); }
}
?>