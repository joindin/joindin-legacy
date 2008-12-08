<?php

class Api extends Controller {
	
	function Api(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		//echo 'error';
	}
	//function _output($out){ var_dump($out); echo json_encode($out); }
	//---------------------
	function event(){
		
	}
	function talk(){
		
	}
	function tz($cont){
		$this->load->model('tz_model');
		
		//$out=$this->tz_model->getAreaInfo($cont);
		$out=$this->tz_model->getOffsetInfo($cont);
		echo json_encode($out);
	}
	
}