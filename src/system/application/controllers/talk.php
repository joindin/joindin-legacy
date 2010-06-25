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
		
		//$talks=$this->talks_model->getTalks(null,true);
		$talks=array();
		//$talks['popular']=$this->talks_model->getPopularUpcomingTalks();
		$talks['popular']	= $this->talks_model->getPopularTalks();
		$talks['recent']	= $this->talks_model->getRecentTalks();
		
		$this->template->write_view('content','talk/main',array('talks'=>$talks),TRUE);
		$this->template->render();
		//$this->load->view('talk/main',array('talks'=>$talks));
	}
	//-------------------
	function add($id=null,$opt=null){
		
		$pass=true;
		$tracks=array();
		
		$this->load->model('talks_model');
		$this->load->model('event_model');
		$this->load->model('categories_model');	
		$this->load->model('lang_model');				
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->library('timezone');
		$this->load->model('event_track_model','eventTracks');
		$this->load->model('talk_track_model','talkTracks');
		$this->load->model('talk_speaker_model','talkSpeakers');
		$this->load->model('user_admin_model','userAdmins');
		
		//Check to see if they're supposed to be here
		if(!$this->auth){ redirect(); }

		$currentUserId = $this->session->userdata('ID');
		if(isset($id) && $id=='event'){
			$eid	= $opt; 
			$id		= null; 
			$type	= null;
			
			if(!$this->user_model->isAdminEvent($eid)){ redirect(); }
		}elseif($id){ 
			$this->edit_id=$id;
			$eid	= null;
			
			// See if they have access to the talk (claimed user, site admin, event admin)
			if($this->user_model->isAdminEvent($eid) || $this->userAdmins->hasPerm($currentUserId,$id,'talk')){
				/* fine, let them through */
			}else{ redirect(); }
			
		}elseif(!$id && !$opt){
			//no options specified! redirect
			redirect();
		}

		$cats	= $this->categories_model->getCats();
		$langs	= $this->lang_model->getLangs();
		
		$rules=array(
			'event_id'		=>'required',
			'talk_title'	=>'required',
			'talk_desc'		=>'required',
			//'speaker'		=>'required',
			'session_type'	=>'required',
			'session_lang'	=>'required',
			'given_mo'		=>'callback_given_mo_check'
		);
		$fields=array(
			'event_id'		=>'Event Name',
			'talk_title'	=>'Talk Title',
			//'speaker'		=>'Speaker',
			'given_mo'		=>'Given Month',
			'given_day'		=>'Given Day',
			'given_yr'		=>'Given Year',
			'given_hour'	=>'Given Hour',
			'given_min'		=>'Given Minute',
			'slides_link'	=>'Slides Link',
			'talk_desc'		=>'Talk Description',
			'session_type'	=>'Session Type',
			'session_lang'	=>'Session Language'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		// If we have the event ID in our option...
		if($id==null && $opt!=null){
			$tracks	= $this->eventTracks->getEventTracks($opt);
		}
		
		if($id){
			$det	= $this->talks_model->getTalks($id);
			$thisTalk = $det[0];
			
			$events	= $this->event_model->getEventDetail($thisTalk->event_id);
			$tracks	= $this->eventTracks->getEventTracks($thisTalk->eid);
			

			$thisTalksEvent = $events[0];
			$thisTalksTrack = $tracks[0];
			
			
			$track_info=$this->talkTracks->getSessionTrackInfo($thisTalk->ID); //print_r($track_info);
			$this->validation->session_track=(isset($thisTalksTrack->ID)) ? $thisTalksTrack->ID : null;
			
			$is_private=($thisTalksEvent->private=='Y') ? true : false;
			
			foreach($thisTalk as $k=>$v){
				$this->validation->$k=$v;
			}
			
			// set our speaker information
			$this->validation->speaker=$this->talkSpeakers->getSpeakerByTalkId($id);
			
			$this->validation->eid=$thisTalk->eid;
			$this->validation->given_day = $this->timezone->formattedEventDatetimeFromUnixtime($thisTalk->date_given, $thisTalk->event_tz_cont.'/'.$thisTalk->event_tz_place, 'd');
			$this->validation->given_mo = $this->timezone->formattedEventDatetimeFromUnixtime($thisTalk->date_given, $thisTalk->event_tz_cont.'/'.$thisTalk->event_tz_place, 'm');
			$this->validation->given_yr = $this->timezone->formattedEventDatetimeFromUnixtime($thisTalk->date_given, $thisTalk->event_tz_cont.'/'.$thisTalk->event_tz_place, 'Y');
			$this->validation->given_hour = $this->timezone->formattedEventDatetimeFromUnixtime($thisTalk->date_given, $thisTalk->event_tz_cont.'/'.$thisTalk->event_tz_place, 'H');
			$this->validation->given_min = $this->timezone->formattedEventDatetimeFromUnixtime($thisTalk->date_given, $thisTalk->event_tz_cont.'/'.$thisTalk->event_tz_place, 'i');
			
			$this->validation->session_lang=$thisTalk->lang_name;
			$this->validation->session_type=$thisTalk->tcid;
		}else{
			$events	= $this->event_model->getEventDetail($eid);
			$thisTalksEvent = $events[0];
			$det=array();
			//set the date to the start date of the event
			$this->validation->given_mo = date('m',$thisTalksEvent->event_start);
			$this->validation->given_day= date('d',$thisTalksEvent->event_start);
			$this->validation->given_yr = date('Y',$thisTalksEvent->event_start);
			$this->validation->given_hour= date('H',$thisTalksEvent->event_start);
			$this->validation->given_min= date('i',$thisTalksEvent->event_start);
			
			$this->validation->session_track	= null;
			$this->validation->speaker			= array();
			
			$is_private=false;
		}
		if(isset($eid)){ $this->validation->event_id=$eid; }
		
		if($this->validation->run()!=FALSE){
			$talk_date = mktime(
				0,
				0,
				0,
				$this->input->post('given_mo'),
				$this->input->post('given_day'),
				$this->input->post('given_yr')
			);
			if(!empty($thisTalksEvent->event_tz_cont) && !empty($thisTalksEvent->event_tz_place)) {
				$talk_timezone = new DateTimeZone($thisTalksEvent->event_tz_cont . '/' . $thisTalksEvent->event_tz_place);
			} else {
				$talk_timezone = new DateTimeZone('UTC');
			}
			$talk_datetime = date_create(date('d-M-Y ',$talk_date) . $this->input->post('given_hour') . ':' . $this->input->post('given_min'), $talk_timezone);

			// How much wrong will ->format("U") be if I do it now, due to DST changes?
			// Only needed until PHP Bug #51051 delivers a better method
			$unix_offset1 = $talk_timezone->getOffset($talk_datetime);
			$unix_offset2 = $talk_timezone->getOffset(new DateTime());
			$unix_correction = $unix_offset1 - $unix_offset2;

			$unix_timestamp = $talk_datetime->format("U") - $unix_correction;

			$arr=array(
				'talk_title'	=> $this->input->post('talk_title'),
				//'speaker'		=> $this->input->post('speaker'),
				'slides_link'	=> $this->input->post('slides_link'),
				'date_given'	=> $unix_timestamp,   // Unix timestamp, therefore in UTC
				'event_id'		=> $this->input->post('event_id'),
				'talk_desc'		=> $this->input->post('talk_desc'),
				'active'		=> '1',
				'lang'			=> $this->input->post('session_lang')
			);

			if($id){ 
				//update the speaker information
				$this->talkSpeakers->handleSpeakerData($id,$this->input->post('speaker_row'));
					
				$this->db->where('id',$id);
				$this->db->update('talks',$arr);
				//remove the current reference for the talk category and add a new one				
				$this->db->delete('talk_cat',array('talk_id'=>$id));
				
				$this->validation->speaker=$this->talkSpeakers->getTalkSpeakers($id);
				
				//check to see if we have a track and it's not the "none"
				if($this->input->post('session_track')!='none'){
					$curr_track	= (isset($thisTalksTrack->ID)) ? $thisTalksTrack->ID : null;
					$new_track	= $this->input->post('session_track');
					$this->talkTracks->updateSessionTrack($id,$curr_track,$new_track);
					$this->validation->session_track=$new_track;
				}elseif($this->input->post('session_track')=='none'){
					//remove the track for the session
					if (is_array($track_info) && count($track_info) > 0 && is_object($thisTalksTrack)) {
						$curr_track	= $thisTalksTrack->ID;
						$this->talkTracks->deleteSessionTrack($id,$curr_track);
					}
				}
				
				$tc_id	= $id;
				$msg	= 'Talk information successfully updated! <a href="/talk/view/'.$id.'">Return to talk</a>';
				$pass	= true;
			}else{
				//check to be sure its unique
				$q=$this->db->get_where('talks',$arr);
				$ret=$q->result();
				if(count($ret)==0){
					$this->db->insert('talks',$arr);
					$tc_id=$this->db->insert_id();
					
					// Add the new speakers
					$this->talkSpeakers->handleSpeakerData($tc_id,$this->input->post('speaker_row'));
					
					//check to see if we have a track and it's not the "none"
					if($this->input->post('session_track')!='none'){
						$this->talkTracks->setSessionTrack($tc_id,$this->input->post('session_track'));
					}
				
					$msg='Talk information successfully added!</br><a href="/talk/add/event/'.$thisTalksEvent->ID.'">Add another</a> ';
					$msg.='or <a href="/event/view/'.$thisTalksEvent->ID.'">View Event</a>';
					$pass=true;
				}else{
					$err='There was an error adding the talk information! (Duplicate talk)<br/>';
					$err.='<a href="/event/view/'.$thisTalksEvent->ID.'">View Event</a>';
					$pass=false;
				}
			}
			if($pass){
				//now make the link between the talk and the category
				$tc_arr=array(
					'talk_id'	=> $tc_id,
					'cat_id'	=> $this->input->post('session_type')
					);
				$this->db->insert('talk_cat',$tc_arr);
			}
		}

		$det = $this->talks_model->setDisplayFields($det);
		$out=array(
			'msg'		=>(isset($msg)) ? $msg : '',
			'err'		=>(isset($err)) ? $err : '',
			'events'	=>$events,
			'cats'		=>$cats,
			'langs'		=>$langs,
			'detail'	=>$det,
			'evt_priv'	=>$is_private,
			'tracks'	=>$tracks
		);
		$this->template->write_view('content','talk/add',$out,TRUE);
		$this->template->render();
	}
	function edit($id){
		$this->add($id);
	}
	function delete($id){
		$this->load->model('talks_model');
		$this->load->model('user_model');
		$this->load->model('talk_track_model','talkTracks');
		
		//Check to see if they're supposed to be here
		if(!$this->auth){ redirect(); }
		
		$talk_detail=$this->talks_model->getTalks($id);
		if(empty($talk_detail)){ redirect('talk'); }

		$currentUserId 	= $this->session->userdata('ID');
		$arr			= array('error' => '');
		
		if($this->user_model->isAdminEvent($talk_detail[0]->eid)){
			$this->load->helper('form');
			$this->load->library('validation');
			$this->load->model('talks_model');

			$arr['tid'] = $id;
			if(isset($_POST['answer']) && $_POST['answer']=='yes'){
				$this->talks_model->deleteTalk($id);
				
				// Delete any records in the tracks table too
				$this->talkTracks->deleteSessionTrack($id);
				unset($arr['tid']);
			}
		} else {
		    $arr['error'] = 'No event administration rights';
		}
		
		$this->template->write_view('content','talk/delete',$arr,TRUE);
		$this->template->render();
	}
	function view($id,$add_act=null,$code=null){
		$this->load->model('talks_model');
		$this->load->model('event_model');
		$this->load->model('invite_list_model','ilm');
		$this->load->model('user_attend_model');
		$this->load->model('user_admin_model','userAdmins');
		$this->load->model('talk_track_model','talkTracks');
		$this->load->model('talk_comments_model','tcm');
		$this->load->model('talk_speaker_model','talkSpeakers');
		$this->load->helper('form');
		$this->load->helper('events');
		$this->load->helper('talk');
		$this->load->helper('reqkey');
		$this->load->plugin('captcha');
		$this->load->library('akismet');
		$this->load->library('defensio');
		$this->load->library('spam');		
		$this->load->library('validation');
		$this->load->library('timezone');
		$this->load->library('sendemail');		
		
		$msg			= '';
		$view_private	= false;
		
		// Filter it down to just the numeric characters
		if(preg_match('/[0-9]+/',$id,$m)){
			$id=$m[0];
		}else{ redirect('talk'); }
		
		$currentUserId = $this->session->userdata('ID');
		
		$talk_detail=$this->talks_model->getTalks($id);
		if(empty($talk_detail)){ redirect('talk'); }
		
		if($talk_detail[0]->private=='Y'){
			if(!$this->user_model->isAuth()){ /* denied! */ redirect('event/view/'.$talk_detail[0]->eid); }
			// If the event for this talk is private, be sure that the user is allowed
			if(!$this->ilm->isInvited($talk_detail[0]->eid,$currentUserId) && !$this->user_model->isAdminEvent($talk_detail[0]->eid)){
				redirect('event/view/'.$talk_detail[0]->eid);
			}
		}
		
		//$evt_started = $this->timezone->talkEvtStarted($id);
		
		$claim_status	= false;
		$claim_msg		= '';
		if(isset($add_act) && $add_act=='claim'){
			//be sure they're loged in first...
			if(!$this->user_model->isAuth()){
				//redirect to the login form
				$this->session->set_userdata('ref_url','/talk/view/'.$id.'/claim/'.$code);
				redirect('user/login');
			}else{
				$sp=explode(',',$talk_detail[0]->speaker);
				
				$codes=array();
				//loop through the speakers to make the codes
				foreach($sp as $k=>$v){
					//we should be logged in now...lets check and see if the code is right
					//$str='ec'.str_pad(substr($id,0,2),2,0,STR_PAD_LEFT).str_pad($talk_detail[0]->event_id,2,0,STR_PAD_LEFT);
					//$str.=substr(md5($talk_detail[0]->talk_title.$k),5,5);
					
					$str=buildCode($id,$talk_detail[0]->event_id,$talk_detail[0]->talk_title,trim($v));
					
					$codes[]=$str;
				}

				if(in_array($code,$codes)){
					//TODO: linking on the display side to the right user
					$uid=$this->session->userdata('ID');
					$ret=$this->talks_model->linkUserRes($uid,$id,'talk',$code);
					if(!$ret){
						$claim_status	= false;
						$claim_msg		= 'There was an error claiming your talk!';
					}else{
						$claim_status	= true;
						$claim_msg		= 'Talk claimed successfully!';
					}
				}else{
					$claim_status	= false;
					$claim_msg		= 'There was an error claiming your talk!';
				}
			}
		}
		
		$cl=($r=$this->talks_model->isTalkClaimed($id)) ? $r : false;

		$cap_arr=array(
			'img_path'		=>$_SERVER['DOCUMENT_ROOT'].'/inc/img/captcha/',
			'img_url'		=>'/inc/img/captcha/',
			'img_width'		=>'130',
			'img_height'	=>'30'
		);
		
		$rules	=array(
			'rating'	=> $cl && $cl[0]->userid == $currentUserId ? null : 'required'
		);
		$fields	=array(
			'comment'	=> 'Comment',
			'rating'	=> 'Rating'
		);
		
		// if it's past time for the talk, they're required
		// All other times they're not required...
		if(time()>=$talk_detail[0]->date_given){
			$rules['comment']='required';
		}
		
		// This is for the CAPTACHA - it was disabled for authenticatied users
		//if(!$this->user_model->isAuth()){
		//	$rules['cinput']	= 'required|callback_cinput_check';
		//	$fields['cinput']	= 'Captcha';
		//}
		
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);

		if($this->validation->run()==FALSE){
			
			// vote processing code removed
		}else{ 
			$is_auth	= $this->user_model->isAuth();
			$arr		= array(
				'comment_type'		=> 'comment',
				'comment_content'	=> $this->input->post('your_com')
			);
			
			$ret=$this->akismet->send('/1.1/comment-check',$arr);

			$priv=$this->input->post('private');
			$priv=(empty($priv)) ? 0 : 1;

			if(!$is_auth){
				$sp_ret=$this->spam->check('regex',$this->input->post('comment'));
				error_log('sp: '.$sp_ret);
			
				if($is_auth){
					$ec['user_id']	= $this->session->userdata('ID');
					$ec['cname']	= $this->session->userdata('username');
				}else{
					$ec['user_id']	= 0;
					$ec['cname']	= $this->input->post('cname');
				}
				$ec['comment']=$this->input->post('comment');
				$def_ret=$this->defensio->check($ec['cname'],$ec['comment'],$is_auth,'/talk/view/'.$id);
			
				$is_spam=(string)$def_ret->spam;
				if(strtolower($ec['cname'])=='dynom'){ $is_spam='false'; } //hack to allow comments for now
			}else{
				// They're logged in, let their comments through
				$is_spam	= false;
				$sp_ret 	= true;
			}
			
			if($is_spam!='true' && $sp_ret==true){
				
				$arr=array(
					'talk_id'		=> $id,
					'rating'		=> $this->input->post('rating'),
					'comment'		=> $this->input->post('comment'),
					'date_made'		=> time(),
					'private'		=> $priv,
					'active'		=> 1,
					'user_id'		=> ($this->user_model->isAuth()) ? $this->session->userdata('ID') : '0'
				);
				
				$out='';
				if($this->input->post('edit_comment')){
					$cid=$this->input->post('edit_comment');
					$uid=$this->session->userdata('ID');
					
					// Be sure they have the right to update the comment
					$com_detail=$this->tcm->getCommentDetail($cid);
					if(isset($com_detail[0]) && $com_detail[0]->user_id==$uid){
						$this->db->where('ID',$cid);
						$this->db->update('talk_comments',$arr);
						$out='Comment updated!';
					}else{ $out='Error on updating comment!'; }
				}else{
					$this->db->insert('talk_comments',$arr);
					$out='Comment added!';
				}
			
				//send an email when a comment's made
				$msg='';
				$arr['spam']=($ret=='false') ? 'spam' : 'not spam';
				foreach($arr as $ak=>$av){ $msg.='['.$ak.'] => '.$av."\n"; }
				@mail($this->config->item('email_admin'),'Comment on talk '.$id,$msg,'From: ' . $this->config->item('email_comments'));
			
				//if its claimed, be sure to send an email to the person to tell them
				if($cl){
					$this->sendemail->sendTalkComment($id,$cl[0]->email,$talk_detail,$arr);
				}
			
				$this->session->set_flashdata('msg', $out);
			}
			redirect('talk/view/'.$talk_detail[0]->tid . '#comments', 'location', 302);
		}
		//$cap = create_captcha($cap_arr);
		//$this->session->set_userdata(array('cinput'=>$cap['word']));
		
		$reqkey			= buildReqKey();
		$talk_detail 	= $this->talks_model->setDisplayFields($talk_detail);
		
		// catch this early...if it's not a valid session...
		if(empty($talk_detail)){ redirect('talk'); }
		
		$is_talk_admin	= $this->user_model->isAdminTalk($id);
		
		// Check to see if they can view private comments....
		$view_private 	= ($this->user_model->canViewPrivateComments($talk_detail[0]->eid,$id)) ? true : false;
		$event_claims	= $this->event_model->getClaimedTalks($talk_detail[0]->eid);
		$talk_comments	= splitCommentTypes($this->talks_model->getTalkComments($id,null,$view_private));
		
		$also_given=$this->talks_model->talkAlsoGiven($id,$talk_detail[0]->event_id);
		$also_given=array(
			'talks'	=> $also_given,
			'title'	=> 'Talk Also Given At...'
		);
		
		$arr=array(
			'detail'		=> $talk_detail[0],
			'comments'		=> (isset($talk_comments['comment'])) ? $talk_comments['comment'] : array(),
			'admin'	 		=> ($is_talk_admin) ? true : false,
			'site_admin'	=> ($this->user_model->isSiteAdmin()) ? true : false,
			'auth'			=> $this->auth,
			'claimed'		=> $this->talks_model->isTalkClaimed($id),
			'claims'		=> $event_claims,
			'claim_status'	=> $claim_status,
			'claim_msg'		=> $claim_msg,
			'claim_details'	=> $this->userAdmins->getTalkClaims($id),
			//'speaker_claims'=> buildClaimData($talk_detail[0],$event_claims,$ftalk),
			'speakers'		=> $this->talkSpeakers->getSpeakerByTalkId($id),
			//'ftalk'			=> $ftalk, // this one requires the previous call to buildClaimData (return by reference)
			'reqkey' 		=> $reqkey,
			'seckey' 		=> buildSecFile($reqkey),
			'user_attending'=>($this->user_attend_model->chkAttend($currentUserId,$talk_detail[0]->event_id)) ? true : false,
			'msg'			=> $msg,
			'track_info'	=> $this->talkTracks->getSessionTrackInfo($id),
			'user_id'		=> ($this->user_model->isAuth()) ? $this->session->userdata('ID') : null
		);
		
		$this->template->write('feedurl','/feed/talk/'.$id);
		if(!empty($also_given['talks'])){
			$this->template->write_view('sidebar2','talk/_also_given',$also_given,TRUE);
		}
		$this->template->write_view('content','talk/detail',$arr,TRUE);
		$this->template->render();
	}
	function claim(){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->load->model('user_admin_model','userAdmins');
		$this->load->library('validation');
		$this->load->library('sendemail');
		$this->load->helper('events_helper');		
		
		$rules	= array();
		$fields	= array();
		//$this->validation->set_rules($rules);
		//$this->validation->set_fields($fields);
		
		$claims=$this->userAdmins->getPendingClaims();
		
		$approved=0;
		$deleted=0;
		foreach($claims as $k=>$v){ //print_r($v);
			//first check to see if it was approved
			$chk=$this->input->post('claim_'.$v->ua_id);
			if(!empty($chk)){
				// Split the speakers on the commas and see if we have a match on the name
				$names=explode(',',$v->speaker);
				foreach($names as $nk=>$nv){
				    if(trim($nv)==$v->claiming_name){
					// match!
					$code=buildCode($v->talk_id,$v->eid,$v->talk_title,$v->claiming_name);
					$this->db->where('ID',$v->ua_id);
					$this->db->update('user_admin',array('rcode'=>$code));
				    }
				}

				
				//send an email to the person claiming to let them know it was approved
				$this->sendemail->claimSuccess($v->email,$v->talk_title,$v->event_name);
				$approved++;
				unset($claims[$k]);
			}
			$chk=$this->input->post('del_claim_'.$v->ua_id);
			if(!empty($chk)){
				$this->db->delete('user_admin',array('ID'=>$v->ua_id));
				$deleted++;
				unset($claims[$k]);
			}
		}
		
		$arr=array(
			'claims'	=> $claims,
			'approved'	=> $approved,
			'deleted'	=> $deleted
		);
		$this->template->write_view('content','talk/claim',$arr);
		$this->template->render();
	}
	//------------------------
	function given_mo_check($str){
		$t=mktime(
			$this->input->post('given_hour'),
			$this->input->post('given_min'),
			0,
			$this->input->post('given_mo'),
			$this->input->post('given_day'),
			$this->input->post('given_yr')
		); //echo $t.' '.date('m.d.Y H:i:s',$t);
		//get the duration of the selected event
		$det=$this->event_model->getEventDetail($this->validation->event_id);
		$thisTalk=$det[0];
		//echo '<pre>'; print_r($thisTalk); echo '</pre>';
		$day_start	= mktime(0,0,0,date('m',$thisTalk->event_start),date('d',$thisTalk->event_start),date('Y',$thisTalk->event_start));
		$day_end	= mktime(23,59,59,date('m',$thisTalk->event_end),date('d',$thisTalk->event_end),date('Y',$thisTalk->event_end));
		//if($t>=$thisTalk->event_start && $t<=$thisTalk->event_end){
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
