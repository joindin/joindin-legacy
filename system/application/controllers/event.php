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
		$this->load->helper('reqkey');
		//$this->load->library('calendar',$prefs);
		$this->load->model('event_model');
		$this->load->helper('mycal');
		
		$events = $this->event_model->getEventDetail();
		$reqkey = buildReqKey();
		
		$arr=array(
			'events' =>$events,
			//'admin'	 =>($this->user_model->isAdminEvent($id)) ? true : false
			'month'	=> null,
			'day'	=> null,
			'year'	=> null,
			'all'	=> true,
			'reqkey' => $reqkey,
			'seckey' => buildSecFile($reqkey)
		);	
		$this->template->write_view('content','event/main',$arr,TRUE);
		$this->template->render();
		
		//$this->load->view('event/main',array('events'=>$events));
	}
	function calendar($year = null, $month = null, $day = null){
		$this->load->model('event_model');
		$this->load->helper('reqkey');
		$this->load->helper('mycal');

		if (!$year) {
		    $year = date('Y');
		}
		
	    if (!$month) {
		    $month = date('m');
		}

		$checkDay = $day === null ? 1 : $day;

		if (!checkdate((int)$month, (int)$checkDay, (int)$year)) {
		    $day   = null;
		    $month = date('m');
		    $year  = date('Y');
		}

		$start	= mktime(0,   0,  0, $month, $day === null ? 1                 : $day, $year);
		$end	= mktime(23, 59, 59, $month, $day === null ? date('t', $start) : $day, $year);

		$events	= $this->event_model->getEventDetail(null, $start, $end);
		
		/*$date_p	= explode('_',$date);
		if(count($date_p)==2){
			$start	= mktime(0,0,0,$date_p[0],1,$date_p[1]);
			$end	= mktime(0,0,0,$date_p[0],date('t',$start),$date_p[1]);	
		}else{
			$start	= mktime(0,0,0,$date_p[0],1,$date_p[2]);
			$end	= mktime(0,0,0,$date_p[0],date('t',$start),$date_p[2]);
		}		
		$events	= $this->event_model->getEventDetail(null,$start,$end);
		*/
		$reqkey = buildReqKey();

		/*$arr=array('events'=>$events,'mo'=>$date_p[0]);
		if(count($date_p)==2){
			$arr['day']	= 1;
			$arr['yr']	= $date_p[1];
		}else{ 
			$arr['day']	= $date_p[1];
			$arr['yr']	= $date_p[2];
		}*/
		$arr=array(
			'events' => $events,
			'month'	 => $month,
			'day'	 => $day,
			'year'	 => $year,
			'reqkey' => $reqkey,
			'seckey' => buildSecFile($reqkey)
		);

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
		
		$config['upload_path'] 	= $_SERVER['DOCUMENT_ROOT'].'/inc/img/event_icons';
		$config['allowed_types']= 'gif|jpg|png';
		$config['max_size']		= '100';
		$config['max_width']  	= '90';
		$config['max_height']  	= '90';
		$this->load->library('upload', $config);
		
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
			'event_tz'	=>'Event Timezone',
			'event_href'=>'Event Link(s)',
			'event_hashtag'=>'Event Hashtag'
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
				'event_tz'		=>$this->input->post('event_tz'),
				'event_href'	=>$this->input->post('event_href'),
				'event_hashtag'	=>$this->input->post('event_hashtag')
			);
			if($this->upload->do_upload('event_icon')){
				$updata=$this->upload->data();
				$arr['event_icon']=$updata['file_name'];
			}
			if($id){
				//edit...
				$this->db->where('id',$this->edit_id);
				$this->db->update('events',$arr);
			}else{ 
				$this->db->insert('events',$arr); 
				$id=$this->db->insert_id();				
			}
			
			$arr=array(
				'msg'	=> 'Data saved! <a href="/event/view/'.$id.'">View event</a>',
				'tz'	=> $this->tz_model->getContInfo()
			);
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
		$this->load->helper('events');
		$this->load->library('validation');
		$this->load->library('defensio');
		$this->load->model('event_model');
		$this->load->model('event_comments_model');
		$this->load->model('user_attend_model','uam');
		
		$talks	= $this->event_model->getEventTalks($id);
		$events	= $this->event_model->getEventDetail($id);
		$is_auth= $this->user_model->isAuth();
		
		foreach($talks as $k=>$v){
			$codes=array();
			$p=explode(',',$v->speaker);
			foreach($p as $ik=>$iv){
				$val=trim($iv);
				$talks[$k]->codes[$val]=buildCode($v->ID,$v->event_id,$v->talk_title,$val);
			}
		}
		
		if($is_auth){ 
			$uid=$this->session->userdata('ID');
			$chk_attend=($this->uam->chkAttend($uid,$id)) ? true : false;
			
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
			'attend_ct'=>$this->uam->getAttendCount($id),
			'reqkey' =>$reqkey,
			'seckey' =>buildSecFile($reqkey),
			//'attend' =>$this->uam->getAttendCount($id)
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
			$def_ret=$this->defensio->check($ec['cname'],$ec['comment'],$is_auth,'/event/view/'.$id);
			
			$is_spam=(string)$def_ret->spam;
			if($is_spam=='false'){
				$this->db->insert('event_comments',$ec);
				$arr['msg']='Comment inserted successfully!';
		
			
				if($def_ret){
					$ec['def_resp_spamn']=(string)$def_ret->spaminess;
					$ec['def_resp_spamr']=(string)$def_ret->spam;
				}
				//print_r($ec);
			
				$to		='enygma@phpdeveloper.org';
				$subj	='Joind.in: Event feedback - '.$id;
				$content='';
				foreach($ec as $k=>$v){ $content.='['.$k.'] => '.$v."\n\n"; }
				@mail($to,$subj,$content,'From:feedback@joind.in');
			
				$this->session->set_flashdata('msg', 'Comment inserted successfully!');
			}
			
			redirect('event/view/'.$events[0]->ID . '#comments', 'location', 302);
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
		$this->load->helper('events');
		
		if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($id)){ 
			//they're okay
		}else{ redirect(); }
				
		$rules=array();
		$fields=array();
		
		//make our code list for the talks
		$this->load->model('event_model');
		$codes		= array();
		$full_talks	= array();
		$talks=$this->event_model->getEventTalks($id);
		foreach($talks as $k=>$v){
			$sp=explode(',',$v->speaker); //echo '<pre>'; print_r($sp); echo '</pre>';
			
			foreach($sp as $sk=>$sv){				
				//$str='ec'.str_pad(substr($v->ID,0,2),2,0,STR_PAD_LEFT).str_pad($v->event_id,2,0,STR_PAD_LEFT);
				//$str.=substr(md5($v->talk_title.$sk),5,5);
				$str=buildCode($v->ID,$v->event_id,$v->talk_title,trim($sv));
			
				$codes[]		= $str;

				$obj=clone $v;
				$obj->code		= $str;
				$obj->speaker	= trim($sv);
				$full_talks[]	= $obj;
			
				//$rules['email_'.$v->ID]='trim|valid_email';
				$rules['email_'.$v->ID]	='callback_chk_email_check';
				$fields['email_'.$v->ID]='speaker email';
			}
		}
		//echo '<pre>'; print_r($full_talks); echo '</pre>';
		
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		$claimed=array();
		
		$cl=$this->event_model->getClaimedTalks($id); //echo '<pre>'; print_r($cl); echo '</pre>';
		foreach($cl as $k=>$v){
			//$cstr='ec'.str_pad(substr($v->rid,0,2),2,0,STR_PAD_LEFT).str_pad($v->tdata['event_id'],2,0,STR_PAD_LEFT);
			//$cstr.=substr(md5($v->tdata['talk_title'].$sk),5,5);
			$cds=array();
			$sp=explode(',',$v->tdata['speaker']); //print_r($sp);
			foreach($sp as $spk=>$spv){
				$code=buildCode($v->rid,$v->tdata['event_id'],$v->tdata['talk_title'],trim($spv));
				if($code==$v->rcode){ $cl[$k]->code=$code; }
			}
			//$cl[$k]->codes=$cds;
		}
		//echo '<pre>'; print_r($cl); echo '</pre>';
		
		$arr=array(
			'talks'		=> $talks,
			'full_talks'=> $full_talks,
			'codes'		=> $codes,
			'details'	=> $this->event_model->getEventDetail($id),
			'claimed'	=> $cl
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
		$this->load->library('validation');
		$this->load->plugin('captcha');
		//$this->load->library('akismet');
		$this->load->library('defensio');
		
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
			'end_mo'				=> 'Event End Month',
			'end_day'				=> 'Event End Day',
			'end_yr'				=> 'Event End Year',
			'event_loc'				=> 'Event Location',
			'event_stub'			=> 'Event Stub'
		//	'cinput'				=> 'Captcha'
		);
		$rules=array(
			'event_title'			=> 'required',
			'event_loc'				=> 'required',
			'event_contact_name'	=> 'required',
			'event_contact_email'	=> 'required|valid_email',
			'start_mo'				=> 'callback_start_mo_check',
			'end_mo'				=> 'callback_end_mo_check',
			'event_stub'			=> 'callback_stub_check',
			'event_desc'			=> 'required',
		//	'cinput'				=> 'required|callback_cinput_check'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		//if we're just loading, give the dates some default values
		if(empty($this->validation->start_mo)){
			$this->validation->start_mo	= date('m');
			$this->validation->start_day= date('d');
			$this->validation->start_yr	= date('Y');
			
			$this->validation->end_mo	= date('m');
			$this->validation->end_day	= date('d');
			$this->validation->end_yr	= date('Y');
		}
		
		if($this->validation->run()!=FALSE){			
			//TODO: add it to our database, but mark it pending
			$sub_arr=array(
				'event_name'	=>$this->input->post('event_title'),
				'event_start'	=>mktime(
					0,0,0,
					$this->input->post('start_mo'),
					$this->input->post('start_day'),
					$this->input->post('start_yr')
				),
				'event_end'		=>mktime(
					0,0,0,
					$this->input->post('end_mo'),
					$this->input->post('end_day'),
					$this->input->post('end_yr')
				),
				'event_loc'		=>$this->input->post('event_loc'),
				'event_desc'	=>$this->input->post('event_desc'),
				'active'		=>0,
				'event_stub'	=>$this->input->post('event_stub'),
				'event_tz'		=>$this->input->post('event_tz'),
				'pending'		=>1
			);
			
			//echo '<pre>'; print_r($sub_arr); echo '</pre>';
			
			//----------------------
			$is_auth	= $this->user_model->isAuth();
			$cname		= $this->input->post('event_contact_name');
			$ccomment	= $this->input->post('event_desc');
			$def        = $this->defensio->check($cname,$ccomment,$is_auth,'/event/submit');		
			$is_spam	= (string)$def->spam;
			//-----------------------
			
			if($is_spam!='true'){			
				//send the information via email...
				$to		= 'enygma@phpdeveloper.org';
				$subj	= 'Event submission from Joind.in';
				$msg= 'Event Title: '.$this->input->post('event_title')."\n\n";
				$msg.='Event Description: '.$this->input->post('event_desc')."\n\n";
				$msg.='Event Date: '.date('m.d.Y H:i:s',$sub_arr['event_start'])."\n\n";
				$msg.='Event Contact Name: '.$this->input->post('event_contact_name')."\n\n";
				$msg.='Event Contact Email: '.$this->input->post('event_contact_email')."\n\n";
				$msg.='Spam check: '.($is_spam=='false') ? 'not spam' : 'spam';
			
				echo $msg.'<br/><br/>';
			
				mail($to,$subj,$msg,'From: submissions@joind.in');
				$arr['msg']='Event successfully submitted! We\'ll get back with you soon!';
				
				//put it into the database
				$this->db->insert('events',$sub_arr);
			}else{ 
				$arr['msg']='There was an error submitting your event! Please <a href="submissions@joind.in">send us an email</a> with all the details!';
			}
		}
		
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
	function stub_check($str){
		if(!empty($str)){
			$this->load->model('event_model');
			$ret=$this->event_model->isUniqueStub($str);
			if(!$ret){
				$this->validation->set_message('stub_check','Please choose another stub - this one\'s already in use!');
				return false;
			}else{ return true; }
		}else{ return true; }
	}
	//----------------------
}

?>
