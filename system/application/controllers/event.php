<?php

class Event extends Controller {
	
	function Event(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function cust($in){
		$this->load->helper('url');
		$this->load->model('event_model');
		$id=$this->event_model->getEventIdByName($in);
		//print_r($id); echo $id[0]->ID;
		if(isset($id[0]->ID)){ 
			redirect('event/view/'.$id[0]->ID); 
		}else{ echo 'error'; }
	}
	//--------------------
	function index(){
		$prefs = array (
			'show_next_prev'  => TRUE,
			'next_prev_url'   => '/event'
		);
		
		$this->load->helper('form');
		//$this->load->library('calendar',$prefs);
		$this->load->model('event_model');
		$this->load->helper('mycal');
		
		$events=$this->event_model->getEventDetail();
		$arr=array(
			'events' =>$events,
			//'admin'	 =>($this->user_model->isAdminEvent($id)) ? true : false
			'mo'	=>date('m'),
			'day'	=>0,
			'yr'	=>date('Y'),
			'all'	=>true
		);	
		$this->template->write_view('content','event/main',$arr,TRUE);
		$this->template->render();
		
		//$this->load->view('event/main',array('events'=>$events));
	}
	function calendar($date=null){
		$this->load->model('event_model');
		$this->load->helper('mycal');
		
		if(!$date){ $date=date('m_Y'); }
		
		$date_p	= explode('_',$date);
		if(count($date_p)==2){
			$start	= mktime(0,0,0,$date_p[0],1,$date_p[1]);
			$end	= mktime(0,0,0,$date_p[0],date('t',$start),$date_p[1]);	
		}else{
			$start	= mktime(0,0,0,$date_p[0],1,$date_p[2]);
			$end	= mktime(0,0,0,$date_p[0],date('t',$start),$date_p[2]);
		}		
		$events	= $this->event_model->getEventDetail(null,$start,$end);
		
		$arr=array('events'=>$events,'mo'=>$date_p[0]);
		if(count($date_p)==2){
			$arr['day']	= 1;
			$arr['yr']	= $date_p[1];
		}else{ 
			$arr['day']	= $date_p[1];
			$arr['yr']	= $date_p[2];
		}
		$this->template->write_view('content','event/main',$arr,TRUE);
		$this->template->render();
	}
	function add($id=null){
		//check for admin
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		
		if($id){ $this->edit_id=$id; }
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('event_model');
		$this->load->model('tz_model');
		
		$rules=array(
			'event_name'	=> 'required',
			'event_loc'		=> 'required',
			'event_tz'		=> 'required',
			'start_mo'		=> 'callback_start_mo_check',
			'end_mo'		=> 'callback_end_mo_check'
		);
		$this->validation->set_rules($rules);
		
		$fields=array(
			'event_name'=>'Event Name',
			'start_mo'	=>'Start Month',
			'start_day'	=>'Start Day',
			'start_yr'	=>'Start Year',
			'end_mo'	=>'End Month',
			'end_day'	=>'End Day',
			'end_yr'	=>'End Year',
			'event_loc'	=>'Event Location',
			'event_desc'=>'Event Description',
			'event_tz'	=>'Event Timezone'
		);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()==FALSE){
			if($id){
				//we're editing here...
				$ret=$this->event_model->getEventDetail($id);
				foreach($ret[0] as $k=>$v){
					if($k=='event_start'){
						$this->validation->start_mo	= date('m',$v);
						$this->validation->start_day= date('d',$v);
						$this->validation->start_yr	= date('Y',$v);
					}elseif($k=='event_end'){
						$this->validation->end_mo	= date('m',$v);
						$this->validation->end_day	= date('d',$v);
						$this->validation->end_yr	= date('Y',$v);
					}else{ $this->validation->$k=$v; }
				}
			}
			$arr=array(
				'tz'	=> $this->tz_model->getOffsetInfo()
			);
			$this->template->write_view('content','event/add',$arr);
			$this->template->render();
		}else{ 
			//success...
			$arr=array(
				'event_name'	=>$this->input->post('event_name'),
				'event_start'	=>mktime(
					0,0,0,
					$this->input->post('start_mo'),
					$this->input->post('start_day'),
					$this->input->post('start_yr')
				),
				'event_end'		=>mktime(
					23,59,59,
					$this->input->post('end_mo'),
					$this->input->post('end_day'),
					$this->input->post('end_yr')
				),
				'event_loc'		=>$this->input->post('event_loc'),
				'event_desc'	=>$this->input->post('event_desc'),
				'active'		=>'1',
				'event_tz'		=>$this->input->post('event_tz')
			);
			if($id){
				//edit...
				$this->db->where('id',$this->edit_id);
				$this->db->update('events',$arr);
			}else{ $this->db->insert('events',$arr); }
			
			$arr=array(
				'msg'	=> 'Data saved! <a href="/event/view/'.$id.'">View event</a>',
				'tz'	=> $this->tz_model->getContInfo()
			);
			echo 'here';
			$this->template->write_view('content','event/add',$arr);
			$this->template->render();
		}
	}
	function edit($id){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->add($id);
	}
	function view($id){
		$this->load->helper('form');
		$this->load->helper('reqkey');
		$this->load->library('validation');
		$this->load->model('event_model');
		$this->load->model('event_comments_model');
		$this->load->model('user_attend_model');
		
		$talks	= $this->event_model->getEventTalks($id);
		$events	= $this->event_model->getEventDetail($id);
		$is_auth= $this->user_model->isAuth();
		
		if($is_auth){ 
			$uid=$this->session->userdata('ID');
			$chk_attend=($this->user_attend_model->chkAttend($uid,$id)) ? true : false;
			
		}else{ $chk_attend=false; }
		
		if(empty($events)){ redirect('event'); }
		$reqkey=buildReqKey();
		
		$arr=array(
			'events' =>$events,
			'talks'  =>$talks,
			'admin'	 =>($this->user_model->isAdminEvent($id)) ? true : false,
			'claimed'=>$this->event_model->getClaimedTalks($id),
			'user_id'=>($is_auth) ? $this->session->userdata('ID') : '0',
			'attend' =>$chk_attend,
			'reqkey' =>$reqkey,
			'seckey' =>buildSecFile($reqkey)
		);
		
		//our event comment form
		$rules=array(
			'event_comment'	=> 'required'
		);
		$fields=array(
			'event_comment'	=>'Event Comment'
		);
		if(!$is_auth){
			$rules['cname']	= 'required';
			$fields['cname']= 'Name';
		}
		$this->validation->set_fields($fields);
		$this->validation->set_rules($rules);
		
		if($this->validation->run()!=FALSE){
			$ec=array(
				'event_id'	=> $id,
				'comment'	=> $this->input->post('event_comment'),
				'date_made'	=> time(),
				'active'	=> 1
			);
			if($is_auth){
				$ec['user_id']	= $this->session->userdata('ID');
				$ec['cname']	= $this->session->userdata('username');
			}else{
				$ec['user_id']	= 0;
				$ec['cname']	= $this->input->post('cname');
			}
			
			$this->db->insert('event_comments',$ec);
			$arr['msg']='Comment inserted successfully!';
			
			$to		='enygma@phpdeveloper.org';
			$subj	='Joind.in: Event feedback - '.$id;
			$content='';
			foreach($ec as $k=>$v){ $content.='['.$k.'] => '.$v."\n\n"; }
			mail($to,$subj,$content,'From:feedback@joind.in');
		}
		
		$arr['comments']=$this->event_comments_model->getEventComments($id);
		
		$this->template->write_view('content','event/detail',$arr,TRUE);
		$this->template->render();
		//$this->load->view('event/detail',$arr);
	}
	function delete($id){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('event_model');
		
		$arr=array(
			'eid'		=> $id,
			'details'	=> $this->event_model->getEventDetail($id)
		);
		if(isset($_POST['answer']) && $_POST['answer']=='yes'){
			$this->event_model->deleteEvent($id);
			$arr=array();
		}
		
		$this->template->write_view('content','event/delete',$arr,TRUE);
		$this->template->render();
		//$this->load->view('event/delete',$arr);
	}
	function codes($id){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->library('events');
		$this->load->helper('url');
		
		if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($id)){ 
			//they're okay
		}else{ redirect(); }
				
