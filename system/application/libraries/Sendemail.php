<?php

/* Just a few functions to send emails through out the site */
class SendEmail {
	
	private $_from	= 'feedback@joind.in';

	private function _sendEmail($to,$msg,$subj){
		if(!is_array($to)){ $to=array($to); }
		foreach($to as $email){
			mail($email,$subj,$msg,'From: '.$this->_from);
		}
	}
	//-----------------------
	
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

	public function sendInvite($to,$evt_id,$evt_name){
		$subj="You've been invited to ".$evt_name;
		$msg=sprintf("
You have been invited to the event \"%s\" (a private event)

To reply to this invite and add yourself to the attending list, please 
visit http://joind.in/event/invite/%s/respond
		",$evt_name,$evt_id);
		
		$this->_sendEmail($to,$subj,$msg);		
	}
}
?>