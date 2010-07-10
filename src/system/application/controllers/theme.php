<?php

class Theme extends Controller {
	
	public function Theme(){
		parent::Controller();
		$this->auth=($this->user_model->isAuth()) ? true : false;
		$this->user_model->logStatus();
	}
	public function index(){
		
		$this->load->model('event_themes_model','eventThemes');

		$arr=array(
			'themes'=>$this->eventThemes->getUserThemes()
		);
		$this->template->write_view('content','theme/index',$arr);
		$this->template->render();
	}
	public function add($id=null){
		
		//Check to see if they're supposed to be here
		if(!$this->auth){ redirect(); }
		
		$_css_upload_config	= array(
			'upload_path'	=> $_SERVER['DOCUMENT_ROOT'].'/inc/css/event',
			'allowed_types'	=> 'css',
			'overwrite'		=> true
		);
		
		$this->load->model('event_themes_model','eventThemes');
		$this->load->model('user_admin_model','userAdmin');
		$this->load->library('validation');
		$this->load->library('upload',$_css_upload_config);
		
		$rules=array(
			'theme_name'	=>'required',
			'theme_event'	=>'required',
			'theme_desc'	=>'required'
		);
		$fields=array(
			'theme_name'	=>'Theme Name',
			'theme_event'	=>'Theme Event',
			'theme_desc'	=>'Theme Description',
			'theme_active'	=>'Theme Active',
			'theme_style'	=>'Theme Style'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		// get the events the user is an admin for
		$uid=$this->session->userdata('ID');
		foreach($this->userAdmin->getUserEventAdmin($uid) as $event){
			//$this->validation->theme_event[$event->event_id]=$event->event_name;
			$this->user_events[$event->event_id]=$event->event_name;
		}
		
		if($this->validation->run()!=FALSE){
			
			if(!$this->upload->do_upload('theme_style')){
				var_dump($this->upload->display_errors());
			}else{ $upload_data=$this->upload->data(); }
			
			echo 'success!';
			
			// By default, this new theme won't be active...
			$detail=array(
				'theme_name'=> $this->input->post('theme_name'),
				'theme_desc'=> $this->input->post('theme_desc'),
				'active'	=> 0,
				'event_id'	=> $this->input->post('theme_event'),
				'css_file'	=> $upload_data['file_name'],
				'created_by'=> $uid,
				'created_at'=> time()
			);
			$this->eventThemes->addEventTheme($detail);
			
			/* 
			When the submit is successful:
				- add a record to the database 
				- maybe make it active
				- move the CSS upload to the /inc/css/event folder 
					with the event ID as a part of the name
			*/
			$data=array();
			//$this->eventThemes->addEventTheme($data);
			
		}
		
		$this->template->write_view('content','theme/add');
		$this->template->render();
	}
	
	/**
	 * Edit the given theme - logic lives in add()
	 */
	public function edit($id){
		$this->add($id);
	}
	
	public function activate($theme_id){
		
		// Be sure that they have access to that theme (event admin)
		$this->load->model('event_themes_model','eventThemes');
		$uid=$this->session->userdata('ID');
		foreach($this->eventThemes->getUserThemes($uid) as $theme){
			if($theme->ID==$theme_id){
				$this->eventThemes->activateTheme($theme->ID,$theme->event_id);
			}
		}
		redirect('theme');
		
	}
}


?>