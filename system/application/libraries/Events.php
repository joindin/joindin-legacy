<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Events {
	
	function sendCodeEmail($email,$code){
		//echo $email.' '.$code;
		$msg=sprintf('
			You have been sent this code to claim your talk for <event>. Please log in to 
			http://www.foo.com and enter the code below to claim the talk.
			
			By claiming the talk you will be able to update its information and view any 
			private comments from visitors to the site.
			
			Code: %s
		',$code);
		$to		=$email;
		$subj	='Code from foo.com';
		mail($to,$subj,$msg,'From: webadmin@foo.com');
	}
	
}