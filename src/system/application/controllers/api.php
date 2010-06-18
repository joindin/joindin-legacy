<?php

class Api extends Controller {
	
	function Api(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		//show our docs
		$this->template->write_view('content','api/doc');
		$this->template->render();
	}
	//function _output($out){ var_dump($out); echo json_encode($out); }
	//---------------------
	function event($act=null){
		$this->load->library('service');
		//$data=array('action'=>$act,'data'=>array('foo','bar'));
		$data=file_get_contents('php://input');
		$ret=array('out'=>$this->service->handle('event',$data));
		$this->output($ret);
	}
	function talk($act=null){
		$this->load->library('service');
		$data=file_get_contents('php://input');
		$ret=array('out'=>$this->service->handle('talk',$data));
		$this->output($ret);
	}
	function comment($act=null){
		$this->load->library('service');
		$data=file_get_contents('php://input');
		$ret=array('out'=>$this->service->handle('comment',$data));
		$this->output($ret);
	}
	function blog($act=null){
		$this->load->library('service');
		$data=file_get_contents('php://input');
		$ret=array('out'=>$this->service->handle('blog',$data));
		$this->output($ret);
	}	
	function user($act=null){
		$this->load->library('service');
		$data=file_get_contents('php://input');
		$ret=array('out'=>$this->service->handle('user',$data));
		$this->output($ret);
	}
	function site($act=null){
		$this->load->library('service');
		$data=file_get_contents('php://input');
		$ret=array('out'=>$this->service->handle('site',$data));
		$this->output($ret);
	}
	
	//---------------------
	function output($ret){
		// ret contains element out with elements output (format) and data
		$out=null;
		if(isset($ret['out'])){
			if(isset($ret['out']['output']) && is_string($ret['out']['output'])){ 
				$out = 'out_' . $ret['out']['output'];
			} else {
				$out = 'out_json';
			}
			$this->load->view('api/'.$out,$ret['out']['data']);
		}else{
			$this->load->view('api/out_json',array('items'=>array('msg'=>'Unknown Error'))); 
		}
	}
	function tz($cont){
		$this->load->model('tz_model');
		
		//$out=$this->tz_model->getAreaInfo($cont);
		$out=$this->tz_model->getOffsetInfo($cont);
		echo json_encode($out);
	}
	
}
?>
