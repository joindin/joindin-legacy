<?php

class User extends Controller {
	
	function User(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		$this->load->helper('url');
		redirect('user/login');
	}
	function login(){
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('validation');
		$this->load->model('user_model');
		
		$fields=array(
			'user'=>'Username',
			'pass'=>'Password'
		);
		$rules=array(
			'user'=>'required',
			'pass'=>'required|callback_start_up_check'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()==FALSE){
			$ref=(isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->session->userdata('ref_url');
			//$this->session->set_userdata('ref_url',$ref);

			$this->template->write_view('content','user/login');
			$this->template->render();
		}else{
			//success! get our data and update our login time
			$ret=$this->user_model->getUser($this->input->post('user')); //print_r($ret);
			$this->session->set_userdata((array)$ret[0]);
			
			//update login time
			$this->db->where('id',$ret[0]->ID);
			$this->db->update('user',array('last_login'=>time()));
			
			$rurl=$this->session->userdata('ref_url');
			if(!empty($rurl) && false === strpos($rurl, 'user/login')){
				$url=str_replace('http://'.$_SERVER['HTTP_HOST'],'',$rurl); //echo $url;
				redirect($url);
			}else{ redirect('user/main'); }
		}
	}
	function logout(){
		$this->load->helper('url');
		$this->session->sess_destroy();
		redirect();
	}
	function forgot(){
		$this->load->helper('form');
		$this->load->library('validation');
		$arr=array();
		
		$fields=array(
			'user'	=> 'Username',
			'email'	=> 'Email Address'
		);
		$rules=array(
			'user'	=> 'trim|xss_clean|callback_login_exist_check',
			'email'	=> 'trim|xss_clean|valid_email|callback_email_exist_check'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()!=FALSE){
			//reset their password and send it out to the account
			$email=$this->input->post('email');
			$login=$this->input->post('user');
			if(!empty($email)){
				$ret=$this->user_model->getUserByEmail($email);
			}elseif(!empty($login)){
				$ret=$this->user_model->getUser($login);
			}
			//print_r($ret);
			
			//generate the new password...
			$sel		= array_merge(range('a','z'),range('A','Z'),range(0,9)); shuffle($sel);
			$pass_len	= 10;
			$pass		= '';
			$uid		= $ret[0]->ID;
			for($i=0;$i<$pass_len;$i++){
				$r=mt_rand(0,count($sel)-1);
				$pass.=$sel[$r];
			}
			$arr=array('password'=>md5($pass));
			$this->user_model->updateUserInfo($uid,$arr);
			
			$to		= $ret[0]->email;
			$subj	= 'Joind.in - Password Reset Request';
			$content= sprintf('
				%s,
				
				Someone has requested a password reset for your account on Joind.in. 
				Your new password is below:
				
				%s
				
				Please log in in at http://joind.in/user/login and reset your password as soon as possible.
			',$ret[0]->username,$pass);
			
			mail($to,$subj,$content,'From: info@joind.in');
		}
		
		$this->template->write_view('content','user/forgot',$arr);
		$this->template->render();
	}
	function changestat($uid){
	    // Kick them back out if they're not an admin
	    if(!$this->user_model->isSiteAdmin()){ redirect(); }
	    $this->user_model->toggleUserStatus($uid);
	    redirect('user/view/'.$uid);
	}

	function register(){
			$this->load->helper('form');
			$this->load->library('validation');
			$this->load->model('user_model');
			
			/*$this->load->plugin('captcha');
			$cap_arr=array(
				'img_path'		=>$_SERVER['DOCUMENT_ROOT'].'/inc/img/captcha/',
				'img_url'		=>'/inc/img/captcha/',
				'img_width'		=>'130',
				'img_height'	=>'30'
			);*/

			$fields=array(
				'user'	=> 'Username',
				'pass'	=> 'Password',
				'passc'	=> 'Confirm Password',
				'email'	=> 'Email',
				'full_name'=>'Full Name',
			//	'cinput'	=> 'Captcha'				
			);
			$rules=array(
				'user'	=> 'required|trim|callback_usern_check|xss_clean',
				'pass'	=> 'required|trim|matches[passc]|md5',
				'passc'	=> 'required|trim',
				'email'	=> 'required|trim|valid_email',
			//	'cinput'	=> 'required|callback_cinput_check'
			);
			$this->validation->set_rules($rules);
			$this->validation->set_fields($fields);
			
			if($this->validation->run()==FALSE){
				//$this->load->view('talk/add',array('events'=>$events));
			}else{
				//success!
				//echo 'Success!';
				$this->session->set_flashdata('msg', 'Account successfully created!');
				$arr=array(
					'username'	=> $this->input->post('user'),
					'password'	=> $this->input->post('pass'),
					'email'		=> $this->input->post('email'),
					'full_name'	=> $this->input->post('full_name'),
					'active'	=> 1,
					'last_login'=> time()
				);
				$this->db->insert('user',$arr);
				
				//now, since they're set up, log them in a push them to the main page
				$ret=$this->user_model->getUser($arr['username']);
				$this->session->set_userdata((array)$ret[0]);
				redirect('user/main');
			}
			//$cap=create_captcha($cap_arr);
			//$this->session->set_userdata(array('cinput'=>$cap['word']));
			//$carr=array('captcha'=>$cap);
			$carr=array();

			$this->template->write_view('content','user/register',$carr);
			$this->template->render();
	}
	function main(){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('talks_model');
		
		if (!$this->user_model->isAuth()) { redirect('user/login'); }
		
		$fields=array(
			'talk_code'=>'Talk Code'
		);
		$rules=array(
			'talk_code'=>'required'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()!=FALSE){
			$code=$this->input->post('talk_code');
			$ret=$this->talks_model->getTalkByCode($code); //print_r($ret);
			if(!empty($ret)){
				//link our user and talk
				$uid=$this->session->userdata('ID');
				$rid=$ret[0]->ID;
				$this->talks_model->linkUserRes($uid,$rid,'talk');
				$arr['msg']='Talk claimed successfully!';
			}
		}
		$arr['talks']	= $this->talks_model->getUserTalks($this->session->userdata('ID'));
		$arr['comments']= $this->talks_model->getUserComments($this->session->userdata('ID'));
		$arr['is_admin']= $this->user_model->isSiteAdmin();
		
		$this->template->write_view('content','user/main',$arr);
		$this->template->render();
	}
	function view($uid){
		$this->load->model('talks_model');
		$this->load->model('user_attend_model','uam');
		$this->load->model('user_admin_model','uadmin');
		$this->load->helper('reqkey');
		$this->load->helper('url');
		$reqkey=buildReqKey();

		// See if we have a sort type and apply it
		$p=explode('/',uri_string());
		if(isset($p[4])){ $sort_type=$p[4]; }else{ $sort_type=null; }
		
		$details = $this->user_model->getUser($uid);
		
		if (empty($details[0])) {
			redirect();
		}

		$curr_user=$this->session->userdata('ID');
		
		//$ret=$this->user_model->getOtherUserAtEvt($uid);
		//echo '<pre>'; print_r($ret); echo '</pre>';

		$arr=array(
			'details'	=> $details,
			'comments'	=> $this->talks_model->getUserComments($uid),
			'talks'		=> $this->talks_model->getUserTalks($uid),
			'is_admin'	=> $this->user_model->isSiteAdmin(),
			'is_attending'	=> $this->uam->getUserAttending($uid),
			'my_attend'	=> $this->uam->getUserAttending($curr_user),
			'uadmin'	=> $this->uadmin->getUserTypes($uid,array('talk','event')),
			'reqkey' 	=> $reqkey,
			'seckey' 	=> buildSecFile($reqkey),
			'sort_type'	=> $sort_type
		);
		$block=array(
			'title'		=> 'Other Speakers',
			'content'	=> $this->user_model->getOtherUserAtEvt($uid),
			'udata'		=> $arr['details']
		);

		$this->template->write_view('sidebar2','user/_other-speakers',$block);
		$this->template->write_view('content','user/view',$arr);
		$this->template->render();
	}
	function manage(){
		// Be sure they're logged in
		if (!$this->user_model->isAuth()) {
		    $this->session->set_userdata('ref_url','user/manage');
		    redirect('user/login');
		}

		$this->load->helper('form');
		$this->load->library('validation');
		$uid=$this->session->userdata('ID');
		$arr=array(
			'curr_data'=>$this->user_model->getUser($uid)
		);
		
		$rules=array(
			'full_name'	=>'required',
			'email'		=>'required',
			'pass'		=>'trim|matches[pass_conf]|md5',
			'pass_conf'	=>'trim',
		);
		$fields=array(
			'full_name'	=>'Full Name',
			'email'		=>'Email',
			'pass'		=>'Password',
			'pass_conf'	=>'Confirm Password'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()!=FALSE){
			$data=array(
				'full_name'	=> $this->input->post('full_name'),
				'email'		=> $this->input->post('email')
			);

			$pass=$this->input->post('pass');
			if (!empty($pass)) { 
			    $data['password'] = $this->validation->pass;
			    
			}

			$this->db->where('ID',$uid);
			$this->db->update('user',$data);
			
			$this->session->set_flashdata('msg', 'Changes saved successfully!');
			redirect('user/manage', 'location', 302);
		}
		
		$this->template->write_view('content','user/manage',$arr);
		$this->template->render();
	}
	function admin($page=null){
		$this->load->helper('reqkey');
		$this->load->library('validation');
		$reqkey	= buildReqKey();
		$page	= (!$page) ? 1 : $page;
		$rows_in_pg=10;
		$offset	= ($page==1) ? 1 : $page*10;
		$all_users=$this->user_model->getAllUsers();
		$users	= array_slice($all_users,$offset,$rows_in_pg);
		
		$fields=array(
			'user_search'=>'Search Term'
		);
		$rules=array(
			'user_search'=>'required'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()!=FALSE){
			$users=$this->user_model->search($this->input->post('user_search'));
		}
		
		$arr=array(
			'users'		=> $users,
			'all_user_ct'=>count($all_users),
			'page'		=> $page,
			'reqkey' 	=> $reqkey,
			'seckey' 	=> buildSecFile($reqkey),
		);
		
		$this->template->write_view('content','user/admin',$arr);
		$this->template->render();
	}
	//--------------------
	function start_up_check($p){
		$u=$this->input->post('user');
		$ret=$this->user_model->validate($u,$p);
		if(!$ret){
			$this->validation->set_message('start_up_check', 'Username/password combination invalid!');
		}
		return $ret;
	}
	function cinput_check($str){
		if($this->input->post('cinput') != $this->session->userdata('cinput')){
			$this->validation->_error_messages['cinput_check'] = 'Incorrect Captcha characters.';
			return FALSE;                            
		}else{ return TRUE; }
	}
	function usern_check($str){
		$ret=$this->user_model->getUser($str);
		if(!empty($ret)){
			$this->validation->_error_messages['usern_check'] = 'Username already exists!';
			return false;
		}else{ return true; }
	}
	function email_exist_check($str){
		$ret=$this->user_model->getUserByEmail($str);
		if(empty($ret)){
			$this->validation->_error_messages['email_exist_check'] = 'Login for that email address does not exist!';
			return false;
		}else{ return true; }
	}
	function login_exist_check($str){
		$ret=$this->user_model->getUser($str);
		if(empty($ret)){
			$this->validation->_error_messages['login_exist_check'] = 'Username does not exist!';
			return false;
		}else{ return true; }
	}
}
?>
