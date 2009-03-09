<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/** ServiceHandler */
require_once BASEPATH . 'application/libraries/service/ServiceHandler.php';
/** ServiceReponseXml */
require_once BASEPATH . 'application/libraries/service/ServiceResponseXml.php';
/** Talks_model */
require_once BASEPATH . 'application/models/talks_model.php';

class Claim extends ServiceHandler {
	
    public function isAuthorizedRequest()
    {
        return true;
    }
    
	public function handle(){
	    
	    $ci = CI_Base::get_instance();
	    
		$ci->load->model('user_admin_model');
		$ci->load->model('talks_model');
		
		$talkId = (string) $this->_xmlData->action->tid;
		
		// Check if the talk id exists
		if(empty($talkId)) {
		    $this->_sendError('Talk Id not found');
		}
		
		// Check if the user was logged in
		if(!$ci->user_model->isAuth()) {
		    $this->_sendRedirect('/user/login');
		}
		
		// Get the talk
		$talk = $this->talks_model->getTalks($talkId);
		
		// Insert the data
		$data = array(
		    'uid' => $this->_ci->session->userdata('ID'),
		    'rid' => $talkId,
		    'rtype' => 'talk',
		    'rcode' => 'pending'
		);
		$ci->db->insert('user_admin', $data);
		
		// Send a mail
		$to = 'enygma@phpdeveloper.org';
		$subject = 'Talk claim submitted! Go check!';
		$message = sprintf(
			"Talk claim has been submitted for talk \"%s\".\n\nhttp://joind.in/talk/claim", 
		    $talk->talk_title);
		mail(
			'enygma@phpdeveloper.org', 
			'Joind.in: Talk claim submitted! Go check!', 
		    $msg, 
	    	'From: feedback@joind.in'
		);

	    return array('success');
	}
	
	
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/service/ServiceHandler#getOutputType()
	 */
	public function getOutputType()
	{
	    return 'json';
	}
	
}