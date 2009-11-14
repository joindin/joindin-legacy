<?php

/* Just a few functions to send emails through out the site */
class SendEmail {
	
	private $_from	= 'feedback@joind.in';

	private function _sendEmail($to,$msg,$subj){
		foreach($to as $email){
			mail($email,$subj,$msg,'From: '.$this->_from);
		}
	}
	//-----------------------
	
	public function claimSuccess($to,$talk_title,$evt_name){
		if(!is_array($to)){ $to=array($to); }
		$subj='Joind.in: Claim on talk "'.$talk_title.'"';
		$msg=sprintf("
You recently laid claim to a talk at the \"%s\" event on Joind.in - \"%s\"
Your claim has been approved. This talk will now be listed under your account.

Thanks,
The Joind.in Crew
		",$evt_name,$talk_title);
		$this->_sendEmail($to,$subj,$msg);
	}
	
}
?>