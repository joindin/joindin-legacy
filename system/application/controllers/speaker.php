<?php
/* 
 * Speaker controller
 */
class Speaker extends Controller {

    public function Speaker(){
	parent::Controller();
    }
    //--------------
    /**
     * Work with the speaker's profile(s)
     */
    public function profile(){
	$this->load->model('speaker_profile_model','sp');
	
	$arr		= array();
	$udata		= $this->user_model->getUser($this->session->userdata('ID'));
	$arr['pdata']	= $this->sp->getProfile($udata[0]->ID);

	$profile_pic=null;
	if(!empty($arr['pdata'][0]->picture)){
	    $p=$this->config->item('user_data').'/'.$arr['pdata'][0]->picture;
	    if(is_file($p)){ $profile_pic='/inc/img/profile/'.$arr['pdata'][0]->picture; }
	    $arr['profile_pic']=$profile_pic;
	}
	
	$this->template->write_view('content','speaker/profile',$arr);
	$this->template->render();
    }
    /**
     * Create/modify the information in a speaker's profile
     */
    public function edit(){
	$pic_err    = null;
	$resume_err = null;
	
	$this->load->helper('form');
	$this->load->library('validation');
	$this->load->model('speaker_profile_model','sp');
	$udata=$this->user_model->getUser($this->session->userdata('ID'));

	$fields=array(
	    'full_name'	=> 'Full Name',
	    'email'	=> 'Email',
	    'website'	=> 'Website',
	    'blog'	=> 'Blog',
	    'phone'	=> 'Phone',
	    'job_title'	=> 'Job Title',
	    'bio'	=> 'Bio',
	    'street'	=> 'Street',
	    'city'	=> 'City',
	    'zip'	=> 'Zip',
	    'country'	=> 'Country',
	    'resume'	=> 'Resume',
	    'picture'	=> 'Picture'
	);
	$rules=array(
	    'full_name'	=> 'required',
	    'email'	=> 'required|valid_email',
	    'bio'	=> 'required'
	);
	$this->validation->set_rules($rules);
	$this->validation->set_fields($fields);

	// If we have profile settings, assign them
	$cdata=$this->sp->getProfile($udata[0]->ID);
	if(isset($cdata[0])){
	    foreach($cdata[0] as $k=>$v){ $this->validation->$k=$v; }
	}

	// Run the form!
	if($this->validation->run()!=FALSE){
	    // Set up the upload for the resume
	    $config=array(
		'upload_path'	=> $this->config->item('user_data'),
		'allowed_types'	=> 'jpg|gif|png',
		'overwrite'	=> true,
		'max_size'	=> 2000,
		'max_height'	=> 200,
		'max_width'	=> 200
	    );
	    $this->load->library('upload',$config);

	    /*$this->load->library('upload',array(
		'resume'=>array(
		    'upload_path'	=> $this->config->item('user_resume_path'),
		    'allowed_types'=>'txt|doc|pdf','max_size'=>2000
		),
		'picture'=>array(
		    'upload_path'	=> $this->config->item('user_pic_path'),
		    'allowed_types'	=> 'gif|jpg|png',
		    'max_size'=>2000,'max_height'=>100,'max_width'=>100
		)
	    ));*/

	    // Let's go! Make our array to insert!

	    // Check for picture upload...reset our filename if it's there
	    if(isset($_FILES['picture']) && $_FILES['picture']['error']==0){
		$ext=strrchr($_FILES['picture']['name'],'.');
		$_FILES['picture']['name']='user_pic_'.$udata[0]->ID.$ext;
	    }
	    // Check for resume upload...reset out filename if it's there
	    if(isset($_FILES['resume']) && $_FILES['resume']['error']==0){
		$ext=strrchr($_FILES['resume']['name'],'.');
		$_FILES['resume']['name']='user_resume_'.$udata[0]->ID.$ext;
	    }
	    
	    //$this->upload->do_upload('resume');
	    $this->upload->do_upload('picture');

	    //$this->upload->do_upload('resume');
	    $up_err = $this->upload->display_errors();
	    $up_data= $this->upload->data();

	    $data=array(
		'user_id'	=>$udata[0]->ID,
		'country_id'	=>$this->input->post(),
		'full_name'	=>$this->input->post('full_name'),
		'contact_email'	=>$this->input->post('email'),
		'website'	=>$this->input->post('website'),
		'blog'		=>$this->input->post('blog'),
		'phone'		=>$this->input->post('phone'),
		'city'		=>$this->input->post('city'),
		'zip'		=>$this->input->post('zip'),
		'street'	=>$this->input->post('street'),
		'job_title'	=>$this->input->post('job_title'),
		'bio'		=>$this->input->post('bio'),
		//'resume'	=>$rdata['file_name'],
	    );
	    if($up_data['file_name']){
		$data['picture']=$up_data['file_name'];
	    }
	    //echo '<pre>'; print_r($data); echo '</pre>';

	    if(isset($cdata[0])){
		$this->sp->updateProfile($udata[0]->ID,$data);
		$this->validation->error_string='Profile successfully updated!';
	    }else{
		$this->sp->setProfile($data);
		$this->validation->error_string='Profile successfully saved!';
	    }
	}else{
	    // If there's not an data set, get from their profile
	    if(empty($this->validation->email)){
		$udata=$this->user_model->getUser($this->session->userdata('ID'));
		$this->validation->email    = $udata[0]->email;
		$this->validation->full_name= $udata[0]->full_name;
	    }
	}

	$msg=$this->validation->error_string;
	$msg.=($pic_err) ? 'Profile Image: '.$pic_err : '';
	$msg.=($resume_err) ? 'Resume Upload: '.$resume_err : '';

	$profile_pic=null;
	if(!empty($cdata[0]->picture)){
	    $p=$this->config->item('user_data').'/'.$cdata[0]->picture;
	    if(is_file($p)){ $profile_pic='/inc/img/profile/'.$cdata[0]->picture; }
	}

	$arr=array(
	    'msg'	    => $msg,
	    'profile_pic'   => $profile_pic
	);

	$this->template->write_view('content','speaker/edit',$arr);
	$this->template->render();
    }
    /**
     * Define the access levels for different versions of
     * the speaker's profile
     */
    public function access(){
	$this->load->model('speaker_profile_model','spm');
	$this->load->helper('url');
	$this->load->helper('form');
	$this->load->library('validation');
	$p=explode('/',uri_string());
	$arr=array();

	$view='';
	if(isset($p[3])){
	    switch(strtolower($p[3])){
		case 'add':
		    $view='speaker/access_add';
		    //$f=$this->spm->getProfileFields(); echo '<pre>'; print_r($f); echo '</pre>';
		    
		    $rules  = array('fields'=>'required');
		    $fields = array('fields'=>'Items');

		    $this->validation->set_rules($rules);
		    $this->validation->set_fields($fields);

		    if($this->validation->run()!=FALSE){
			var_dump($this->input->post('fields'));
		    }else{
			$this->validation->set_message('fields','You must select at least one field!');
			$arr['msg']=$this->validation->error_string;
		    }

		    break;
	    }
	}else{ $view='speaker/access'; }

	$udata	= $this->user_model->getUser($this->session->userdata('ID'));
	
	$arr['access_data']=$this->spm->getProfileAccess($udata[0]->ID);
	
	$this->template->write_view('content',$view,$arr);
	$this->template->render();
    }
}
?>
