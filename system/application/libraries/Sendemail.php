<?php

/* Just a few functions to send emails through out the site */
class SendEmail {
	
	private $_from	= 'feedback@joind.in';

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
		$this->_sendEmail($to,$subj,$msg);
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
		
		$this->_sendEmail($to,$subj,$msg);		
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
		
		$to=array();
		foreach($admins as $k=>$v){ $to[]=$v->email; }
		$this->_sendEmail($to,$subj,$msg);
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
}
?>