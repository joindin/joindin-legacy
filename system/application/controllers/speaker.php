<?php
/**
 * Class Speaker
 * @package Core
 * @subpackage Controllers
 */

/** 
 * Controls speaker information and actions.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Speaker extends Controller
{
    
    /**
     * Speaker profile for the logged in user.
     * @var SpeakerModel
     */
    protected $_speaker = null;

	/** **/

    function Speaker()
    {
        parent::Controller();
        
        if(!user_is_authenticated()) {
            redirect('/account/login');
        }
        
        // Try to find a speaker profile for the user
        $this->load->model('SpeakerModel');
        $speaker = $this->SpeakerModel->findByUserId(user_get_id(), true);
        if(null !== $speaker) {
            $this->_speaker = $speaker;
        } 
        else if($this->uri->uri_string() != '/speaker/profile' && $this->uri->uri_string() != '/speaker/edit') {
			redirect('/speaker/profile');
        }
        
    }
    
    /**
	 * Displays a speaker profile
	 */
	function profile()
	{
	    $this->load->helper('address');
	    
	    $profile = $this->SpeakerModel->findByUserId(user_get_id(), true);
	    
	    $this->template->write_view('content','speaker/profile', array('profile' => $profile));
	    $this->template->render();
	}
	
	/**
	 * Edit a speaker profile
	 */
	function edit()
	{
	    $this->load->model('CountryModel');
	    
	    $speaker = $this->_speaker;
	    if(null === $speaker) {
	    	$speaker = new SpeakerModel(array('user_id' => user_get_id()));
	    }
	    
	    $viewVars = array(
	        'speaker' => $speaker,
	        'countries' => $this->CountryModel->getList()
	    );

	    if($_SERVER['REQUEST_METHOD'] === 'POST') {
	    	
	    	if($speaker->save($_POST)) {
	    	    redirect('/speaker/profile');
	    	}
	    	else {
	    	    $viewVars['error'] = $speaker->getErrors();
	    	}
	    }
	    
        $this->template->write_view('content', 'speaker/form', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Deletes a speaker profile.
	 */
	function delete()
	{
    	$this->_speaker->delete();
	    redirect('speaker/profile');
	}
	
    /**
	 * Displays a list of talks for a speaker.
	 */
	function talks()
	{
	    $this->template->write_view('content', 'speaker/talks', array('speaker' => $this->_speaker));
        $this->template->render();
	}
	
    /**
     * Displays a list of sessions a speaker has given.
     */
    function sessions()
    {
        $this->template->write_view('content','speaker/sessions', array('speaker' => $this->_speaker));
	    $this->template->render();
    }
    
    /**
	 * Displays a list of access tokens for the speakers data.
	 */
	function access()
	{
	    $this->template->write_view('content', 'speaker/access', array('speaker' => $this->_speaker));
		$this->template->render();
	}
	
	/**
	 * Displays information for a token.
	 * @param string|int $token_id
	 */
	function token($token_id)
	{
		$this->load->model('SpeakerTokenModel');
		$token = $this->SpeakerTokenModel->find($token_id);
		if(null === $token || $token->getSpeakerProfileId() != $this->_speaker->getId()) {
			redirect('/speaker/access');
		}
		
		$this->template->write_view('content', 'speaker/token', array('token' => $token));
		$this->template->render();
	}
	
	/** 
	 * Adds or edits an access token from a speaker profile.
	 * @param int|string $token_id
	 */
	function edittoken($token_id = null)
	{
	    $this->load->model('SpeakerTokenModel');
	    $token = new SpeakerTokenModel();
		if(null !== $token_id) {
			$token = $token->find($token_id);
			if(null === $token || $token->getSpeakerProfileId() != $this->_speaker->getId()) {
				redirect('/speaker/access');
			}
		}
	    
	    $viewVars = array (
	        'token' => $token,
			'speaker' => $this->_speaker
	    );
	    
	    if('POST' === $_SERVER['REQUEST_METHOD']) {
			if($_POST['speaker_profile_id'] != $this->_speaker->getId()) {
				redirect('/speaker/access');
			}
			
			$token->setSpeakerProfileId($this->_speaker->getId());
            $token->setDescription($_POST['description']);
			if(isset($_POST['fields'])) {
				$token->setFields($_POST['fields']);
			}
			
			if($token->isNew()) {
				require_once BASEPATH . 'application/libraries/StringTokenGenerator.php';
				$generator = new StringTokenGenerator($token->getAllTokenStrings());
				$token->setAccessToken($generator->generate());
				$token->setCreated(mktime());
			}
			
			if($token->save()) {
				$this->session->set_flashdata('message', 'Token saved successfully.');
				redirect('/speaker/access');
			}
			else {
				$viewVars['error'] = $token->getErrors();
			}
	    }
	    
	    $this->template->write_view('content', 'speaker/form_token', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Deletes a token from the speaker profile.
	 * @param int|string $token_id
	 */
	function deltoken($token_id)
	{
		$this->load->model('SpeakerTokenModel');
		$token = $this->SpeakerTokenModel->find($token_id);
		if(null === $token || $token->getSpeakerProfileId() != $this->_speaker->getId()) {
			redirect('/speaker/access');
		}
		
		if($token->delete()) {
			$this->session->set_flashdata('message', 'Token deleted successfully.');
			redirect('/speaker/access');
		} else {
			$this->session->set_flashdata('error', 'Token deletion failed.');
			redirect('/speaker/access');
		}
	}
	
	/**
	 * Adds or edits an instant messaging account for a speakers profile.
	 * @param int $service_id
	 */
	function editim($service_id = null)
	{
		$this->load->model('MessagingServiceModel');
		$this->load->model('MessagingServiceProviderModel');
		
		if(null === $service_id) {
			$service = new MessagingServiceModel();
		} else {
			$service = $this->MessagingServiceModel->find($service_id);
			if(null === $service) {
				redirect('/speaker/profile#messaging');
			}
		}
		
		$viewVars = array(
			'speaker' => $this->_speaker,
			'service' => $service,
			'providers' => $this->MessagingServiceProviderModel->getList()
		);
		
		if('POST' === $_SERVER['REQUEST_METHOD']) {
			if($_POST['speaker_profile_id'] != $this->_speaker->getId()) {
				redirect('/speaker/profile#messaging');
			}
			else if($service->save($_POST)) {
				$this->session->set_flashdata('Instant Messaging Account added.');
				redirect('/speaker/profile#messaging');
			}
		}
		
		$this->template->write_view('content', 'speaker/form_im', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Removes an instant messaging account from the speakers profile.
	 * @param int $service_id
	 */
	function delim($service_id)
	{
		$this->load->model('MessagingServiceModel');
		
		$service = $this->MessagingServiceModel->find($service_id);
		if(null === $service) {
			redirect('/speaker/profile#messaging');
		}
		else if($service->getSpeakerProfileId() != $this->_speaker->getId()) {
			redirect('/speaker/profile#messaging');
		}
		
		$service->delete();
		$this->session->set_flashdata('message', 'Instant Messaging Account deleted.');
		redirect('/speaker/profile#messaging');
	}
	
	/**
	 * Adds or edits a web service account for a speakers profile.
	 * @param int $service_id
	 */
	function editweb($service_id = null)
	{
		$this->load->model('WebServiceModel');
		$this->load->model('WebServiceProviderModel');
		
		if(null === $service_id) {
			$service = new WebServiceModel();
		} else {
			$service = $this->WebServiceModel->find($service_id);
			if(null === $service) {
				redirect('/speaker/profile#messaging');
			}
		}
		
		$viewVars = array(
			'speaker' => $this->_speaker,
			'service' => $service,
			'providers' => $this->WebServiceProviderModel->getList()
		);
		
		if('POST' === $_SERVER['REQUEST_METHOD']) {
			if($_POST['speaker_profile_id'] != $this->_speaker->getId()) {
				redirect('/speaker/profile#web');
			}
			else if($service->save($_POST)) {
				$this->session->set_flashdata('Web Service Account added.');
				redirect('/speaker/profile#web');
			}
		}
		
		$this->template->write_view('content', 'speaker/form_web', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Removes a web service account from the speakers profile.
	 * @param int $service_id
	 */
	function delweb($service_id)
	{
		$this->load->model('WebServiceModel');
		
		$service = $this->WebServiceModel->find($service_id);
		if(null === $service) {
			redirect('/speaker/profile#messaging');
		}
		else if($service->getSpeakerProfileId() != $this->_speaker->getId()) {
			redirect('/speaker/profile#messaging');
		}
		
		$service->delete();
		$this->session->set_flashdata('message', 'Instant Messaging Account deleted.');
		redirect('/speaker/profile#messaging');
	}
}

