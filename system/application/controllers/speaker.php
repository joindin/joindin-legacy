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
	$arr=array();
	
	$this->template->write_view('content','speaker/profile',$arr);
	$this->template->render();
    }
    /**
     * Create/modify the information in a speaker's profile
     */
    public function edit(){
	$pic_err=null;
	
	$this->load->helper('form');
	$this->load->library('validation');
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

	// Run the form!
	if($this->validation->run()!=FALSE){
	    // Set up the upload for the user pic
	    /*$this->load->library('upload',array(
		'upload_path'	=> $this->config->item('user_pic_path'),
		'allowed_types'	=> 'gif|jpg|png',
		'max_size'=>2000,'max_height'=>100,'max_width'=>100
	    ));
	    $this->p_up=$this->upload;
	    unset($this->_upload);

	    // Set up the upload for the resume
	    $this->load->library('upload',array(
		'upload_path'	=> $this->config->item('user_resume_path'),
		'allowed_types'=>'txt|doc|pdf','max_size'=>2000
	    ));
	    $this->r_up=$this->upload;
	    unset($this->_upload);
	     *
	     */

	    $this->load->library('upload',array(
		'resume'=>array(
		    'upload_path'	=> $this->config->item('user_resume_path'),
		    'allowed_types'=>'txt|doc|pdf','max_size'=>2000
		),
		'picture'=>array(
		    'upload_path'	=> $this->config->item('user_pic_path'),
		    'allowed_types'	=> 'gif|jpg|png',
		    'max_size'=>2000,'max_height'=>100,'max_width'=>100
		)
	    ));

	    // Let's go! Make our array to insert!

	    echo '<pre>'; print_r($_FILES); echo '</pre>';

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

	    echo '<pre>'; print_r($_FILES); echo '</pre>';

	    $this->upload->do_upload(array('picture','resume'));
	    //$pic_err=$this->p_up->display_errors();
	    //$pdata=$this->p_up->data(); print_r($pdata); echo '<br/><br/>';

	    //$this->upload->do_upload('resume');
	    $up_err=$this->upload->display_errors();
	    $udata=$this->upload->data(); print_r($udata);

	    $data=array(
		'user_id'	=>$udata[0]->ID,
		'country_id'	=>$this->input->post(),
		'full_name'	=>$this->input->post('full_name'),
		'contact_email'	=>$this->input->post('contact_email'),
		'website'	=>$this->input->post('website'),
		'blog'		=>$this->input->post('blog'),
		'phone'		=>$this->input->post('phone'),
		'city'		=>$this->input->post('city'),
		'zip'		=>$this->input->post('zip'),
		'street'	=>$this->input->post('street'),
		'job_title'	=>$this->input->post('job_title'),
		'bio'		=>$this->input->post('bio'),
		'resume'	=>$rdata['file_name'],
		'picture'	=>$pdata['file_name']
	    );
	    echo '<pre>'; print_r($data); echo '</pre>';
	}else{
	    // If there's not an data set, get from their profile
	    if(empty($this->validation->email)){
		$udata=$this->user_model->getUser($this->session->userdata('ID'));
		$this->validation->email    = $udata[0]->email;
		$this->validation->full_name= $udata[0]->full_name;
	    }
	}

	$arr=array(
	    'msg'=>$this->validation->error_string.'Pic:'.$pic_err.'Resume:'.$resume_err
	);

	$this->template->write_view('content','speaker/edit',$arr);
	$this->template->render();
    }
    /**
     * Define the access levels for different versions of
     * the speaker's profile
     */
    public function access(){

    }
}
?>
