<?php

class Talk extends Controller {
	
	var $auth	= false;
	
	function Talk(){
		parent::Controller();
		$this->auth=($this->user_model->isAuth()) ? true : false;
		$this->user_model->logStatus();
	}
	function index(){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('talks_model');
		
		$talks=$this->talks_model->getTalks();
		
		$this->template->write_view('content','talk/main',array('talks'=>$talks),TRUE);
		$this->template->render();
		//$this->load->view('talk/main',array('talks'=>$talks));
	}
	//-------------------
	function add($id=null){
		if($id){ $this->edit_id=$id; }
		$this->load->model('talks_model');
		$this->load->model('event_model');
		$this->load->helper('form');
		$this->load->library('validation');

		$events=$this->event_model->getEventDetail();
		
		$rules=array(
			'event_id'	=>'required',
			'talk_title'=>'required',
			'talk_desc'	=>'required',
			'speaker'	=>'required',
			'given_mo'	=>'callback_given_mo_check'
		);
		$fields=array(
			'event_id'	=>'Event Name',
			'talk_title'=>'Talk Title',
			'speaker'	=>'Speaker',
			'given_mo'	=>'Given Month',
			'given_day'	=>'Given Day',
			'given_yr'	=>'Given Year',
			'slides_link'=>'Slides Link',
			'talk_desc'	=>'Talk Description'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($id){
			$det=$this->talks_model->getTalks($id); //print_r($det);
			foreach($det[0] as $k=>$v){
				$this->validation->$k=$v;
			}
			$this->validation->eid=$det[0]->eid;
			$this->validation->given_mo = date('m',$det[0]->date_given);
			$this->validation->given_day= date('d',$det[0]->date_given);
			$this->validation->given_yr = date('Y',$det[0]->date_given);
		}
		
		
		if($this->validation->run()==FALSE){
			//$this->load->view('talk/add',array('events'=>$events));
		}else{ 
			echo 'Success!';
			$arr=array(
				'talk_title'	=> $this->input->post('talk_title'),
				'speaker'		=> $this->input->post('speaker'),
				'slides_link'	=> $this->input->post('slides_link'),
				'date_given'	=> mktime(
					0,0,0,
					$this->input->post('given_mo'),
					$this->input->post('given_day'),
					$this->input->post('given_yr')
				),
				'event_id'		=> $this->input->post('event_id'),
				'talk_desc'		=> $this->input->post('talk_desc'),
				'active'		=> '1'
			);
			if($id){
				$this->db->where('id',$id);
				$this->db->update('talks',$arr);
			}else{
				$this->db->insert('talks',$arr);
			}
		}
		
		$this->template->write_view('content','talk/add',array('events'=>$events),TRUE);
		$this->template->render();
	}
	function edit($id){
		$this->add($id);
	}
	function delete($id){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('talks_model');
		
		$arr=array('tid'=>$id);
		if(isset($_POST['answer']) && $_POST['answer']=='yes'){
			echo 'delete';
			$this->talks_model->deleteTalk($id);
			$arr=array();
		}
		
		$this->template->write_view('content','talk/delete',$arr,TRUE);
		$this->template->render();
	}
	function view($id){
		$this->load->model('talks_model');
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->plugin('captcha');

		$cap_arr=array(
			'img_path'		=>$_SERVER['DOCUMENT_ROOT'].'/inc/img/captcha/',
			'img_url'		=>'/inc/img/captcha/',
			'img_width'		=>'130',
			'img_height'	=>'30'
		);
		
		$rules	=array(
			'comment'	=> 'required',
			'rating'	=> 'required'
		);
		$fields	=array(
			'comment'	=> 'Comment',
			'rating'	=> 'Rating'
		);
		if(!$this->user_model->isAuth()){
			$rules['cinput']	= 'required|callback_cinput_check';
			$fields['cinput']	= 'Captcha';
		}
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()==FALSE){
			//echo 'error!';
		}else{ 
			$priv=$this->input->post('private');
			$priv=(empty($priv)) ? 0 : 1;
			
			$arr=array(
				'talk_id'	=> $id,
				'rating'	=> $this->input->post('rating'),
				'comment'	=> $this->input->post('comment'),
				'date_made'	=> time(),
				'private'	=> $priv,
				'active'	=> 1,
				'user_id'	=> ($this->user_model->isAuth()) ? $this->session->userdata('ID') : '0'
			);
			$this->db->insert('talk_comments',$arr);
			
			//send an email when a comment's made
			$msg='';
			foreach($arr as $ak=>$av){ $msg.='['.$ak.'] => '.$av."\n"; }
			mail('enygma@phpdeveloper.org','Comment on talk '.$id,$msg,'From: comments@joind.in');
			
			$this->session->set_flashdata('msg', 'Comment added!');
		}
		$cap = create_captcha($cap_arr);
		$this->session->set_userdata(array('cinput'=>$cap['word']));
			
		$this->load->model('talks_model');
		$arr=array(
			'detail'	=> $this->talks_model->getTalks($id),
			'comments'	=> $this->talks_model->getTalkComments($id),
			'admin'	 	=> ($this->user_model->isAdminTalk($id)) ? true : false,
			'site_admin'=> ($this->user_model->isSiteAdmin()) ? true : false,
			'auth'		=> $this->auth,
			'captcha'	=> $cap
		);
		if(empty($arr['detail'])){ redirect('talk'); }
		
		$this->template->write('feedurl','/feed/talk/'.$id);
		$this->template->write_view('content','talk/detail',$arr,TRUE);
		$this->template->render();
		//$this->load->view('talk/detail',$arr);
	}
	//------------------------
	function given_mo_check($str){
		$t=mktime(
			0,0,0,
			$this->validation->given_mo,
			$this->validation->given_day,
			$this->validation->given_yr
		); echo $t.' '.date('m.d.Y H:i:s',$t);
		//get the duration of the selected event
		$det=$this->event_model->getEventDetail($this->validation->event_id);
		$det=$det[0];
		echo '<pre>'; print_r($det); echo '</pre>';
		if($t>=$det->event_start && $t<=$det->event_end){
			return true;
		}else{
			$this->validation->set_message('given_mo_check','Talk date must be during the event!');
			return false;
		}
	}
	function cinput_check($str){
		if($this->input->post('cinput') != $this->session->userdata('cinput')){
			$this->validation->_error_messages['cinput_check'] = 'Incorrect Captcha characters.';
			return FALSE;                            
		}else{ return TRUE; }
	}
}
?>
