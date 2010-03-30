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
		$this->load->library('timezone');
		//$this->load->library('calendar',$prefs);
		$this->load->model('event_model');
		$this->load->model('user_attend_model');
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
		
		// now add the attendance data
		$uid = $this->user_model->getID();
		foreach($events as $e) {
			if($uid) {
				$e->user_attending = $this->user_attend_model->chkAttend($uid, $e->ID);
			}else{ $e->user_attending=false; }
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
		
		//$this->template->write_view('info_block','msg_info',array('msg'=>'test'),TRUE);
		
		$this->template->write_view('content','event/main',$arr,TRUE);
		$this->template->render();
		
		//$this->load->view('event/main',array('events'=>$events));

	}

	function index($pending=false){
		$type=($pending) ? 'pending' : 'upcoming';
		$this->_runList($type, $pending);
	}
	
	function all($pending=false){
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
		$this->load->model('user_attend_model');
		$this->load->helper('reqkey');
		$this->load->helper('mycal');
		$this->load->library('timezone');

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
		// now add the attendance information
		$uid = $this->user_model->getID();
		foreach($events as $e) {
			if($uid) {
				$e->user_attending = $this->user_attend_model->chkAttend($uid, $e->ID);
			} else {
				$e->user_attending = false;
			}

		}
		
		$reqkey = buildReqKey();

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
		$this->load->helper('custom_timezone');
		$this->load->library('validation');
		$this->load->library('timezone');
		$this->load->model('event_model');
		
		$config['upload_path'] 	= $_SERVER['DOCUMENT_ROOT'].'/inc/img/event_icons';
		$config['allowed_types']= 'gif|jpg|png';
		$config['max_size']		= '100';
		$config['max_width']  	= '90';
		$config['max_height']  	= '90';
		$this->load->library('upload', $config);
		
		$rules=array(
			'event_name'	=> 'required',
			'event_loc'		=> 'required',
			'event_tz_cont'		=> 'required',
			'event_tz_place'	=> 'required',
			'start_mo'		=> 'callback_start_mo_check',
			'end_mo'		=> 'callback_end_mo_check',
			'event_stub'	=> 'callback_stub_check'
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
			'event_tz_cont'		=>'Event Timezone (Continent)',
			'event_tz_place'	=>'Event Timezone (Place)',
			'event_href'=>'Event Link(s)',
			'event_hashtag'=>'Event Hashtag',
			'event_voting'=>'Event Voting Allowed',
			'event_private'=>'Private Event',
			'event_stub'=>'Event Stub'
		);
		$this->validation->set_fields($fields);

		$event_detail	= array();
		$min_start_yr	= date('Y');
		$min_end_yr		= date('Y');
		if($this->validation->run()==FALSE){
			if($id){
				//we're editing here...
				$event_detail=$this->event_model->getEventDetail($id); 
				
				if(date('Y',$event_detail[0]->event_start)<$min_start_yr){
					$min_start_yr=date('Y',$event_detail[0]->event_start);
				}
				if(date('Y',$event_detail[0]->event_end)<$min_end_yr){
					$min_end_yr=date('Y',$event_detail[0]->event_end);
				}
				
				foreach($event_detail[0] as $k=>$v){
					if($k=='event_start'){
						$this->validation->start_mo	= $this->timezone->formattedEventDatetimeFromUnixtime($v, $event_detail[0]->event_tz_cont.'/'.$event_detail[0]->event_tz_place, 'm');
						$this->validation->start_day	= $this->timezone->formattedEventDatetimeFromUnixtime($v, $event_detail[0]->event_tz_cont.'/'.$event_detail[0]->event_tz_place, 'd');
						$this->validation->start_yr	= $this->timezone->formattedEventDatetimeFromUnixtime($v, $event_detail[0]->event_tz_cont.'/'.$event_detail[0]->event_tz_place, 'Y');
					}elseif($k=='event_end'){
						$this->validation->end_mo	= $this->timezone->formattedEventDatetimeFromUnixtime($v, $event_detail[0]->event_tz_cont.'/'.$event_detail[0]->event_tz_place, 'm');
						$this->validation->end_day	= $this->timezone->formattedEventDatetimeFromUnixtime($v, $event_detail[0]->event_tz_cont.'/'.$event_detail[0]->event_tz_place, 'd');
						$this->validation->end_yr	= $this->timezone->formattedEventDatetimeFromUnixtime($v, $event_detail[0]->event_tz_cont.'/'.$event_detail[0]->event_tz_place, 'Y');
					}else{ $this->validation->$k=$v; }
				}
				$this->validation->event_private=$event_detail[0]->private;
			}
			$arr=array(
				'detail'		=> $event_detail,
				'min_start_yr'	=> $min_start_yr,
				'min_end_yr'	=> $min_end_yr
			);
			$this->template->write_view('content','event/add',$arr);
			$this->template->render();
		}else{ 
			//success...
			$arr=array(
				'event_name'	=>$this->input->post('event_name'),
				'event_start'	=>$this->timezone->UnixtimeForTimeInTimezone(
					$this->input->post('event_tz_cont').'/'.$this->input->post('event_tz_place'),
					$this->input->post('start_yr'),
					$this->input->post('start_mo'),
					$this->input->post('start_day'),
					0,0,0
				),
				'event_end'	=>$this->timezone->UnixtimeForTimeInTimezone(
					$this->input->post('event_tz_cont').'/'.$this->input->post('event_tz_place'),
					$this->input->post('end_yr'),
					$this->input->post('end_mo'),
					$this->input->post('end_day'),
					23,59,59
				),
				'event_loc'		=>$this->input->post('event_loc'),
				'event_desc'	=>$this->input->post('event_desc'),
				'active'		=>'1',
				'event_tz_cont'	=>$this->input->post('event_tz_cont'),
				'event_tz_place'	=>$this->input->post('event_tz_place'),
				'event_href'	=>$this->input->post('event_href'),
				'event_hashtag'	=>$this->input->post('event_hashtag'),
				'event_voting'	=>$this->input->post('event_voting'),
				'private'		=>$this->input->post('event_private'),
				'event_tz_cont'		=>$this->input->post('event_tz_cont'),
				'event_tz_place'	=>$this->input->post('event_tz_place'),
				'event_stub'	=>$this->input->post('event_stub')
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
				'min_start_yr'	=> $min_start_yr,
				'min_end_yr'	=> $min_end_yr,
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
	function view($id,$opt=null,$opt_id=null){
		$this->load->helper('form');
		$this->load->helper('reqkey');
		$this->load->helper('events');
		$this->load->library('validation');
		$this->load->library('defensio');
		$this->load->library('spam');
		$this->load->library('twitter');
		$this->load->library('timezone');
		$this->load->model('event_model');
		$this->load->model('event_comments_model');
		$this->load->model('user_attend_model','uam');
		$this->load->model('event_blog_posts_model','ebp');
		$this->load->model('talk_track_model','ttm');
		$this->load->model('event_track_model','etm');
		$this->load->model('talks_model');
		
		$events		= $this->event_model->getEventDetail($id);
		$evt_admins	= $this->event_model->getEventAdmins($id);
		if($events[0]->private=='Y'){
			$this->load->model('invite_list_model','ilm');
						
			// Private event! Check to see if they're on the invite list!
			$is_auth	= $this->user_model->isAuth();
			$priv_admin	= ($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($id)) ? true : false;
			if($is_auth){
				$udata=$this->user_model->getUser($is_auth);
				$is_invite=$this->ilm->isInvited($id,$udata[0]->ID);
				
				//If they're invited, accept if they haven't already
				if($is_invite){ $this->ilm->acceptInvite($id,$udata[0]->ID); }
				
				if(!$is_invite && !$priv_admin){
					$arr=array('detail'=>$events,'is_auth'=>$is_auth,'admins'=>$evt_admins);
					$this->template->write_view('content','event/private',$arr,TRUE);
				
					// Render the page
					$this->template->render();
					return true;
				}
			}else{ 
				$arr=array('detail'=>$events,'is_auth'=>$is_auth,'admins'=>$evt_admins);
				$this->template->write_view('content','event/private',$arr,TRUE);
			
				// Render the page
				$this->template->render();
				return true;
			}
		}
		
		$talks	= $this->event_model->getEventTalks($id, false);
		$is_auth= $this->user_model->isAuth();
		
		foreach($talks as $k=>$v){
			$codes=array();
			$p=explode(',',$v->speaker);
			foreach($p as $ik=>$iv){
				$val=trim($iv);
				$talks[$k]->codes[$val]=buildCode($v->ID,$v->event_id,$v->talk_title,$val);
			}
			$talks[$k]->tracks=$this->ttm->getSessionTrackInfo($v->ID);
			
			//If we have a track filter, check it!
			if(strtolower($opt)=='track' && isset($opt_id)){
				$has_track=false;
				foreach($talks[$k]->tracks as $track){
					if($track->ID==$opt_id){ $has_track=true; }
				}
				if(!$has_track){ unset($talks[$k]); }
			}
		}
		
		if($is_auth){ 
			$uid=$this->session->userdata('ID');
			$chk_attend=($this->uam->chkAttend($uid,$id)) ? true : false;
		}else{ $chk_attend=false; }
		
		if(empty($events)){ redirect('event'); }
		if($events[0]->pending==1 && !$this->user_model->isSiteAdmin()){
			$parr=array('detail'=>$events);
			$this->template->write_view('content','event/pending',$parr,true);
			echo $this->template->render();	
			return true;
		}
		
		$reqkey			= buildReqKey();
		$attend			= $this->uam->getAttendUsers($id);
		$talks 			= $this->talks_model->setDisplayFields($talks);
		$claimed_talks	= $this->event_model->getClaimedTalks($id);
		$claim_detail	= buildClaimDetail($claimed_talks);
		$event_related_sessions = $this->event_model->getEventRelatedSessions($id);
		
		$arr=array(
			'event_detail'	=>$events[0],
			'talks'			=> $talks,
			'evt_sessions'	=> $event_related_sessions,
			'slides_list'	=>buildSlidesList($talks),
			'admin'	 		=>($this->user_model->isAdminEvent($id)) ? true : false,
			'claimed'		=>$claimed_talks,
			'user_id'		=>($is_auth) ? $this->session->userdata('ID') : '0',
			'attend' 		=>$chk_attend,
			'attend_ct'		=>count($attend),
			'reqkey' 		=>$reqkey,
			'seckey' 		=>buildSecFile($reqkey),
			'attending'		=>$attend,
			'latest_comment'=>$this->event_model->getLatestComment($id),
			'admins' 		=>$evt_admins,
			'tracks' 		=>$this->etm->getEventTracks($id),
			'times_claimed'	=>$claim_detail['claim_count'],
			'claimed_uids'	=>$claim_detail['uids']
			//'attend' =>$this->uam->getAttendCount($id)
			//'started'=>$this->tz->hasEvtStarted($id),
		);
		if($opt=='track'){ 
			$arr['track_filter']	= $opt_id;
			$arr['track_data']		= null;
			foreach($arr['tracks'] as $tr){
				if($tr->ID==$opt_id){ $arr['track_data']=$tr; }
			}
		}
		
		//our event comment form
		$rules=array(
			'event_comment'	=> 'required'
		);
		$fields=array(
			'event_comment'	=>'Event Comment'
		);
		$this->validation->set_fields($fields);
		$this->validation->set_rules($rules);
		
		if($this->validation->run()!=FALSE){	
			$ec=array(
				'event_id'		=> $id,
				'comment'		=> $this->input->post('event_comment'),
				'date_made'		=> time(),
				'active'		=> 1
			);
			if($is_auth){
				$ec['user_id']	= $this->session->userdata('ID');
				$ec['cname']	= $this->session->userdata('username');
			}else{
				$ec['user_id']	= 0;
			}
			// If they're logged in, dont bother with the spam check
			if(!$is_auth){
				$def_ret=$this->defensio->check('Anonymous',$ec['comment'],$is_auth,'/event/view/'.$id);
				$is_spam=(string)$def_ret->spam;
			}else{ $is_spam='false'; }
			
			//$this->spam->check('regex',$ec['comment']);
			
			if($is_spam=='false'){
				$this->db->insert('event_comments',$ec);
				$arr['msg']='Comment inserted successfully!';
		
			
				if(isset($def_ret)){
					$ec['def_resp_spamn']=(string)$def_ret->spaminess;
					$ec['def_resp_spamr']=(string)$def_ret->spam;
				}
				//print_r($ec);

				$to=array();
				$admin_emails=$this->user_model->getSiteAdminEmail();
				foreach($admin_emails as $user){ $to[]=$user->email; }
				
				// Get whatever email addresses there are for the event
				$admins=$this->event_model->getEventAdmins($id);
				foreach($admins as $ak=>$av){ $to[]=$av->email; }
				
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
		// Only show if they're an admin...
		$this->template->write_view('sidebar3','event/_event_blog',array(
			'entries'	=> $this->ebp->getPosts($id,true),
			'eid'		=> $id
		));
		
		if($arr['admin']){ $this->template->write_view('sidebar2','event/_sidebar-admin',
			array(
				'eid'			=> $id,
				'is_private'	=> $events[0]->private,
				'evt_admin'		=> $this->event_model->getEventAdmins($id)
			)); 
		}
		$this->template->write_view('content','event/detail',$arr,TRUE);
		if(!empty($t)){ 
			// If there's no twitter results, don't show this sidebar
			$this->template->write_view('sidebar2','event/_twitter-search',$other_data);
		}
		$this->template->write_view('sidebar2','event/_event_contact',array('eid'=>$id));
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
	/**
	* Handle the user submission of a new event
	*/
	function submit(){
		$arr=array();
		$this->load->library('validation');
		$this->load->plugin('captcha');
		$this->load->helper('custom_timezone');
		//$this->load->library('akismet');
		$this->load->library('defensio');
		$this->load->library('timezone');
		$this->load->model('user_admin_model');
		
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
			'event_tz_cont'			=> 'Event Timezone (Continent)',
			'event_tz_place'		=> 'Event Timezone (Place)',
			'event_stub'			=> 'Event Stub'
		//	'cinput'				=> 'Captcha'
		);
		$rules=array(
			'event_title'			=> 'required|callback_event_title_check',
			'event_loc'				=> 'required',
			'event_contact_name'	=> 'required',
			'event_contact_email'	=> 'required|valid_email',
			'event_tz_cont'		=> 'required',
			'event_tz_place'	=> 'required',
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
			$this->validation->is_private	= 'n';
		}else{
			$this->validation->cfp_checked	= $this->validation->is_cfp;
		}
		
		if($this->validation->run()!=FALSE){			
			//TODO: add it to our database, but mark it pending

			$tz = $this->input->post('event_tz_cont').'/'.$this->input->post('event_tz_place');

			// Get offset unix timestamp for start of event
			$startUnixTimestamp = $this->timezone->UnixtimeForTimeInTimezone(
									$tz,
									$this->input->post('start_yr'),
									$this->input->post('start_mo'),
									$this->input->post('start_day'),
									0,
									0,
									0
			);

			// Get offset unix timestamp for end of event
			$endUnixTimestamp = $this->timezone->UnixtimeForTimeInTimezone(
									$tz,
									$this->input->post('end_yr'),
									$this->input->post('end_mo'),
									$this->input->post('end_day'),
									23,
									59,
									59	
			);

			$sub_arr=array(
				'event_name'	=>$this->input->post('event_title'),
				'event_start'	=>$startUnixTimestamp,
				'event_end'		=>$endUnixTimestamp,
				'event_loc'		=>$this->input->post('event_loc'),
				'event_desc'	=>$this->input->post('event_desc'),
				'active'		=>0,
				'event_stub'	=>$this->input->post('event_stub'),
				'event_tz_cont'		=>$this->input->post('event_tz_cont'),
				'event_tz_place'	=>$this->input->post('event_tz_place'),
				'pending'		=>1,
				'private'		=>($this->input->post('is_private')=='n') ? null : $this->input->post('is_private')
			);
			
			// Check to see if our Call for Papers dates are set...
			$cfp_check=$this->input->post('cfp_start_mo');
			if(!empty($cfp_check)){
				// Get offset unix timestamp for start of CFP
				$sub_arr['event_cfp_start'] = $this->timezone->UnixtimeForTimeInTimezone(
										$tz,
										$this->input->post('cfp_start_yr'),
										$this->input->post('cfp_start_mo'),
										$this->input->post('cfp_start_day'),
										0,
										0,
										0
				);

				// Get offset unix timestamp for end of CFP
				$sub_arr['event_cfp_end'] = $this->timezone->UnixtimeForTimeInTimezone(
										$tz,
										$this->input->post('cfp_end_yr'),
										$this->input->post('cfp_end_mo'),
										$this->input->post('cfp_end_day'),
										23,
										59,
										59
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
				$subj	= 'Event submission from Joind.in';
				$msg= 'Event Title: '.$this->input->post('event_title')."\n\n";
				$msg.='Event Description: '.$this->input->post('event_desc')."\n\n";
				$msg.='Event Date: '.date('m.d.Y H:i:s',$sub_arr['event_start'])."\n\n";
				$msg.='Event Contact Name: '.$this->input->post('event_contact_name')."\n\n";
				$msg.='Event Contact Email: '.$this->input->post('event_contact_email')."\n\n";
				$msg.='Spam check: '.($is_spam=='false') ? 'not spam' : 'spam';
			
				//echo $msg.'<br/><br/>';
				
				$admin_emails=$this->user_model->getSiteAdminEmail();
				foreach($admin_emails as $user){
					mail($user->email,$subj,$msg,'From: submissions@joind.in');
				}
				$arr['msg']=sprintf('
					<style="font-size:16px;font-weight:bold">Event successfully submitted!</span><br/>
					<style="font-size:14px;">
						Once your event is approved, you (or the contact person for the event) will
						receive an email letting you know it\'s been accepted.
						<br/><br/>
						We\'ll get back with you soon!
					</span>
					</span>
				');
				
				//put it into the database
				$this->db->insert('events',$sub_arr);
				
				// Check to see if we need to make them an admin of this event
				if($this->input->post('is_admin') && $this->input->post('is_admin')==1){
					$uid	= $this->session->userdata('ID');
					$rid	= $this->db->insert_id();
					$type	= 'event';
					$this->user_admin_model->addPerm($uid,$rid,$type);
				}
			}else{ 
				$arr['msg']='There was an error submitting your event! Please <a href="submissions@joind.in">send us an email</a> with all the details!';
			}
		}else{ $this->validation->is_admin=0; }
		$arr['is_auth']=$this->user_model->isAuth();
		
		$this->template->write_view('content','event/submit',$arr);
		$this->template->write_view('sidebar2','event/_submit-sidebar',array());
		$this->template->render();
	}
	
	/**
	* Export the full event information as a CSV including:
	* - Speakers
	* - Sessions
	* - Session ratings/comments
	*/
	function export($eid){
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
	
	/**
	* Get the list of pending events
	*/
	function pending(){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->index(true);
	}
	
	/**
	* Approve a pending event and send emails to the admins (if there are any)
	*/
	function approve($eid){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		
		$this->load->model('event_model');
		$this->load->library('sendemail');
		$this->load->library('twitter');
		$this->event_model->approvePendingEvent($eid);
		
		//print_r($this->event_model->getEventDetail($eid));
		
		// If we have admins for the event, send them an email to let them know
		$admin_list	= $this->event_model->getEventAdmins($eid);
		if($admin_list && count($admin_list)>0){
			$evt_detail	= $this->event_model->getEventDetail($eid);
			$this->sendemail->sendEventApproved($eid,$evt_detail,$admin_list);
		}
		
		// @todo get this and twitter class working with short URL
		/*echo '<pre>';
		$link=$this->twitter->short_bitly('http://joind.in/event/view/'.$eid); 
		echo '</pre>';*/
		
		// Send the new approved event to Twitter
		//$this->twitter->sendMsg($msg);
		
		// Finally, redirect back to the event!
		redirect('event/view/'.$eid); 
	}
	
	/**
	* Allows a user to claim an event - adds a pending row to the admin table
	* for the site admins to go in and approve
	*/
	function claim($eid){
		if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($eid)){ 
			//they're okay
		}else{ redirect('event/view/'.$eid); }
		
		$this->load->model('user_admin_model','uam');
		$this->load->helper('events_helper');
		$this->load->library('sendemail');
		$ret 	= $this->uam->getPendingClaims('talk',$eid);
		
		$claim	= $this->input->post('claim');
		$sub	= $this->input->post('sub');
		
		// If we have claims to process...
		if($claim && count($claim)>0 && isset($sub)){
			foreach($claim as $k=>$v){
				// p[0] is record from $ret, p[1] is the user ID, p[2] is the session ID
				$p=explode('_',$k);
				if($v=='approve'){
					$t_id		= $p[2];
					$t_title	= $ret[$p[0]]->talk_title;
					$c_name		= $ret[$p[0]]->claiming_name;
					$code		= buildCode($t_id,$eid,$t_title,$c_name);
					
					// Put the code in the database to claim their talk!
					$this->db->where('ID',$ret[$p[0]]->ua_id);
					$this->db->update('user_admin',array('rcode'=>$code));
					
					$email		= $ret[$p[0]]->email;
					$evt_name	= $ret[$p[0]]->event_name;
					$this->sendemail->claimSuccess($email,$t_title,$evt_name);
					unset($ret[$p[0]]);
				}else{
					// Remove the claim...it's not valid
					$tdata=array(
						'rid'	=> $p[2],
						'rtype'	=> 'talk',
						'rcode'	=> 'pending'
					);
					$this->db->delete('user_admin',$tdata);
					unset($ret[$p[0]]);
				}
			}
		}
		
		// Data to pass out to the view
		$arr=array(
			'claims'	=> $ret,
			'eid'		=> $eid
		);

		$this->template->write_view('content','event/claim',$arr);
		$this->template->render();
	}
	
	/**
	 * Import an XML file and push the test information into the table
	 * XML is validated against a document structure in the /inc/xml directory
	 */
	function import($eid){
		// Be sure they're supposed to be here...
		if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($eid)){
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
				$msg='Import Successful! <a href="/event/view/'.$eid.'">View event</a>';
			}catch(Exception $e){
				$msg='Error: '.$e->getMessage();
			}
			unlink($p);
		}else{
			//print_r($this->upload->display_errors()); 
			$msg=$this->upload->display_errors();
		}

		$arr=array(
			'details'	=> $this->em->getEventDetail($eid),
			'msg'		=> $msg
		);
		$this->template->write_view('content','event/import',$arr);
		$this->template->render();
	}
	
	/**
	* Allows the event/site admins to send and manage invites to their invite-only
	* event. They can see the status of the invites (pending, accepted, requested).
	*/
	function invite($eid,$resp=null){
		
		$this->load->model('invite_list_model','ilm');
		$this->load->library('sendemail');
		$this->load->model('event_model');
		//$this->load->library('validation');
		$msg=null;
		$detail=$this->event_model->getEventDetail($eid);
		
		$is_auth= $this->user_model->isAuth();
		$user	= ($is_auth) ? $this->user_model->getUser($is_auth) : false;
		$admins	= $this->event_model->getEventAdmins($eid);
		if($resp && $user){
			switch(strtolower($resp)){
				case "respond":
					// Check their invite, be sure it's an empty status
					$inv=$this->ilm->getInvite($eid,$user[0]->ID);
					if(empty($inv[0]->accepted)){					
						// They're respondng to an invite - update the database
						$this->ilm->acceptInvite($eid,$user[0]->ID);
						redirect('event/view/'.$eid);
					}else{ redirect('event/view/'.$eid); }
					break;
				case "request":
					// They're requesting an invite, let the admin know!
					$evt_title	= $detail[0]->event_name;
					$evt_id		= $detail[0]->ID;
					$this->sendemail->sendInviteRequest($evt_id,$evt_title,$user,$admins);
					$this->ilm->addInvite($eid,$user[0]->ID,'A');
					
					$arr=array('detail'=>$detail);
					$this->template->write_view('content','event/request',$arr);
					$this->template->render();
					return;
					break;
			}
		}
					
		// Be sure they're supposed to be here...the rest of this is for admins
		if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($eid)){
			//they're okay
		}else{ redirect(); }
		
		$invites=$this->ilm->getEventInvites($eid);
		
		if($this->input->post('sub') && $this->input->post('sub')=='Send Invite'){
			// See if they're adding a username and check to see if it's valid
			$u=$this->input->post('user');
			if(!empty($u)){
				$ret=$this->user_model->getUser($u);
				if(empty($ret)){ 
					$msg='Invalid user <b>'.$u.'</b>!'; 
				}else{
					// Good user, lets add them to the list (if they're not there already)
					$is_invited=$this->ilm->isInvited($eid,$ret[0]->ID);
					if(!$is_invited){
						$this->ilm->addInvite($eid,$ret[0]->ID);
						$this->sendemail->sendInvite($ret[0]->email,$eid,$detail[0]->event_name);
						$msg='User <b>'.$u.'</b> has been sent an invite!';
					}else{
						$msg='User <b>'.$u.'</b> has already been invited to this event!';
					}
				}
			}
		}
		if($this->input->post('attend_list')){
			//Managing the list...
			foreach($invites as $k=>$v){
				//check for... *pending*
				
				//check to see if we have a delete action
				$del=$this->input->post('del_'.$v->uid);
				if($del && $del=='delete'){ $this->ilm->removeInvite($eid,$v->uid); }
				
				//check to see if there's an "approve" action
				$del=$this->input->post('approve_'.$v->uid);
				if($del && $del=='approve'){ $this->ilm->updateInviteStatus($eid,$v->uid,'Y'); }
				
				//check to see if there's a decline action
				$del=$this->input->post('decline_'.$v->uid);
				if($del && $del=='decline'){ $this->ilm->removeInvite($eid,$v->uid); }
				
			}
			
			// Refresh the invite list
			$invites=$this->ilm->getEventInvites($eid);
			$msg='Invite list changes saved!';
		}
		
		// Finally, we send it out to the view....
		$arr=array(
			'eid'		=> $eid,
			'invites'	=> $invites,
			'msg'		=> $msg,
			'evt_detail'=> $detail
		);
		
		$this->template->write_view('content','event/invite',$arr);
		$this->template->render();
	}
	
	/**
	* Allow logged in users to send a message to the event admins
	* if any are assigned. Will always send to site admins regardless
	*/
	function contact($eid){
		// They need to be logged in...
		$is_auth=$this->user_model->isAuth();
		if(!$is_auth){ redirect('event/view/'.$eid); }
		$this->load->model('event_model');
		$this->load->library('validation');
		$this->load->library('sendemail');
		
		$rules=array(
			'subject'	=> 'required',
			'comments'	=> 'required'
		);
		$this->validation->set_rules($rules);
		
		$fields=array(
			'subject'	=> 'Subject',
			'comments'	=> 'Comments'
		);
		$this->validation->set_fields($fields);
		
		
		$arr=array(
			'detail'=>$this->event_model->getEventDetail($eid)
		);
		
		if($this->validation->run()!=FALSE){
			$user	= $this->user_model->getUser($is_auth);
			// Grab the event admins
			$admins	= $this->event_model->getEventAdmins($eid);
			
			//If there's no event admins, we send it to the site admins
			if(empty($admins)){ $admins=$this->user_model->getSiteAdminEmail(); }
			
			// Push the emails over to the mailer class
			$evt_name	= $arr['detail'][0]->event_name;
			$msg		= 'Subject: '.$this->input->post('subject')."\n\n";
			$msg		.= $this->input->post('comments');
			$this->sendemail->sendEventContact($eid,$evt_name,$msg,$user,$admins);
			
			$arr['msg']='Your comments have been sent to the event administrators! They\'ll 
				get back in touch with you soon!';
		}else{
			$arr['msg']=$this->validation->error_string;
		}
	
		$this->template->write_view('content','event/contact',$arr);
		$this->template->render();
	}
	
	function blog($act='view',$eid,$pid=null){
		$this->load->model('event_model');
		$this->load->library('validation');
		$this->load->library('twitter');
		$this->load->model('event_blog_posts_model','ebp');
		
		$msg	= '';
		$rules	= array(
			'title'		=> 'required',
			'content'	=> 'required'
		);
		$fields	= array(
			'title'		=> 'Post Title',
			'content'	=> 'Post Content'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		$posts=$this->ebp->getPosts($eid);
		if($act=='add' || $act=='edit'){
			$this->template->write('feedurl','http://joind.in/event/blog/feed/'.$eid);
			
			// Be sure they're either a site admin or event admin
			if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($eid)){
				//they're okay
			}else{ redirect('event/blog/view/'.$eid); }
			
			if($act=='edit'){
				$detail=$this->ebp->getPostDetail($pid); //print_r($detail);
				$this->validation->title	= $detail[0]->title;
				$this->validation->content	= $detail[0]->content;
			}
			
			if($this->validation->run()!=FALSE){
				$data=array(
					'title'		=> $this->input->post('title'),
					'content'	=> $this->input->post('content')
				);
				if($pid){
					$this->ebp->updatePost($pid,$data);
					$msg='Post updated!';
				}else{ 
					$id=$this->ebp->addPost($eid,$data); 
					$msg='New post added!';
					
					//Sent it out to twitter
					$msg='Event Update: '.$data['title'].' http://joind.in/event/blog/view/'.$eid;
					$resp=$this->twitter->sendMsg($msg);
				}
			}else{
				$msg=$this->validation->error_string;
			}
		}elseif($act=='feed'){
			$items=array();
			foreach($posts as $k=>$v){
				$items[]=array(
					'title'			=> $v->title,
					'guid'			=> 'http://joind.in/event/blog/view/'.$eid.'#'.$v->ID,
					'link'			=> 'http://joind.in/event/blog/view/'.$eid.'#'.$v->ID,
					'description' 	=> $v->content,
					'pubDate'		=> date('t')
				);
			}
			$arr=array(
				'title'=>'Event Feed '.$eid,
				'items'=>$items
			);
			$this->load->view('feed/feed',$arr);
			return;
		}else{ 
			$this->template->write('feedurl','http://joind.in/event/blog/feed/'.$eid);
		}
		
		$arr=array(
			'evt_detail'=>$this->event_model->getEventDetail($eid),
			'action'	=>$act,
			'posts'		=>$posts,
			'pid'		=>$pid,
			'msg'		=>$msg
		);
		$this->template->write_view('content','event/blog',$arr);
		$this->template->render();
	}
	function tracks($eid){
		if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($eid)){ 
			//they're okay
		}else{ redirect(); }
		
		$this->load->model('event_track_model','etm');
		$this->load->model('event_model');
		$this->load->helper('reqkey');
		
		$reqkey=buildReqKey();
		$arr=array(
			'detail'	=> $this->event_model->getEventDetail($eid),
			'tracks'	=> $this->etm->getEventTracks($eid),
			'admin'	 	=> ($this->user_model->isAdminEvent($eid)) ? true : false,
			'reqkey' 	=> $reqkey,
			'seckey' 	=> buildSecFile($reqkey)
		);
		$this->template->write_view('content','event/tracks',$arr);
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
			$id=($this->uri->segment(3)===false) ? null : $this->uri->segment(3);
			
			$ret=$this->event_model->isUniqueStub($str,$id);
			if(!$ret){
				$this->validation->set_message('stub_check','Please choose another stub - this one\'s already in use!');
				return false;
			}else{ return true; }
		}else{ return true; }
	}
	//----------------------
}

?>
