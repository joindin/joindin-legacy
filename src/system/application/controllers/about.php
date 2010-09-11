<?php

class About extends Controller {
	
	function About(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		$this->load->helper('form');
		
		$this->template->write_view('content','about/main');
		$this->template->write_view('sidebar2','about/_facebook-sidebar');
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
			
			$subj='Feedback from ' . $this->config->item('site_name');
			$cont= 'Name: '.$this->input->post('your_name')."\n\n";
			$cont.='Email: '.$this->input->post('your_email')."\n\n";
			$cont.='Comment: '.$this->input->post('your_com')."\n\n";
			$cont.='Spam check: '.($ret=='false') ? 'not spam' : 'spam caught';
			
			$admin_emails=$this->user_model->getSiteAdminEmail();
			foreach($admin_emails as $user){
				mail($user->email,$subj,$cont,'From: ' . $this->config->item('email_feedback'));
			}
			$arr=array('msg'=>'Comments sent! Thanks for the feedback!');
			
			//clear out the values so they know it was sent..
			$this->validation->your_name	='';
			$this->validation->your_email	='';
			$this->validation->your_com		='';						
		}
		
		$this->template->write_view('content','about/contact',$arr);
		$this->template->render();
	}
	function iphone_support(){
		$this->template->write_view('content','about/iphone_support');
		$this->template->render();
	}
	function widget(){
		$this->template->write_view('content','about/widget');
		$this->template->render();
	}
	
	/**
	 * Pull in the current list of gravatars and push out a random set
	 */
	function who(){
		$dir=$this->config->item('gravatar_cache_dir');
		
		$default_size	= 1323;
		$users			= array();
		foreach(new DirectoryIterator($dir) as $file){
			if(!$file->isDot() && filesize($dir.'/'.$file->getFilename())!=1323){
				if(preg_match('/user([0-9]+)\.jpg/',$file->getFilename(),$m)){
					//$users[$m[1]]=$file->getFilename();
					$users[]=$m[1];
				}
			}
		}
		
		$arr=array('users'=>$users);
		$this->template->write_view('content','about/who',$arr);
		$this->template->render();
	}
	
	function services(){
		
		$arr=array();
		$this->template->write_view('content','about/services',$arr);
		$this->template->render();
	}
}
?>
