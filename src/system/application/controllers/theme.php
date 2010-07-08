<?php

class Theme extends Controller {
	
	var $_css_upload_config	= array(
		'upload_path'	=> '/inc/css/event',
		'allowed_types'	=> 'css'
	);
	
	public function Theme(){
		parent::Controller();
		$this->auth=($this->user_model->isAuth()) ? true : false;
		$this->user_model->logStatus();
	}
	public function index(){
		
		$this->load->model('event_themes_model','eventThemes');
		$this->eventThemes->getUserThemes();
		
		$this->template->write_view('content','theme/index');
		$this->template->render();
	}
	public function add($id=null){
		
		//Check to see if they're supposed to be here
		if(!$this->auth){ redirect(); }
		
		$this->load->model('event_themes_model','eventThemes');
		$this->load->model('user_admin_model','userAdmin');
		$this->load->library('validation');
		$this->load->library('upload',$this->_css_upload_config);
		
		$rules=array(
			'theme_name'	=>'required',
			'theme_event'	=>'required',
			'theme_desc'	=>'required',
			'theme_style'	=>'required'
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
		
		if(!$this->upload->do_upload()){
			var_dump($this->upload->display_errors());
		}

		
		if($this->validation->run()!=FALSE){
			
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
	
}


?>