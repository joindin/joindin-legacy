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
		$this->load->helper('form');
		$this->load->library('validation');
		
		$fields=array(
			'your_name'	=>'Name',
			'your_email'=>'Email',
			'your_com'	=>'Comments'
		);
		$rules=array();
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		$this->template->write_view('content','about/contact');
		$this->template->render();
	}
}
?>