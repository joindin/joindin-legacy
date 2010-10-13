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
				$this->load->model('user_model');
				$user=$this->user_model->getUser($id);
				$data=array(
					'username'	=> $user[0]->username,
					'full_name'	=> $user[0]->full_name,
					'talks'		=> $this->talks_model->getUserTalks($id)
				);
				break;
			case 'vote':
				$this->load->model('talks_model');
				$data=$this->talks_model->getTalks($id);
				break;
		}
		echo 'joindin.jsonpCallback(
			'.$id.',
			"'.strtolower($type).'",
			"'.$display_type.'",
			"'.$render_to.'",
			'.json_encode($data).')';
	}
	
	public function postdata(){
		error_log('vote comment: '.$this->input->post('vote_comment'));
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
		//$p=explode('/',uri_string());
		
		//The talk ID is in $p[3]
		//The type is in $p[5]
		
		error_log('uri: '.uri_string());
		error_log('cb: '.$this->input->get('callback'));
		error_log('rating: '.$this->input->get('rating'));
		error_log('comment: '.$this->input->get('comment'));

		echo "joindin.voteCallback('test')";
		
		$arr=array(
			'talk_id'		=> $this->input->get('talk_id'),
			'rating'		=> $this->input->get('rating'),
			'comment'		=> $this->input->get('comment'),
			'date_made'		=> time(),
			'user_id'		=> ($this->user_model->isAuth()) ? $this->session->userdata('ID') : '0',
			'comment_type'	=> 'comment',
			'active'		=> 1,
			'private'		=> 0
		);
		error_log(print_r($arr,true));
		$this->db->insert('talk_comments',$arr);
		
	}
	
}

?>
