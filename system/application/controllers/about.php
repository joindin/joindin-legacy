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
	function import(){
		$this->template->write_view('content','about/import');
		$this->template->render();
	}
	function evt_admin(){
		$this->template->write_view('content','about/evt_admin');
		$this->template->render();
	}
	function contact(){
		$arr=array();
		$this->load->helper('form');
		$this->load->library('akismet');
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
			$arr=array(
				'comment_type'			=>'comment',
				'comment_author'		=>$this->input->post('your_name'),
				'comment_author_email'	=>$this->input->post('your_email'),
				'comment_content'		=>$this->input->post('your_com')
			);
			$ret=$this->akismet->send('/1.1/comment-check',$arr);
			
			$to='enygma@phpdeveloper.org';
			$subj='Feedback from joind.in';
			$cont= 'Name: '.$this->input->post('your_name')."\n\n";
			$cont.='Email: '.$this->input->post('your_email')."\n\n";
			$cont.='Comment: '.$this->input->post('your_com')."\n\n";
			$cont.='Spam check: '.($ret=='false') ? 'not spam' : 'spam caught';

			mail($to,$subj,$cont,'From: feedback@joind.in');
			$arr=array('msg'=>'Comments sent! Thanks for the feedback!');
			
			//clear out the values so they know it was sent..
			$this->validation->your_name	='';
			$this->validation->your_email	='';
			$this->validation->your_com		='';						
		}
		
		$this->template->write_view('content','about/contact',$arr);
		$this->template->render();
	}
}
?>