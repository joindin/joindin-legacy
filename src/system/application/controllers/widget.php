<?php

class Widget extends Controller {

	public function __construct(){
		parent::Controller();
	}
	public function index(){
	}
	
	public function fetchdata($type,$id){
		$render_to	 = $this->input->get('render_to');
		$display_type= $this->input->get('display_type');
		
		switch(strtolower($type)){
			case 'talk': 
				$this->load->model('talks_model');
				$data=$this->talks_model->getTalks($id);
				break;
			case 'event': 
				$this->load->model('event_model');
				$data=$this->event_model->getEventDetail($id);
				break;
			case 'user':
				$this->load->model('talks_model');
				$data=array('username'=>'enygma','talks'=>$this->talks_model->getUserTalks($id));
				break;
		}
		echo 'joindin.jsonpCallback(
			'.$id.',
			"'.strtolower($type).'",
			"'.$display_type.'",
			"'.$render_to.'",
			'.json_encode($data).')';
		
	}
	
	public function event(){
		$this->load->helper('url');
		$this->load->model('event_model','event');
		$p=explode('/',uri_string());
		
		$event_detail=$this->event->getEventDetail($p[3]);
		
		$data=array(
			'event'=>$event_detail[0]
		);
		$this->load->view('widget/event',$data);
	}
	
	public function talk(){
		$this->load->helper('url');
		$this->load->helper('cookie');
		$this->load->model('talks_model','talk');
		$this->load->model('talk_comments_model','tcm');
		$p=explode('/',uri_string());
		
		//The talk ID is in $p[3]
		//The type is in $p[5]
		
		error_log('type: '.$p[5]);
		if(!$p[5]){ $p[5]='large'; }
		
		$talk_detail	= $this->talk->getTalks($p[3]);
		$has_commented	= false;		
		$uid			= $this->session->userdata('ID');
		if($uid){
			$has_commented=$this->tcm->hasUserCommented($p[3],$uid);
		}
		
		$data=array(
			'talk' => $talk_detail[0],
			'site' => $_SERVER['SERVER_NAME']
		);
		$widget='widget/modules/talk_'.strtolower($p[5]);
		$this->load->view($widget,$data);
	}
	
}

?>