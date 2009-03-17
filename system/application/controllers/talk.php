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
	function add($id=null,$opt=null){
		if(isset($id) && $id=='event'){
			$eid	= $opt; 
			$id		= null; 
			$type	= null;
		}elseif($id){ 
			$this->edit_id=$id;
			$eid	= null;
		}
		$pass=true;
		
		$this->load->model('talks_model');
		$this->load->model('event_model');
		$this->load->model('categories_model');	
		$this->load->model('lang_model');				
		$this->load->helper('form');
		$this->load->library('validation');

		$events	= $this->event_model->getEventDetail($eid);
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
			$det	= $this->talks_model->getTalks($id); //print_r($det);
			$events	= $this->event_model->getEventDetail($det[0]->event_id);
			foreach($det[0] as $k=>$v){
				$this->validation->$k=$v;
			}
			$this->validation->eid=$det[0]->eid;
			$this->validation->given_mo = date('m',$det[0]->date_given);
			$this->validation->given_day= date('d',$det[0]->date_given);
			$this->validation->given_yr = date('Y',$det[0]->date_given);
			
			$this->validation->session_lang=$det[0]->lang;
		}else{
			//set the date to the start date of the event
			$this->validation->given_mo = date('m',$events[0]->event_start);
			$this->validation->given_day= date('d',$events[0]->event_start);
			$this->validation->given_yr = date('Y',$events[0]->event_start);
		}
		if(isset($eid)){ $this->validation->event_id=$eid; }
		
		if($this->validation->run()!=FALSE){
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

			if($id){ //print_r($arr);
				$this->db->where('id',$id);
				$this->db->update('talks',$arr);
				//remove the current reference for the talk category and add a new one				
				$this->db->delete('talk_cat',array('talk_id'=>$id));
				
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
				
					$msg='Talk information successfully added!</br><a href="/talk/add/event/'.$events[0]->ID.'">Add another</a> ';
					$msg.='or <a href="/event/view/'.$events[0]->ID.'">View Event</a>';
					$pass=true;
				}else{
					$err='There was an error adding the talk information! (Duplicate talk)<br/>';
					$err.='<a href="/event/view/'.$events[0]->ID.'">View Event</a>';
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
		$out=array(
			'msg'	=>(isset($msg)) ? $msg : '',
			'err'	=>(isset($err)) ? $err : '',
			'events'=>$events,
			'cats'	=>$cats,
			'langs'	=>$langs
		);
		$this->template->write_view('content','talk/add',$out,TRUE);
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
	function view($id,$add_act=null,$code=null){
		$this->load->model('talks_model');
		$this->load->model('event_model');
		$this->load->helper('form');
		$this->load->helper('events');
		$this->load->helper('reqkey');
		$this->load->plugin('captcha');
		$this->load->library('akismet');
		$this->load->library('defensio');
		$this->load->library('spam');		
		$this->load->library('validation');
		
		$currentUserId = $this->session->userdata('ID');
		
		$talk_detail=$this->talks_model->getTalks($id);
		
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
				//echo $code.'<br/>'; print_r($codes);
				
				//$ret=$this->talks_model->getTalkByCode($code); print_r($ret);
				
				//if(isset($ret[0]) && $ret[0]->ID==$id && in_array($code,$codes)){
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
		
		$cl=($r=$this->talks_model->isTalkClaimed($id)) ? $r : false; //print_r($cl);

		$cap_arr=array(
			'img_path'		=>$_SERVER['DOCUMENT_ROOT'].'/inc/img/captcha/',
			'img_url'		=>'/inc/img/captcha/',
			'img_width'		=>'130',
			'img_height'	=>'30'
		);
		
		$rules	=array(
			'comment'	=> 'required',
			'rating'	=> $cl && $cl[0]->userid == $currentUserId ? null : 'required'
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

		if($this->validation->run()==FALSE){
			//echo 'error!';
		}else{ 
			$is_auth	= $this->user_model->isAuth();
			$arr=array(
				'comment_type'			=>'comment',
				'comment_content'		=>$this->input->post('your_com')
			);
			$ret=$this->akismet->send('/1.1/comment-check',$arr);
			
			$priv=$this->input->post('private');
			$priv=(empty($priv)) ? 0 : 1;
			
			$sp_ret=$this->spam->check('regex',$this->input->post('comment'));
			
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
			if($is_spam!='true' && $sp_ret==true){
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
				@mail('enygma@phpdeveloper.org','Comment on talk '.$id,$msg,'From: comments@joind.in');
			
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
			redirect('talk/view/'.$talk_detail[0]->tid . '#comments', 'location', 302);
		}
		//$cap = create_captcha($cap_arr);
		//$this->session->set_userdata(array('cinput'=>$cap['word']));
		
		$reqkey=buildReqKey();
		$this->load->model('talks_model');
		$arr=array(
			'detail'		=> $talk_detail,
			'comments'		=> $this->talks_model->getTalkComments($id),
			'admin'	 		=> ($this->user_model->isAdminTalk($id)) ? true : false,
			'site_admin'	=> ($this->user_model->isSiteAdmin()) ? true : false,
			'auth'			=> $this->auth,
		//	'captcha'		=> $cap,
			'claimed'		=> $this->talks_model->isTalkClaimed($id),
			//'claims'		=> $this->event_model->getClaimedTalks($talk_detail[0]->eid),
			'claim_status'	=> $claim_status,
			'claim_msg'		=> $claim_msg,
			'reqkey' 		=> $reqkey,
			'seckey' 		=> buildSecFile($reqkey),
		);
		if(empty($arr['detail'])){ redirect('talk'); }
		
		$this->template->write('feedurl','/feed/talk/'.$id);
		$this->template->write_view('content','talk/detail',$arr,TRUE);
		$this->template->render();
		//$this->load->view('talk/detail',$arr);
	}
	function claim(){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->load->model('user_admin_model','uam');
		$this->load->library('validation');
		$this->load->helper('events_helper');		
		
		$rules	= array();
		$fields	= array();
		//$this->validation->set_rules($rules);
		//$this->validation->set_fields($fields);
		
		$claims=$this->uam->getPendingClaims();
		
		$approved=0;
		$deleted=0;
		foreach($claims as $k=>$v){
			//first check to see if it was approved
			$chk=$this->input->post('claim_'.$v->ua_id);
			if(!empty($chk)){ echo $chk.'<br/>';
				$code=buildCode($v->talk_id,$v->eid,$v->talk_title,$v->speaker);
				$this->db->where('ID',$v->ua_id);
				$this->db->update('user_admin',array('rcode'=>$code));
				
				//send an email to the person claiming to let them know it was approved
				$to=$v->email;
				$subj='Joind.in: Claim on talk "'.$v->talk_title.'"';
				$msg=sprintf("
You recently laid claim to a talk at the \"%s\" event on Joind.in - \"%s\"
Your claim has been approved. This talk will now be listed under your account.

Thanks,
The Joind.in Crew
				",$v->event_name,$v->talk_title);
				mail($to,$subj,$msg,'From: feedback@joind.in');
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