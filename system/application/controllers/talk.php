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
		
		$talks=$this->talks_model->getTalks(null,true);
		
		$this->template->write_view('content','talk/main',array('talks'=>$talks),TRUE);
		$this->template->render();
		//$this->load->view('talk/main',array('talks'=>$talks));
	}
	//-------------------
	function add($id=null){
		if($id){ $this->edit_id=$id; }
		$this->load->model('talks_model');
		$this->load->model('event_model');
		$this->load->model('categories_model');	
		$this->load->model('lang_model');				
		$this->load->helper('form');
		$this->load->library('validation');

		$events	= $this->event_model->getEventDetail();
		$cats	= $this->categories_model->getCats();
		$langs	= $this->lang_model->getLangs();
		
		$rules=array(
			'event_id'		=>'required',
			'talk_title'	=>'required',
			'talk_desc'		=>'required',
			'speaker'		=>'required',
			'session_type'	=>'required',
			'session_lang'	=>'required',
			'given_mo'		=>'callback_given_mo_check'
		);
		$fields=array(
			'event_id'		=>'Event Name',
			'talk_title'	=>'Talk Title',
			'speaker'		=>'Speaker',
			'given_mo'		=>'Given Month',
			'given_day'		=>'Given Day',
			'given_yr'		=>'Given Year',
			'slides_link'	=>'Slides Link',
			'talk_desc'		=>'Talk Description',
			'session_type'	=>'Session Type',
			'session_lang'	=>'Session Language'
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
			
			$this->validation->session_lang=$det[0]->lang;
		}
		//check the referrer, if there's an event in it, default the select to that value
		if(preg_match('/\/event\/view\/([0-9]+)/',$_SERVER['HTTP_REFERER'],$m)){
			$this->validation->event_id=$m[1];
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
				'active'		=> '1',
				'lang'			=> $this->input->post('session_lang')
			);
			if($id){
				$this->db->where('id',$id);
				$this->db->update('talks',$arr);
				//remove the current reference for the talk and add a new one
				
				$this->db->delete('talk_cat',array('talk_id'=>$id));
				$tc_id=$id;
			}else{
				$this->db->insert('talks',$arr);
				$tc_id=$this->db->insert_id();
			}
			//now make the link between the talk and the category
			$tc_arr=array(
				'talk_id'	=> $tc_id,
				'cat_id'	=> $this->input->post('session_type')
			);
			$this->db->insert('talk_cat',$tc_arr);
		}
		
		$this->template->write_view('content','talk/add',array('events'=>$events,'cats'=>$cats,'langs'=>$langs),TRUE);
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
		$this->load->library('akismet');

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
		//	$rules['cinput']	= 'required|callback_cinput_check';
		//	$fields['cinput']	= 'Captcha';
		}
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		$cl=($r=$this->talks_model->isTalkClaimed($id)) ? $r : false; //print_r($cl);
		
		$talk_detail=$this->talks_model->getTalks($id);
		
		if($this->validation->run()==FALSE){
			//echo 'error!';
		}else{ 
			$arr=array(
				'comment_type'			=>'comment',
				'comment_content'		=>$this->input->post('your_com')
			);
			$ret=$this->akismet->send('/1.1/comment-check',$arr);
			
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
			$arr['spam']=($ret=='false') ? 'spam' : 'not spam';
			foreach($arr as $ak=>$av){ $msg.='['.$ak.'] => '.$av."\n"; }
			mail('enygma@phpdeveloper.org','Comment on talk '.$id,$msg,'From: comments@joind.in');
			
			//if its claimed, be sure to send an email to the person to tell them
			if($cl){
				$to=$cl[0]->email;
				$subj	= 'A new comment has been posted on your talk!';
				$msg	= sprintf("
A comment has been posted to your talk on joind.in: \n%s\n
Click here to view it: http://joind.in/talk/view/%s
				",$talk_detail[0]->talk_title,$id);
				mail($to,$subj,$msg,'From: comments@joind.in');
			}
			
			$this->session->set_flashdata('msg', 'Comment added!');
		}
		//$cap = create_captcha($cap_arr);
		//$this->session->set_userdata(array('cinput'=>$cap['word']));
			
		$this->load->model('talks_model');
		$arr=array(
			'detail'	=> $talk_detail,
			'comments'	=> $this->talks_model->getTalkComments($id),
			'admin'	 	=> ($this->user_model->isAdminTalk($id)) ? true : false,
			'site_admin'=> ($this->user_model->isSiteAdmin()) ? true : false,
			'auth'		=> $this->auth,
		//	'captcha'	=> $cap,
			'claimed'	=> $this->talks_model->isTalkClaimed($id)
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
		); //echo $t.' '.date('m.d.Y H:i:s',$t);
		//get the duration of the selected event
		$det=$this->event_model->getEventDetail($this->validation->event_id);
		$det=$det[0];
		//echo '<pre>'; print_r($det); echo '</pre>';
		$day_start	= mktime(0,0,0,date('m',$det->event_start),date('d',$det->event_start),date('Y',$det->event_start));
		$day_end	= mktime(23,59,59,date('m',$det->event_end),date('d',$det->event_end),date('Y',$det->event_end));
		//if($t>=$det->event_start && $t<=$det->event_end){
		if($t>=$day_start && $t<=$day_end){
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