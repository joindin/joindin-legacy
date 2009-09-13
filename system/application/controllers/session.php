<?php
/**
 * Class Session
 * @package Core
 * @subpackage Controllers
 */
 
/**
 * Handles calls concerning event sessions.
 *
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Session extends Controller
{

    function Session()
    {
        parent::Controller();
    }
    
    /**
     * Shows a list of the ten latest sessions.
     */
    function index()
    {
        $this->load->model('SessionModel');
        
        // Get the ten latest sessions
        $sessions = $this->SessionModel->findAll(
            "`date` <= '" . time() . "'",
            '`date` DESC',
            10
        );
        
        $this->template->write_view('content', 'session/main', array('sessions' => $sessions), true);
		$this->template->render();
    }

    /**
     * Shows the details for a session
     * @param int $id
     */
    function view($id)
    {
        $this->load->helper('user');
        $this->load->model('SessionModel');
        $this->load->model('SessionCommentModel');
        
        $session = $this->SessionModel->find($id);
        $newComment = new SessionCommentModel();
        
        if(null === $session) {
            redirect('/session');
        }
        
        // Check for comment submission
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newComment->setData($_POST)
                       ->setAuthorName(user_get_displayname());

            if($newComment->validate()) {
                $newComment->setColumns(array(
                    'session_id' => $session->getId(),
                    'user_id' => user_get_id(),
                    'date' => mktime(),
                    'active' => 1
                ))->save();
                
                // clear the comment by creating a new instance
                $newComment = new SessionCommentModel();
            }
            else {
                $viewVars['error'] = $newComment->getErrors();
            }
        }
        
        $viewVars['session'] = $session;
        $viewVars['newComment'] = $newComment;
        
        $this->template->write_view('content', 'session/view', $viewVars, true);
		$this->template->render();
    }
    
    /**
     * Edit session details.
     * @param string|int $id
     */
    function edit($id) 
    {
        if(null === $id) {
            redirect('/session');
        }
        $this->load->model('SessionModel');
        
        $session = $this->SessionModel->find($id);
        if(null === $session) {
            redirect('/session');
        }
        
        // Check for submitted data
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if($session->validate($_POST)) {
                $session->setActive(1);
                $session->save();
                
                $this->session->set_flashdata('message', 'Session saved sucessfully!');
                redirect('/session/view/' . $session->getId());
            }
        }
        
        // Display the form
        $this->_displayForm($session);
    }
    
    /**
     * Add a session to an event.
     * @param string|int $eventId
     */
    function add($eventId)
    {
        if(null === $eventId) {
            redirect('/event');
        }
        
        $this->load->model('SessionModel');
        $session = new SessionModel(array(
            'event_id' => $eventId
        ));
		
        // Display the form
        $this->_displayForm($session);
    }
    
    /**
     * Displays the session add/edit form.
     * @param SessionModel $session
     */
    function _displayForm($session)
    {
        $this->load->model('SessionCategoryModel');
        $this->load->model('LanguageModel');
        
		$viewVars = array(
            'session' => $session,
            'categories' => $this->SessionCategoryModel->getList(),
            'languages' => $this->LanguageModel->getList()
        );
		
		// Check for submitted data
        if('POST' === $_SERVER['REQUEST_METHOD']) {
            if($session->validate($_POST)) {
                $session->setActive(1);
				
				if(isset($_POST['access_token']) && !empty($_POST['access_token'])) {
					$this->load->model('TalkTokenModel');
					$token = $this->TalkTokenModel->findByAccessToken($_POST['access_token'], true);
					if(null !== $token) {
						$session->setTalkId($token->getTalkId());
					}
				}
				
                $session->save();
                
                $this->session->set_flashdata('message', 'Session saved sucessfully!');
                redirect('/session/view/' . $session->getId());
            } else {
				$viewVars['error'] = $session->getErrors();
			}
        }
		
        if(!$session->isNew()) {
            $viewVars['title'] = 'Edit Session';
            $viewVars['action'] = '/session/edit/' . $session->getId();
        }
        else {
            $viewVars['title'] = 'Add Session';
            $viewVars['action'] = '/session/add/' . $session->getEventId();
        }
        
        // Check for validation errors
        if(count($session->getErrors()) > 0) {
            $viewVars['error'] = $session->getErrors();
        }
        
        $this->template->write_view('content', 'session/form', $viewVars, true);
		$this->template->render();
    }
    
    
    /**
     * Deletes a session from the system. This will also delete all comments.
     * @param int $id
     */
    function delete($id)
    {
        if(empty($id)) {
            redirect('');
        }
    
        $this->load->model('SessionModel');
        $session = $this->SessionModel->find($id);
        
        if(null === $session) {
            redirect('/session');
        }
        
        if(!user_is_administrator() || !$session->getEvent()->isEventManager(user_get_id())) {
            if(!user_is_authenticated()) {
                redirect('/user/login');
            } else {
                redirect('/event/view/' . $session->getEventId());
            }
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        
            if(isset($_POST['session_id']) && $_POST['session_id'] == $id) {
                $session->delete();
                $this->session->set_flashdata('message', 'Session delete successfully.');
            }
            else {
                $this->session->set_flashdata('error', 'Delete failed, please try again.');
            }
            
            redirect('/event/view/' . $session->getEventId());
        }
        
        $this->template->write_view('content', 'session/delete', array('session' => $session), true);
		$this->template->render();
    }
    
    /**
     * Deletes a comment from the application
     * @param sting|int $id
     */
    function deletecomment($id) 
    {
        if(!user_is_administrator()) {
            redirect('');
        }
        $this->load->model('SessionCommentModel');
        
        $comment = $this->SessionCommentModel->find($id);
        if(null === $comment) {
            redirect('/session');
        }
        
        // Delete the comment
        $comment->delete();
        
        // Redirect back to the session view page
        redirect("/session/view/{$comment->getSessionId()}#comments");
    }
    
}
