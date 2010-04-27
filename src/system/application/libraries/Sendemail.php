<?php

/* Just a few functions to send emails through out the site */
class SendEmail {
	
	private $_from	= 'feedback@joind.in';
	private $CI		= null;
	
	public function __construct(){
		$this->CI=&get_instance();
	}

	/**
	* Generic function for sending emails
	*/
	private function _sendEmail($to,$msg,$subj,$from=null){
		if(!is_array($to)){ $to=array($to); }
		$from=($from) ? $from : $this->_from;
		foreach($to as $email){
			mail($email,$subj,$msg,'From: '.$from);
		}
	}
	//-----------------------
	
	/**
	* Send a message to user who claimed the talk when its accepted
	*/
	public function claimSuccess($to,$talk_title,$evt_name){
		$subj='Joind.in: Claim on talk "'.$talk_title.'"';
		$msg=sprintf("
You recently laid claim to a talk at the \"%s\" event on Joind.in - \"%s\"
Your claim has been approved. This talk will now be listed under your account.

Thanks,
The Joind.in Crew
		",$evt_name,$talk_title);
		$this->_sendEmail($to,$msg,$subj);
	}

	/**
	* Send a notice of invite to a user (to a private event)
	*/
	public function sendInvite($to,$evt_id,$evt_name){
		$subj="You've been invited to ".$evt_name;
		$msg=sprintf("
You have been invited to the event \"%s\" (a private event)

To reply to this invite and add yourself to the attending list, please 
visit http://joind.in/event/invite/%s/respond
		",$evt_name,$evt_id);
		
		$this->_sendEmail($to,$msg,$subj);
	}
	
	/**
	* Send a request to the event admin from a user wanting an invite
	* $user needs to be the result of a user_model->getUser()
	*/
	public function sendInviteRequest($eid,$evt_name,$user,$admins){
		$subj='User '.$user[0]->full_name.' ('.$user[0]->username.') is requesting an invite!';
		$msg=sprintf("
The user %s (%s) has requested an invite to the event \"%s\"

To invite this user, visit http://joind.in/event/invite/%s and click on the \"Invite list\" to 
approve or reject the invite.
		",$user[0]->full_name,$user[0]->username,$evt_name,$eid);
		
		//$to=array($user[0]->email);
		$to=array();
		foreach($admins as $k=>$v){ $to[]=$v->email; }
		$this->_sendEmail($to,$msg,$subj);
	}
	
	/**
	* Send en email back to the event admins from the user
	* $admins should be a result of a user_model->getEventAdmins
	* $user needs to be the result of a user_model->getUser()
	*/
	public function sendEventContact($eid,$evt_name,$msg,$user,$admins){
		$subj='Joind.in: A question from '.$user[0]->username;
		$msg=sprintf("
%s (%s) has asked a question about the \"%s\" event:

%s

You can reply directly to them by replying to this email.
		",$user[0]->full_name,$user[0]->username,$evt_name,$msg);
		
		$to=array();
		foreach($admins as $k=>$v){ $to[]=$v->email; }
		$this->_sendEmail($to,$msg,$subj,$user[0]->email);
	}
	
	/**
	* Send password reset email to the given user 
	* (user's email address is looked up by username)
	*/
	public function sendPassordReset($user,$pass){
		$to		= $user[0]->email;
		$subj	= 'Joind.in - Password Reset Request';
		$msg	= sprintf('
%s,

Someone has requested a password reset for your account on Joind.in. 
Your new password is below:

%s

Please log in in at http://joind.in/user/login and reset your password as soon as possible.
		',$user[0]->username,$pass);
		$this->_sendEmail($to,$msg,$subj,$user[0]->email);
	}
	
	/**
	* Send an email when a user is added to the admin list for an event
	*/
	public function sendAdminAdd($user,$evt,$added_by=null){
		$subj='You\'re now an admin on "'.$evt[0]->event_name.'"';
		$aby=($added_by) ? 'by '.$added_by : '';
		$msg=sprintf("
You have been added as an admin for the event \"%s\" %s

You can view the event here: http://joind.in/event/view/%s
		",$evt[0]->event_name,$aby,$evt[0]->ID);
		
		$to=array($user[0]->email);
		$this->_sendEmail($to,$msg,$subj);
	}
	
	/**
	* Send an email when a comment has been made on a session that's been claimed
	* Note: these emails are not sent to site admins
	* @param integer $tid Talk ID
	* @param string $to Email address
	* @param array $talk_detail Talk detail information
	* @param array $in_arr User data for byline
	*/
	public function sendTalkComment($tid,$to,$talk_detail,$in_arr){
		$CI =& get_instance();
		$byline='';
		if($in_arr['user_id']!=0){
			$CI->load->model('user_model');
			$udata	= $CI->user_model->getUser($in_arr['user_id']);
			$byline	= 'by '.$udata[0]->full_name.' ('.$udata[0]->username.')';
		}
		
		$subj	= 'A new comment has been posted on your talk!';
		$msg	= sprintf("
A comment has been posted to your talk on joind.in %s: \n%s\n
%s
\n
Click here to view it: http://joind.in/talk/view/%s
		",$byline,$talk_detail[0]->talk_title,trim($in_arr['comment']),$tid);
		
		$to=array($to);
 		$this->_sendEmail($to,$msg,$subj,'comments@joind.in');
	}
	
	/**
	* Sends an email when an event is approved
	* @param integer $eid Event ID
	* @param array $evt_detail Details for the event (to save another fetch)
	* @param array $admin_list Contains the list of admins and their emails
	*/
	public function sendEventApproved($eid,$evt_detail,$admin_list){
		$subj	= 'Submitted Event "'.$evt_detail[0]->event_name.'" Approved!';
		$from	= 'From:feedback@joind.in';
		
		foreach($admin_list as $k=>$user){
			$msg='The event you submitted "'.$evt_detail[0]->event_name.'" has been approved!'."\n";
			$msg.='You can now manage the event here: http://joind.in/event/view/'.$eid."\n\n";
			$msg.='If you need some help getting started with managing your event, try our '."\n";
			$msg.='helpful Event Admin Cheat Sheet! http://joind.in/about/evt_admin';
			
			$to=array($to);
			$this->_sendEmail($to,$msg,$subj);
		}
	}
	
	/**
	* Send an email when an event has successfully imported (from event/import)
	* @param $eid integer Event ID
	* @param $evt_detail array Event Detail information
	* @param $admins array Site admin information
	*/
	public function sendSuccessfulImport($eid,$evt_detail,$admins=null){
		$subj='Successful Import for event '.$evt_detail[0]->event_name;
		$from	= 'From:feedback@joind.in';
		
		if(!$admins){ $this->CI->event_model->getEventAdmins($eid); }
		
		$msg=sprintf("
An import for the event %s has been successful.\n\n
You can view the event here: http://joind.in/event/view/%s
		",$evt_detail[0]->event_name,$eid);
		
		$to=array();
		foreach($admins as $k=>$v){ $to[]=$v->email; }
 		$this->_sendEmail($to,$msg,$subj,'comments@joind.in');
	}
}
?>