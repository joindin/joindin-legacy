<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Events {
	
	function sendCodeEmail($email,$code,$details,$tid){
		$ret=$this->talks_model->getTalks($tid);
		$msg=sprintf('
You have been sent this code to claim your talk "%s" for %s. Please log in to 
http://joind.in and enter the code below to claim the talk.

By claiming the talk you will be able to update its information and view any 
private comments from visitors to the site.

Code: %s
		',$ret[0]->talk_title,$v->event_name,$code);
		$to		=$email;
		$subj	='Talk Code from join.in: '.$ret[0]->talk_title;
		mail($to,$subj,$msg,'From: eventmgr@joind.in');
	}
	
}