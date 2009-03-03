<?php

/** ServiceManager */
require_once BASEPATH . 'application/libraries/service/ServiceManager.php';

/**
 * API controller
 * 
 * @author Chris Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijs@ibuildings.nl>
 */
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
		$manager = new ServiceManager();
	    $data = $this->_processRequest();
	    $manager->dispatch('talk', $data);
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
	
	function speaker()
	{
	    $manager = new ServiceManager();
	    $data = $this->_processRequest();
	    $manager->dispatch('speaker', $data);
	}
	
	private function _processRequest() 
	{
	    $xml = file_get_contents('php://input');
	    
	    $data['xml'] = $xml;
	    $data['query_string'] = $_SERVER['QUERY_STRING'];
    
	    return $data;
	}
	
	
	//---------------------
	function output($ret){
		if(isset($ret['out']['data']['output'])){
			$out=(string)$ret['out']['data']['output'];
		}
		$out=(!empty($out)) ? 'out_'.$out : 'out_xml';
		$this->load->view('api/'.$out,$ret['out']['data']);
	}
	function tz($cont){
		$this->load->model('tz_model');
		
		//$out=$this->tz_model->getAreaInfo($cont);
		$out=$this->tz_model->getOffsetInfo($cont);
		echo json_encode($out);
	}
	
}
?>
