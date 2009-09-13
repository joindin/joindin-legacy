<?php
/**
 * Class Claim
 * @package Core
 * @subpackage Controllers
 */
 
/**
 * Handles the claims for sessions and approving or rejecting them.
 * 
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Claim extends Controller
{
    
    function Claim()
    {
        parent::Controller();
    }
    
    /**
     * Displays a list of pending claims
     */
    function index()
    {
        if(!user_is_administrator()) {
            redirect('/session');
        }
        
        $this->load->model('ClaimModel');
        
        $claims = $this->ClaimModel->findAll();
        
        $this->template->write_view('content', 'claim/list', array('claims' => $claims));
		$this->template->render();
    }
    
	/**
	 * Claims a session with a token.
	 */
	function token()
	{
		$this->load->model('SessionModel');
		
		if('POST' === $_SERVER['REQUEST_METHOD'] && !empty($_POST['claim_token'])) {
			$session = $this->SessionModel->findByClaimToken($_POST['claim_token'], true);
			$this->session($session);
		}
		else {
			redirect('');
		}
	}
	
    /** 
     * Claims a session
     * @param int|SessionModel $session_id
     */
    function session($session)
    {
        if(!user_is_authenticated()) {
            redirect('/user/login');
        }
		
        $this->load->model('SessionModel');
        $this->load->model('SpeakerProfileModel');
		
		if(!($session instanceof SessionModel)) {
			$session = $this->SessionModel->find($session);
		}
        if(null === $session) {
            redirect('/session');
        } else if($session->isClaimed()) {
            $this->session->set_flashdata('This session is already claimed!');
            redirect('/session/view/' . $session->getId());
        }
        
        $speaker = $this->SpeakerProfileModel->findByUserId(user_get_id(), true);
        if(null === $speaker) {
            redirect('/speaker/profile');
        }
        
        $viewVars = array(
            'session' => $session,
            'speaker' => $speaker
        );
        
        if('POST' === $_SERVER['REQUEST_METHOD'] && !isset($_POST['claim_token'])) {
            $this->load->model('ClaimModel');
            $claim = new ClaimModel($_POST);
            $claim->setSpeakerProfileId($speaker->getId());
            $claim->setSessionId($session->getId());
            $claim->setDate(mktime());
            if($claim->save()) {
                $this->session->set_flashdata('message', 'Claim made sucessfully.');
                redirect('/session/view/' . $session->getId());
            }
            else {
                $viewVars['error'] = 'Claim failed, please try again.';
            }
        }
        
        $this->template->write_view('content', 'claim/session', $viewVars);
		$this->template->render();
    }
    
    /** 
     * Approve a claim. This will connect the session to a talk in the speakers 
     * profile. If the session is connected to a new talk one is created for the 
     * speaker with the data of the session.
     * @param int|string $claim_id
     */
    function approve($claim_id)
    {
        if(!user_is_administrator()) {
            redirect('/');
        }
        
        $this->load->model('ClaimModel');
        $claim = $this->ClaimModel->find($claim_id);
        if(null == $claim) {
            $this->session->set_flashdata('error', 'Claim not found.');
            redirect('/');
        }
        
        $claim->approve();
        
        /** @todo send mail to the talk owner */
        
        $this->session->set_flashdata('message', 'Claim approved');
        redirect('/claim');
    }
    
    /**
     * Reject a claim. This will delete the claim from the table and an email 
     * will be send to the claim owner.
     * @param int|string $claim
     */
    function reject($claim_id)
    {
        if(!user_is_administrator()) {
            redirect('/');
        }
        
        $this->load->model('ClaimModel');
        $claim = $this->ClaimModel->find($claim_id);
        if(null == $claim) {
            $this->session->set_flashdata('error', 'Claim not found.');
            redirect('/');
        }
        
        $claim->delete();
        
        /** @todo send email to claim owner */
        
        redirect('/claim');
    }
    
}
