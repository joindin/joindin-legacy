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
			$msg=array();
			
			if(!$this->upload->do_upload('theme_style')){
				var_dump($this->upload->display_errors());
			}else{ $upload_data=$this->upload->data(); }
			
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
			$theme_id=$this->eventThemes->addEventTheme($detail);
			$msg[]='Theme successfully added!';
			
			if($this->input->post('theme_active')==1){
				$this->eventThemes->activateTheme($theme_id,$this->input->post('theme_event'));
				$msg[]='Theme marked as active!';
			}
			
			$msg[]='<a href="/theme">Back to theme list</a>';
			
			if(!empty($msg)){
				$this->validation->error_string=implode("<br/>",$msg);
			}
		}
		
		$this->template->write_view('content','theme/add');
		$this->template->render();
	}
	
	/**
	 * Edit the given theme - logic lives in add()
	 */
	public function edit($theme_id){
		$this->add($theme_id);
	}
	
	public function delete($theme_id){
		if($this->eventThemes->isAuthTheme($uid,$theme_id)){
			$this->eventThemes->deleteTheme($theme_id);
		}
	}
	
	public function activate($theme_id){
		
		// Be sure that they have access to that theme (event admin)
		$this->load->model('event_themes_model','eventThemes');
		$uid=$this->session->userdata('ID');
		
		if($this->eventThemes->isAuthTheme($uid,$theme_id)){
			$this->eventThemes->activateTheme($theme->ID,$theme->event_id);
		}
		redirect('theme');
		
	}
}


?>