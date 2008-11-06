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
			$this->template->write_view('content','user/login');
			$this->template->render();
		}else{
			//success!
			$ret=$this->user_model->getUser($this->input->post('user')); //print_r($ret);
			$this->session->set_userdata((array)$ret[0]);
			redirect('user/main');
		}
	}
	function logout(){
		$this->load->helper('url');
		$this->session->sess_destroy();
		redirect();
	}
	function register(){
			$this->load->helper('form');
			$this->load->library('validation');
			$this->load->model('user_model');
			$this->load->plugin('captcha');

			$cap_arr=array(
				'img_path'		=>$_SERVER['DOCUMENT_ROOT'].'/inc/img/captcha/',
				'img_url'		=>'/inc/img/captcha/',
				'img_width'		=>'130',
				'img_height'	=>'30'
			);

			$fields=array(
				'user'	=> 'Username',
				'pass'	=> 'Password',
				'passc'	=> 'Confirm Password',
				'email'	=> 'Email',
				'full_name'=>'Full Name',
				'cinput'	=> 'Captcha'				
			);
			$rules=array(
				'user'	=> 'required|trim|callback_usern_check|xss_clean',
				'pass'	=> 'required|trim|matches[passc]|md5',
				'passc'	=> 'required|trim',
				'email'	=> 'required|trim|valid_email',
				'cinput'	=> 'required|callback_cinput_check'
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
					'email'		=> $this->input->post('email')
				);
				$this->db->insert('user',$arr);
				
				//now, since they're set up, log them in a push them to the main page
				$ret=$this->user_model->getUser($arr['username']);
				$this->session->set_userdata((array)$ret[0]);
				redirect('user/main');
			}
			$cap=create_captcha($cap_arr);
                        $this->session->set_userdata(array('cinput'=>$cap['word']));
                        $carr=array('captcha'=>$cap);

			$this->template->write_view('content','user/register',$carr);
			$this->template->render();
	}
	function main(){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('talks_model');
		
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
		
		$this->template->write_view('content','user/main',$arr);
		$this->template->render();
	}
	function view($uid){
		$this->load->model('talks_model');
		$arr=array(
			'details'	=> $this->user_model->getUser($uid),
			'comments'	=> $this->talks_model->getUserComments($uid)
		);

		$this->template->write_view('content','user/view',$arr);
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
}
