<?php

class About extends Controller {
	
	function About(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		$this->load->helper('form');
		
		$this->template->write_view('content','about/main');
		$this->template->render();
	}
	function contact(){
		$arr=array();
		$this->load->helper('form');
		$this->load->library('validation');
		
		$fields=array(
			'your_name'	=>'Name',
			'your_email'=>'Email',
			'your_com'	=>'Comments'
		);
		$rules=array(
			'your_name'	=> 'required',
			'your_com'	=> 'required'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);

		if($this->validation->run()!=FALSE){
			$to='enygma@phpdeveloper.org';
			$subj='Feedback from joind.in';
			$cont= 'Name: '.$this->input->post('your_name')."\n\n";
			$cont.='Email: '.$this->input->post('your_email')."\n\n";
			$cont.='Comment: '.$this->input->post('your_com');

			mail($to,$subj,$cont,'From: feedback@joind.in');
			$arr=array('msg'=>'Comments sent! Thanks for the feedback!');
		}
		
		$this->template->write_view('content','about/contact',$arr);
		$this->template->render();
	}
}
?>