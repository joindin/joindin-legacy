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
	function _runList($type, $pending = false)
	{
		$prefs = array (
			'show_next_prev'  => TRUE,
			'next_prev_url'   => '/event'
		);
		
		$this->load->helper('form');
		$this->load->helper('reqkey');
		//$this->load->library('calendar',$prefs);
		$this->load->model('event_model');
		$this->load->helper('mycal');
		
		switch ($type) {
		    case 'hot':
		        $events = $this->event_model->getHotEvents(null);
		        break;
		    case 'upcoming':
		        $events = $this->event_model->getUpcomingEvents(null);
		        break;
		    case 'past':
		        $events = $this->event_model->getPastEvents(null);
		        break;
		    default:
		        $events = $this->event_model->getEventDetail(null,null,null,$pending);
		        break;
		}

		$reqkey = buildReqKey();
		
		$arr=array(
			'type' => $type,
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

	function index($pending=false){
		$this->_runList('index', $pending);
	}
	
    function hot($pending=false){
		$this->_runList('hot', $pending);
	}
	
    function upcoming($pending=false){
		$this->_runList('upcoming', $pending);
	}
	
    function past($pending=false){
		$this->_runList('past', $pending);
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
		if($id){ 
			if(!$this->user_model->isAdminEvent($id)){ redirect(); } 
		}else{
			if(!$this->user_model->isSiteAdmin()){ redirect(); }
		}
		
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
			//'event_tz'		=> 'required',
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

		$event_detail=array();
		if($this->validation->run()==FALSE){
			if($id){
				//we're editing here...
				$event_detail=$this->event_model->getEventDetail($id);
				foreach($event_detail[0] as $k=>$v){
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
				'tz'	=> $this->tz_model->getOffsetInfo(),
				'detail'=> $event_detail
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
				$event_detail=$this->event_model->getEventDetail($id);
			}else{ 
				$this->db->insert('events',$arr); 
				$id=$this->db->insert_id();				
			}
			
			$arr=array(
				'msg'	=> 'Data saved! <a href="/event/view/'.$id.'">View event</a>',
				'tz'	=> $this->tz_model->getContInfo(),
				'detail'=> $event_detail
			);
			$this->template->write_view('content','event/add',$arr);
			$this->template->render();
		}
	}
	function edit($id){
		if(!$this->user_model->isAdminEvent($id)){ redirect(); }
		$this->add($id);
	}
	function view($id){
		$this->load->helper('form');
		$this->load->helper('reqkey');
		$this->load->helper('events');
		$this->load->library('validation');
		$this->load->library('defensio');
		$this->load->library('spam');
		$this->load->library('twitter');
		$this->load->model('event_model');
		$this->load->model('event_comments_model');
		$this->load->model('user_attend_model','uam');
		
		$events	= $this->event_model->getEventDetail($id);
		$talks	= $this->event_model->getEventTalks($id);
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
		
		$attend=$this->uam->getAttendUsers($id);
		$arr=array(
			'events' =>$events,
			'talks'  =>$talks,
			'admin'	 =>($this->user_model->isAdminEvent($id)) ? true : false,
			'claimed'=>$this->event_model->getClaimedTalks($id),
			'user_id'=>($is_auth) ? $this->session->userdata('ID') : '0',
			'attend' =>$chk_attend,
			'attend_ct'=>count($attend),
			'reqkey' =>$reqkey,
			'seckey' =>buildSecFile($reqkey),
			'attending'=>$attend,
			'latest_comment'=>$this->event_model->getLatestComment($id)
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
			// If it's before the event, it's a "vote" & after is 
			// a normal comment (empty)
			$type=(time()<$events[0]->event_start) ? 'vote' : '';
			
			$ec=array(
				'event_id'		=> $id,
				'comment'		=> $this->input->post('event_comment'),
				'date_made'		=> time(),
				'active'		=> 1,
				'comment_type'	=> $type
			);
			if($is_auth){
				$ec['user_id']	= $this->session->userdata('ID');
				$ec['cname']	= $this->session->userdata('username');
			}else{
				$ec['user_id']	= 0;
				$ec['cname']	= $this->input->post('cname');
			}
			$def_ret=$this->defensio->check($ec['cname'],$ec['comment'],$is_auth,'/event/view/'.$id);
			
			//$this->spam->check('regex',$ec['comment']);
			
			$is_spam=(string)$def_ret->spam;
			if($is_spam=='false'){
				$this->db->insert('event_comments',$ec);
				$arr['msg']='Comment inserted successfully!';
		
			
				if($def_ret){
					$ec['def_resp_spamn']=(string)$def_ret->spaminess;
					$ec['def_resp_spamr']=(string)$def_ret->spam;
				}
				//print_r($ec);

				$to=array('enygma@phpdveloper.org');
				
				// Get whatever email addresses there are for the event
				$admins=$this->event_model->getEventAdmins($id);
				foreach($admins as $ak=>$av){ $to[]=$av->email; }
				
				//$to	='enygma@phpdeveloper.org';
				$subj	='Joind.in: Event feedback - '.$id;
				$content='';
				foreach($ec as $k=>$v){ $content.='['.$k.'] => '.$v."\n\n"; }
				foreach($to as $tk=>$tv){
				    @mail($tv,$subj,$content,'From:feedback@joind.in');
				}
			
				$this->session->set_flashdata('msg', 'Comment inserted successfully!');
			}
			
			redirect('event/view/'.$events[0]->ID . '#comments', 'location', 302);
		}

		$arr['comments']	= $this->event_comments_model->getEventComments($id);
		
		//$t=$this->twitter->querySearchAPI(explode(',',$arr['events'][0]->event_hashtag));

		// @tood for testing
		$t=array();
		$other_data=array('title'=>'Tagged on Twitter');
		if(!empty($t)){
			$other_data=array(
				'title'		=> 'Tagged on Twitter',
				'results'	=> $t,
			);
		}
		
		if(!$is_auth){
			$info=array('msg'=>sprintf('
				<h4 style="color:#3A74C5">New to Joind.in?</h4> Find out how we can help you make connections 
				whether you\'re attending or putting on the show. <a href="/about">Click here</a> to learn more!
			'));
			$this->template->write_view('info_block','msg_info',$info,TRUE);
		}
		
		$this->template->write('feedurl','/feed/event/'.$id);
		$this->template->write_view('content','event/detail',$arr,TRUE);
		$this->template->write_view('sidebar2','event/_twitter-search',$other_data);
		$this->template->render();
		//$this->load->view('event/detail',$arr);
	}
    function attendees($id){
		$this->load->model('user_attend_model');

		$users	= $this->user_attend_model->getAttendees($id);				
		
		$arr = array(
		    'users' => $users
		);

		$this->template->write_view('content','event/attendees',$arr,true);
		echo $this->template->render('content');
	}
	function ical($id){
		header('Content-type: text/calendar');
		header('Content-disposition: filename="ical.ics"'); 
	    $this->load->model('event_model');
		$arr=$this->event_model->getEventDetail($id);
		$this->load->view('event/ical',array('data'=>$arr));
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
		$ans=$this->input->post('answer');
		if(isset($ans) && $ans =='yes'){
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
			'is_cfp'				=> 'Is CfP',
			'cfp_start_day'			=> 'CfP Start Day',
			'cfp_start_mo'			=> 'CfP Start Month',
			'cfp_start_yr'			=> 'CfP Start Year',
			'cfp_end_day'			=> 'CfP End Day',
			'cfp_end_mo'			=> 'CfP End Month',
			'cfp_end_yr'			=> 'CfP End Year',
			'end_mo'				=> 'Event End Month',
			'end_day'				=> 'Event End Day',
			'end_yr'				=> 'Event End Year',
			'event_loc'				=> 'Event Location',
			'event_stub'			=> 'Event Stub'
		//	'cinput'				=> 'Captcha'
		);
		$rules=array(
			'event_title'			=> 'required|callback_event_title_check',
			'event_loc'				=> 'required',
			'event_contact_name'	=> 'required',
			'event_contact_email'	=> 'required|valid_email',
			'start_mo'				=> 'callback_start_mo_check',
			'end_mo'				=> 'callback_end_mo_check',
			'cfp_start_mo'			=> 'callback_cfp_start_mo_check',
			'cfp_end_mo'			=> 'callback_cfp_end_mo_check',
			'event_stub'			=> 'callback_stub_check',
			'event_desc'			=> 'required',
		//	'cinput'				=> 'required|callback_cinput_check'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		//if we're just loading, give the dates some default values
		if(empty($this->validation->start_mo)){
			$sel_fields=array(
				'start_mo'=>'m','start_day'=>'d','start_yr'=>'Y','end_mo'=>'m',
				'end_day'=>'d','end_yr'=>'Y','cfp_start_mo'=>'m','cfp_start_day'=>'d',
				'cfp_start_yr'=>'Y','cfp_end_mo'=>'m','cfp_end_day'=>'d','cfp_end_yr'=>'Y'
			);
			foreach($sel_fields as $k=>$v){ $this->validation->$k=date($v); }
			$this->validation->cfp_checked	= false;
		}else{
			$this->validation->cfp_checked=$this->validation->is_cfp;
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
			
			// Check to see if our Call for Papers dates are set...
			$cfp_check=$this->input->post('cfp_start_mo');
			if(!empty($cfp_check)){
				$sub_arr['event_cfp_start']=mktime(
					0,0,0,
					$this->input->post('cfp_start_mo'),
					$this->input->post('cfp_start_day'),
					$this->input->post('cfp_start_yr')
				);
				$sub_arr['event_cfp_end']=mktime(
					0,0,0,
					$this->input->post('cfp_end_mo'),
					$this->input->post('cfp_end_day'),
					$this->input->post('cfp_end_yr')
				);
			}
			
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
			
				//echo $msg.'<br/><br/>';
			
				mail($to,$subj,$msg,'From: submissions@joind.in');
				$arr['msg']='<style="font-size:13px;font-weight:bold">Event successfully submitted! We\'ll get back with you soon!</span>';
				
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
	function pending(){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->index(true);
	}
	function approve($id){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		
		$this->load->model('event_model');
		//$det=$this->event_model->getEventDetail($id); print_r($det);
		
		$this->event_model->approvePendingEvent($id);
		redirect('event/view/'.$id); 
	}
	function claim(){
           $this->load->model('user_admin_model','uam');
           $ret=$this->uam->getPendingClaims('event');

           $arr=array(
               'claims'=>$ret
           );
 
           $this->template->write_view('content','event/claim',$arr);
           $this->template->render();
       }
	/**
	 * Import an XML file and push the test information into the table
	 */
	function import($eid){
		// Be sure they're supposed to be here...
		if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($id)){
		//they're okay
		}else{ redirect(); }

		$this->load->library('validation');
		$this->load->library('xmlimport');
		$this->load->model('event_model','em');
		
		$config['upload_path'] 	= $_SERVER['DOCUMENT_ROOT'].'/inc/tmp';
		$config['allowed_types']= 'xml';
		$this->load->library('upload', $config);

		// Allow them to upload the XML or pull it from another resource
		//$rules   = array('xml_file'=>'required');
		$rules	 = array();
		$fields  = array('xml_file'=>'XML File');
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		$msg=null;
		
		if($this->upload->do_upload('xml_file')){
			// The file's there, lets run our import
			$updata	= $this->upload->data(); //print_r($updata);
			$p		= $config['upload_path'].'/'.$updata['file_name'];
			try{
				$data=file_get_contents($p);
				$this->xmlimport->import($data,'event',$eid);
			}catch(Exception $e){
				$msg='Error: '.$e->getMessage();
			}
			unlink($p);
		}else{
			//print_r($this->upload->display_errors()); 
			$this->upload->display_errors();
		}

		$arr=array(
			'details'	=> $this->em->getEventDetail($eid),
			'msg'		=> $msg
		);
		$this->template->write_view('content','event/import',$arr);
		$this->template->render();
	}
	//----------------------
	/**
	 * Check the database to be sure we don't have another event by this name, pending or not
	 */
	function event_title_check($str){
		$this->load->model('event_model');
		$ret=$this->event_model->getEventIdByTitle($str);
		if(isset($ret[0]->id)){
			$this->validation->set_message('event_title_check','There is already an event by that name!');
			return false;
		}
		return true;
	}
	function start_mo_check($str){
		//be sure it's before the end date
		$t=mktime(
			0,0,0,$this->validation->start_mo,$this->validation->start_day,$this->validation->start_yr
		);
		$e=mktime(
			0,0,0,$this->validation->end_mo,$this->validation->end_day,$this->validation->end_yr
		);
		if($t>$e){
			$this->validation->set_message('start_mo_check','Start date must be prior to the end date!');
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
	/**
	 * Ensure that the date given for the CFP start is before the event date's
	 * and that it's before the cfp_end dates
	 */
	function cfp_start_mo_check(){
		$cfp_st=mktime(0,0,0,
			$this->validation->cfp_start_mo,$this->validation->cfp_start_day,$this->validation->cfp_start_yr
		);
		$cfp_end=mktime(0,0,0,
			$this->validation->cfp_end_mo,$this->validation->cfp_end_day,$this->validation->cfp_end_yr
		);
		$evt_st=mktime(0,0,0,
			$this->validation->start_mo,$this->validation->start_day,$this->validation->start_yr
		);
		if($cfp_st>=$evt_st){
			$this->validation->set_message('cfp_start_mo_check','Call for Papers must start before the event!');
			return false;
		}
		if($cfp_st>=$cfp_end){
			$this->validation->set_message('cfp_start_mo_check','Invalid Call for Papers start date!');
			return false;
		}
		return true;
	}
	/**
	 * Ensure that the date given for the CFP's end is before the start of the event
	 * and that the end date is after the CFP start date
	 */
	function cfp_end_mo_check(){
		$cfp_end=mktime(0,0,0,
			$this->validation->cfp_end_mo,$this->validation->cfp_end_day,$this->validation->cfp_end_yr
		);
		$evt_st=mktime(0,0,0,
			$this->validation->start_mo,$this->validation->start_day,$this->validation->start_yr
		);
		if($cfp_end>=$evt_st){
			$this->validation->set_message('cfp_start_mo_check','Invalid Call for Papers end date! CfP must end before event start!');
			return false;
		}
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
