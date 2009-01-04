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
	function event($act=null){
		$this->load->library('service');
		//$data=array('action'=>$act,'data'=>array('foo','bar'));
		$data=file_get_contents('php://input');
		$ret=array('data'=>$this->service->handle('event',$data));
		$this->load->view('api/out',$ret);
		
	}
	function talk($act=null){
		$this->load->library('service');
		$data=file_get_contents('php://input');
		$ret=array('data'=>$this->service->handle('talk',$data));
		$this->load->view('api/out',$ret);
	}
	function comment($act=null){
		$this->load->library('service');
		$data=file_get_contents('php://input');
		$ret=array('data'=>$this->service->handle('comment',$data));
		$this->load->view('api/out',$ret);
	}
	
	//---------------------
	function tz($cont){
		$this->load->model('tz_model');
		
		//$out=$this->tz_model->getAreaInfo($cont);
		$out=$this->tz_model->getOffsetInfo($cont);
		echo json_encode($out);
	}
	
}