		$rules=array();
		$fields=array();
		
		//make our code list for the talks
		$this->load->model('event_model');
		$codes=array();
		$talks=$this->event_model->getEventTalks($id);
		foreach($talks as $k=>$v){
			$str='ec'.str_pad(substr($v->ID,0,2),2,0,STR_PAD_LEFT).str_pad($v->event_id,2,0,STR_PAD_LEFT);
			$str.=substr(md5($v->talk_title),5,5);
			
			$codes[]=$str;
			
			//$rules['email_'.$v->ID]='trim|valid_email';
			$rules['email_'.$v->ID]	='callback_chk_email_check';
			$fields['email_'.$v->ID]='speaker email';
		}
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		$arr=array(
			'talks'		=> $talks,
			'codes'		=> $codes,
			'details'	=> $this->event_model->getEventDetail($id),
			'claimed'	=> $this->event_model->getClaimedTalks($id)
		);
		if($this->validation->run()!=FALSE){
			foreach($talks as $k=>$v){
				$pv=$this->input->post('email_'.$v->ID);
				$chk=$this->input->post('email_chk_'.$v->ID);
				if(!empty($pv) && $chk==1){
					//these are the ones we need to send the email to these
					$this->events->sendCodeEmail($pv,$codes[$k],$arr['details'],$v->ID);
				}
			}
		}else{ /*echo 'fail';*/ }
		$this->template->write_view('content','event/codes',$arr,TRUE);
		$this->template->render();
	}
	function submit(){
		$arr=array();
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
		
		$fields=array(
			'event_title'			=> 'Event Title',
			'event_contact_name'	=> 'Event Contact Name',
			'event_contact_email'	=> 'Event Contact Email',
			'event_desc'			=> 'Event Description',
			'start_mo'				=> 'Event Start Month',
			'start_day'				=> 'Event Start Day',
			'start_yr'				=> 'Event Start Year',
		//	'cinput'				=> 'Captcha'
		);
		$rules=array(
			'event_title'			=> 'required',
			'event_contact_name'	=> 'required',
			'event_contact_email'	=> 'required|valid_email',
			'event_desc'			=> 'required',
			'start_mo'				=> 'callback_start_mo_check',
		//	'cinput'				=> 'required|callback_cinput_check'
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
			
			//send the information via email...
			$t=mktime(
				0,0,0,
				$this->input->post('start_mo'),
				$this->input->post('start_day'),
				$this->input->post('start_yr')
			);
			$to		= 'enygma@phpdeveloper.org';
			$subj	= 'Event submission from Joind.in';
			$msg='Event Title: '.$this->input->post('event_title')."\n\n";
			$msg.='Event Description: '.$this->input->post('event_desc')."\n\n";
			$msg.='Event Date: '.date('m.d.Y H:i:s',$t)."\n\n";
			$msg.='Event Contact Name: '.$this->input->post('event_contact_name')."\n\n";
			$msg.='Event Contact Email: '.$this->input->post('event_contact_email')."\n\n";
			$msg.='Spam check: '.($ret=='false') ? 'not spam' : 'spam';
			
			mail($to,$subj,$msg,'From: submissions@joind.in');
			$arr['msg']='Event successfully submitted! We\'ll get back with you soon!';
		}
		//$cap = create_captcha($cap_arr);
		//$this->session->set_userdata(array('cinput'=>$cap['word']));
		//$arr['cap']=$cap;
		
		$this->template->write_view('content','event/submit',$arr);
		$this->template->render();
	}
	function export($eid){
		//export the speakers and their ratings/comments for an entire event
		//push it out as a CSV file...
		$this->load->model('event_model');
		$talks=$this->event_model->getEventFeedback($eid);
		
		$fp=fopen('php://memory','w+');
		foreach($talks as $k=>$v){
			fputcsv($fp,(array)$v);
		}
		//print_r($talks);
		rewind($fp);
		$out=stream_get_contents($fp);
		fclose($fp);
		
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="Event_Comments_'.$eid.'.csv"');
		echo $out;
	}
	//----------------------
	function start_mo_check($str){
		$t=mktime(
			0,0,0,
			$this->validation->start_mo,
			$this->validation->start_day,
			$this->validation->start_yr
		);
		if($t<=time()){
			$this->validation->set_message('start_mo_check','Start date must be in the future!');
			return false;
		}else{ return true; }
	}
	function end_mo_check($str){
		$st=mktime(
			0,0,0,
			$this->validation->start_mo,
			$this->validation->start_day,
			$this->validation->start_yr
		);
		$et=mktime(
			23,59,59,
			$this->validation->end_mo,
			$this->validation->end_day,
			$this->validation->end_yr
		);
		if($et<$st){
			$this->validation->set_message('end_mo_check','End month must be past the start date!');
			return false;
		}else{ return true; }
	}
	function chk_email_check($str){
		$chk_str=str_replace('_','_chk_',$this->validation->_current_field);
		$val=$this->input->post($chk_str);
		if($val==1 && !$this->validation->valid_email($str)){
			$this->validation->set_message('chk_email_check','Email address invalid!');
			return false;
		}else{ return true; }
	}
	function cinput_check($str){
		if($this->input->post('cinput') != $this->session->userdata('cinput')){
			$this->validation->_error_messages['cinput_check'] = 'Incorrect Captcha characters.';
			return FALSE;                            
		}else{ return TRUE; }
	}
	//----------------------
}

?>