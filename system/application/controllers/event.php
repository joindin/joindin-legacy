<?php
/**
 * Class Event
 * @package Core
 * @subpackage Controllers
 */

/**
 * Handles display of information regarding Events.
 *
 * @author Chirs Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Event extends Controller {
	
	function Event(){
		parent::Controller();
		
		$this->load->model('EventModel');
	}
	
	/**
     * Displays a list all events.
     */
	function index(){
		$this->_displayList();
	}
	
	/**
	 * Displays a of "hot" events
	 */
    function hot(){
		$this->_displayList('hot');
	}
	
	/**
	 * Displays a list of "upcoming" events.
	 */
    function upcoming(){
		$this->_displayList('upcoming');
	}
	
	/**
	 * Displays a list of past events.
	 */
    function past(){
		$this->_displayList('past');
	}
	
	/**
	 * Displays a list of pending events
	 */
	function pending(){
	    $this->load->helper('user');
	    if(!user_is_administrator()) {
	        redirect();
	    }
	    
	    $this->load->model('EventModel');
	    
	    $events = $this->EventModel->getPendingEvents();
	    
		$this->template->write_view('content', 'event/pending', array('events' => $events), true);
		$this->template->render();
	}
	
	/**
	 * Displays a list of events. The type parameter is used to specify what events 
	 * will end up on the list. Three types can be provided:
	 * <ul>
	 *  <li>hot: Only events with comments are shown</li>
	 *  <li>upcomming: Only events that are in the future are shown</li>
	 *  <li>past: Only events that are in the past are shown</li>
	 * </ul>
	 * If no type is specified all events will be added to the list.
     * @param string $type
	 */
	function _displayList($type = '')
	{
		$this->load->helper('form');
		$this->load->helper('reqkey');
		$this->load->helper('mycal');
		$this->load->model('EventModel');
		
		switch ($type) {
		    case 'hot':
		        $events = $this->EventModel->getHotEvents();
		        break;
		    case 'upcoming':
		        $events = $this->EventModel->getUpcomingEvents();
		        break;
		    case 'past':
		        $events = $this->EventModel->getPastEvents();
		        break;
		    default:
		        $events = $this->EventModel->findAll(array('active' => 1), '`start` DESC');
		        break;
		}

		$requestKey = buildReqKey();
		
		$viewVars = array(
			'type' => $type,
			'events' => $events,
			'month'	=> null,
			'day'	=> null,
			'year'	=> null,
			'all'	=> true,
			'requestKey' => $requestKey,
			'secretKey' => buildSecFile($requestKey)
		);	
		
		$this->template->write_view('content', 'event/main', $viewVars, true);
		$this->template->render();
	}
	
	/**
	 * Displays a list of events for a given year, month and day.
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 */
	function calendar($year = null, $month = null, $day = null){
		$this->load->model('EventModel');
		$this->load->helper('reqkey');
		$this->load->helper('mycal');
        
		if (null === $year) {
		    $year = date('Y');
		}
		
	    if (null === $month) {
		    $month = date('m');
		}
        
        /* 
         * Check the date values.
         * If day was null substitute it with 1 for the check
         */
		if (!checkdate((int)$month, (int)((null === $day) ? 1 : $day), (int)$year)) {
		    $day = null;
		    $month = date('m');
		    $year = date('Y');
		}
        
		if(null === $day) {
	        // The range is a complete month
	        $rangeStart	= mktime(0, 0, 0, $month, 1, $year);
    		$rangeEnd = mktime(0, 0, 0, $month + 1, 0, $year);
        } else {
            // The range is only 1 day
            $rangeStart	= mktime(0, 0, 0, $month, $day, $year);
            $rangeEnd = mktime(23, 59, 59, $month, $day, $year);
        }
        
        // Get the events
		$events	= $this->EventModel->getEventsForDateRange($rangeStart, $rangeEnd);

        // Collect the view variables		
		$requestKey = buildReqKey();
		$viewVars = array(
			'events' => $events,
			'month'	 => $month,
			'day'	 => $day,
			'year'	 => $year,
			'requestKey' => $requestKey,
			'secretKey' => buildSecFile($requestKey)
		);

		$this->template->write_view('content', 'event/main', $viewVars, TRUE);
		$this->template->render();
	}
	
	/**
	 * Displays the details of an Event
	 * @param int $id
	 */
	function view($id){
		$this->load->helper('form');
		$this->load->helper('reqkey');
		$this->load->helper('events');
		$this->load->model('UserModel');
		$this->load->model('EventModel');
		$this->load->model('EventCommentModel');
		
		$viewVars = array();
		
		$event = $this->EventModel->find($id);
        if(null === $event) {
            redirect('/event');
        }
		
        $viewVars['event'] = $event;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $comment = new EventCommentModel($_POST);
            $comment->setEventId($event->getId());
            $comment->setDate(time());
            $comment->setActive(1);
            // Check for user_id
            if($this->session->userdata('id')!= null) {
                $user = $this->UserModel->find($this->session->userdata('id'));
                $comment->setUserId($user->getId());
                $comment->setAuthorName($user->getUsername());
            }
            
            // Validate the comment
            if($comment->validate()) {
                // Save the comment
                $comment->save();
                
                // Send a mail to the administrators
                /** Mailer */
                require_once BASEPATH . 'application/libraries/Mailer.php';
                
                $mail = new Mailer(array(
                    'to' => $this->config->item('mail_admin'),
                    'from' => $this->config->item('mail_feedback'),
                    'subject' => 'Joind.in: Event feedback - ' . $event->getId()
                ));
                $mail->setBodyFromFile(
                    'mail/event_comment',
                    date('', $comment->getDate()),
                    $event->getTitle() . "({$event->getId()})", 
                    $comment->getAuthorName(),
                    $comment->getComment()
                );
                $mail->send();

                $this->session->set_flashdata('msg', 'Comment inserted successfully!');
                redirect("event/view/{$event->getId()}#comments", 'location', 302);
            } else {
                // Display error
                $viewVars['comment'] = $comment;
                $viewVars['commentErrors'] = $comment->getErrors();
            }
        }
		
		$requestKey = buildReqKey();
		$viewVars['requestKey'] = $requestKey;
		$viewVars['secretKey'] = buildSecFile($requestKey);
		
		$this->template->write('feedurl', "/feed/event/{$event->getId()}");
		$this->template->write_view('content', 'event/view', $viewVars, true);
		$this->template->render();
	}
	
	/**
	 * Handles events either by `id` or by `stub` and redirects to the proper 
	 * view page for the event details.
	 * @param int|string $data
	 */
	function cust($data){
	    $this->load->helper('url');
		
		$id = null;
		if(is_numeric($data)) {
		    // The data is already an event id, no need to search for it in the database
		    $id = $data;
		}
		else {
		    // Try to find the event by it's stub
		    $event = $this->EventModel->findByStub(trim(strip_tags($data)));
		    if((null !== $event) && (count($event) === 1)) {
		        $event = array_shift($event);
		        $id = $event->getId();
		    }
		}
		
		if(null === $id) {
		    // Event was not found, show an error page
		    show_404('event/' . $id);
		}
		
		redirect('event/view/' . $id);
	}
	
	/**
	 * Sends an ical file as response to the client containing the details for 
	 * the event.
	 * @param int $id
	 */
	function ical($id){
		$this->load->model('EventModel');
		
		$event = $this->EventModel->find($id);
		
		if(null === $event) {
		    show_404('event/ical/' . $id);
		}
		
	    header('Content-type: text/calendar');
		header('Content-disposition: filename="ical.ics"');
		$this->load->view('event/ical', array('event' => $event));
	}
	
	/**
	 * Displays the form to add or edit Event details
	 */
	function _showForm($id = null)
	{
	    if(!user_is_authenticated()) {
	        redirect('/user/login');
	    }
	    
	    $this->load->model('EventModel');
	    $event = new EventModel();
	    
	    // check for add or edit action
	    if(null === $id) {
	        $viewVars = array(
	            'title' => 'Add Event',
	            'action' => 'event/add',
	        );
	        $viewVars['event'] = $event;
	    } else {
	        $viewVars = array(
	            'title' => 'Edit Event',
	            'action' => "event/edit/{$id}",
	        );
	        
	        $event = $event->find($id);
	        if(null === $event) {
	            // The event doesn't exist
	            redirect('/event');
	        }
	        if(!user_is_administrator() && !$event->isEventManager(user_get_id())) {
	            // user does not have the privileges to edit the event
	            redirect('/event');
	        }
	        
	        $viewVars['event'] = $event;
	    }
	    
	    // check for post values
	    if('POST' === $_SERVER['REQUEST_METHOD']) {

	        $event->setData($_POST);

	        // Set up the upload config
	        $uploadConfig = array (
	            'upload_path' => $_SERVER['DOCUMENT_ROOT'] . '/inc/img/event_icons',
	            'allowed_types' => 'gif|jpg|png',
	            'max_size' => '100',
	            'max_width' => '90',
	            'max_height' => '90'
	        );
	        // Do the uploading
    		$this->load->library('upload', $uploadConfig);
    		if($this->upload->do_upload('icon_file')){
                $uploadData = $this->upload->data();
				$event->setIcon($uploadData['file_name']);
			} else {
			    $viewVars['error'] = $this->upload->display_errors('', '');
			}
	            
            // Validate the model
            if($event->validate()) {
                $event->setActive(1);
	            $event->save();
	            
	            // redirect
	            redirect('/event/view/' . $event->getId());
	        }
	        else {
	            if(isset($viewVars['error']) && is_array($viewVars['error'])) {
    	            $viewVars['error'] = array_merge($event->getErrors(), $viewVars['error']);
                } else {
                    $viewVars['error'] = $event->getErrors();
                }
	        }
	    }
	    
	    $this->template->write_view('content', 'event/form', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Add an event. Will redirect to _showForm.
	 */
	function add()
	{
	    $this->_showForm();
	}

    /**
     * Edit Event details. Will redirect to _showFrom with the id of the Event 
     * to edit.
     * @param int $id
     */
	function edit($id){
		$this->_showForm($id);
	}
	
	/**
	 * Displays a list of attendees for an event
	 * @param int $id
	 */
    function attendees($id){
        $this->load->model('EventModel');
		
        $event = $this->EventModel->find($id);

		$this->template->write_view('content', 'event/attendees', array('event' => $event));
		// Explicitely echo the rendered region as it is always returned as a
		// string by the template class when rendering a specific region.
		echo $this->template->render('content');
	}
	
	/**
	 * This will deactivate an event. This will also deactivate all event comments.
	 * @param int $id
	 */
	function delete($id){
	    if(!user_is_administrator()) {
	        redirect('/event');
	    }
		$this->load->model('EventModel');
		
		$event = $this->EventModel->find($id);
		if(null === $event) {
		    redirect('/event');
		}

		if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['event_id'] == $event->getId()) {
		    if($event->isPending()) {
		        $event->delete();
		        $this->session->set_flashdata('message', 'Event deleted successfully.');
				redirect('/event/pending');
		    }
		    else {
    		    $event->deactivate();
    		    $this->session->set_flashdata('message', 'Event deactivated successfully.');
				redirect('/event');
		    }
		}
		
		$this->template->write_view('content', 'event/delete', array('event' => $event), true);
		$this->template->render();
	}

	/** 
	 * Allows a user to submit an event.
	 */
	function submit()
	{
	    $this->load->library('defensio');
	    $this->load->library('validation');
	
	    $viewVars = array(
			'error' => array()
		);
	    $event = $viewVars['event'] = new EventModel();
	    
	    if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$event->setData($_POST);
			
			// Check if the dates are valid
			if(!$this->_checkDates($event->getStart(), $event->getEnd())) {
				$viewVars['error'][] = 'One of the date values is incorrect.';
			}
			// Check if start date is before the end date
			else if($event->getStart() > $event->getEnd()) {
				$viewVars['error'][] = 'Start date needs to be before end date.';
			}
			
			if($event->validate() && count($viewVars['error']) === 0) {
				$event->setActive(1);
				$event->setPending(1);
				$event->save();
				
				if(user_is_authenticated()) {
					$event->addEventManager(user_get_model());
				}
				
				// Check for spam
				$isSpam = $this->defensio->check($event->getContactName(), $event->getDescription());
				
				// Send an email to the site administrators
				require_once BASEPATH . 'application/libraries/Mailer.php';
				
				$mail = new Mailer(array(
					'to' => $this->config->item('mail_administrator'),
					'from' => $this->config->item('mail_submissions'),
					'subject' => 'Event submission from Joind.in'
				));
				$mail->setBodyFromFile(
					'mail/event_submission', 
					$event->getTitle(),
					$event->getDescription(),
					date('m/d/Y', $event->getStart()),
					date('m/d/Y', $event->getEnd()),
					$event->getContactName(),
					$event->getContactEmail(),
					($isSpam) ? 'Possibly spam!' : ''
				);
				/** @todo enable this mail */
				//$mail->send();
				
				$this->session->set_flashdata('message', 'Event submitted, thanks! We\'ll get back to you as soon as possible.');
				redirect('/');
			}
			else {
				$viewVars['error'] = array_merge($event->getErrors(), $viewVars['error']);
			}
	    }
	    
	    $this->template->write_view('content', 'event/submit', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Checks a number of strptime generated date arrays and returns if they are 
	 * valid. Dates can be parsed as arguments and will be collected using 
	 * func_get_args().
	 * @return boolean
	 */
	protected function _checkDates()
	{
	    $dates = func_get_args();
	    if($dates <= 0) {
	        return true;
	    }
	    
	    $endResult = true;
	    foreach($dates as $timestamp) {
	        if(!(date('m/d/Y', $timestamp))) {
	            $endResult = false;
				continue;
	        }
	        if(!checkdate((date('m', $timestamp)), date('d', $timestamp), date('Y', $timestamp))) {
	            $endResult = false;
	        }
	    }
	    
	    return $endResult;
	}
	
	/**
	 * Converts a strptime generated date array to a timestamp.
	 * @param array $dateArray
	 * @param boolean $includeTime
	 */
	protected function _convertToTimestamp($dateArray, $includeTime = false)
	{
	    if($includeTime) {
	        return mktime(
	            $dateArray['tm_hour'], 
	            $dateArray['tm_min'], 
	            $dateArray['tm_sec'  ], 
	            ($dateArray['tm_mon'] + 1),
	            $dateArray['tm_mday'], 
	            ($dateArray['tm_year'] + 1900)
	        );
	    }
	    else {
    	    return mktime(
    	        0, 0, 0, 
    	        ($dateArray['tm_mon'] + 1), 
    	        $dateArray['tm_mday'], 
    	        ($dateArray['tm_year'] + 1900)
    	    );
	    }
	}
	
	
	/**
	 * Exports the event sessions with relevant data.
	 * @param int $id
	 */
	function export($id = null){
		if(null === $id) {
		    show_404('error/404');
		}
		$this->load->model('EventModel');
		$event = $this->EventModel->find($id);
		
		if(null === $event) {
		    show_404('error/404');
		}
		
		$sessions = $event->getSessions();
		
		$fp = fopen('php://memory','w+');
		foreach($sessions as $session) {
		    $comments = $session->getComments();
		    foreach($comments as $comment) {
		        $data = array(
		            $session->getTitle(), 
		            $session->getSpeakerName(),
		            $session->getDate(),
		            $session->getRating(),
		            $comment->getComment()
		        );
		        fwrite($fp, implode(', ', $data));
		    }
		}
		rewind($fp);
		$output = stream_get_contents($fp);
		fclose($fp);
		
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="Event_Comments_'.$id.'.csv"');
		echo $output;
	}
	
	
	/**
	 * Approves an event. This will remove the pending status from an event 
	 * and puts its status to active.
	 * @param int $id
	 */
	function approve($id){
		if(!user_is_administrator()) { 
		    redirect();
		}
		
		$this->load->model('EventModel');
		$event = $this->EventModel->find($id);
		
		if(null === $event) {
		    $this->session->set_flashdata('error', 'Event not found, try some of the ones below.');
		    redirect('/event/pending');
		}
		
		$data = array(
		    'pending' => 0, 
		    'active' => 1
		);
		
		if($event->save($data)) {
			/** @todo send mail to event contact / manager */
		    $this->session->set_flashdata('message', 'Approval of event ' . escape($event->getTitle()) . '(' . $event->getId() . ') succeeded!');
		    redirect('event/view/' . $event->getId());
		}
		else {
		    //$this->session->set_flashdata('error', 'Approval of event ' . escape($event->getTitle()) . '(' . $event->getId() . ') failed.');
			$this->session->set_flashdata('error', $event->getErrors());
		    redirect('event/view/' . $event->getId());
		}
	}
	
	/**
	 * Shows a list of managers for the event.
	 * @param int|string $event_id
	 */
	function managers($event_id)
	{
	    if(!user_is_authenticated()) {
	        redirect('/user/login');
	    }
	    
	    $this->load->model('EventModel');
	    $event = $this->EventModel->find($event_id);
	    
	    if(null === $event) {
	        redirect('/event');
	    } 
	    else if(!$event->isEventManager(user_get_id()) && !user_is_administrator()) {
	        redirect('/event/view/' . $event->getId());
	    }
		
	    $this->template->write_view('content', 'event/managers', array('event' => $event));
		$this->template->render();
	}
    
    /**
     * Adds a manager to an event.
     * @param int|string $event_id
     * @return boolean
     * */
    function addmanager($event_id)
    {
        if(!user_is_authenticated()) {
	        redirect('/account/login');
	    }
	    
	    $this->load->model('EventModel');
	    $event = $this->EventModel->find($event_id);
	    
	    if(null === $event) {
	        redirect('/event');
	    } 
	    else if(!$event->isEventManager(user_get_id()) && !user_is_administrator()) {
	        redirect('/event');
	    }
        
        $username = trim($_POST['username']);
        $this->load->model('UserModel');
        $user = $this->UserModel->findByUsername($username, true);
        if(null === $user) {
            $this->session->set_flashdata('error', "Username {$username} not found.");
            redirect("/event/managers/{$event->getId()}");
        }
        
        $success = $event->addEventManager($user);
        if(!$success) {
            $this->session->set_flashdata('error', "Adding manager failed.");
            redirect("/event/managers/{$event->getId()}");
        }
        
        $this->session->set_flashdata('message', "Manager added.");
        redirect("/event/managers/{$event->getId()}");
    }
    
    /**
     * Deletes a manager from an event.
     * @param int|string $event_id
     * @param int|string $user_id
     * @return boolean
     */
    function delmanager($event_id, $user_id)
    {
        if(!user_is_authenticated()) {
	        redirect('/account/login');
	    }
	    
	    $this->load->model('EventModel');
	    $event = $this->EventModel->find($event_id);
	    
	    if(null === $event) {
	        redirect('/event');
	    } 
	    else if(!$event->isEventManager(user_get_id()) && !user_is_administrator()) {
	        redirect('/event');
	    }
        
        $success = $event->removeEventManager($user_id);
        if(!$success) {
            $this->session->set_flashdata('error', "Removing manager failed.");
            redirect("/event/managers/{$event->getId()}");
        }
        
        $this->session->set_flashdata('message', "Manager removed.");
        redirect("/event/managers/{$event->getId()}");
    }
	
	/**
	 * Sends out codes for speakers to claim their sessions.
	 * @param int|string $event_id
	 */
	function codes($event_id)
	{
		if(!user_is_authenticated()) {
			redirect('/account/login');
		}
		$event = $this->EventModel->find($event_id);
		if(null === $event) {
			redirect('/event');
		}
		else if(!user_is_administrator() && !$event->isEventManager(user_get_id())) {
			redirect('/event/view/' . $event->getId());
		}
		
		$viewVars = array(
			'event' => $event,
			'email' => array(),
			'error' => array()
		);
		
		$this->load->model('SessionModel');
		$this->load->library('validation');
		
		if('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['send'])) {
			
			$ids = $_POST['send'];
			$emailAddresses = $_POST['email'];
			
			if(count($ids) > 0) {
				foreach($ids as $id => $state) {
					if(!isset($emailAddresses[$id]) || empty($emailAddresses[$id])) {
						continue;
					}
					
					$session = $this->SessionModel->find($id);
					if(null === $session || $session->getEventId() != $event->getId()) {
						continue;
					}
					
					$addresses = split(',', $emailAddresses[$id]);
					$addresses = array_map('trim', $addresses);
					foreach($addresses as $index => $address) {
						if(empty($address)) {
							unset($addresses[$index]);
						}
						else if(!$this->validation->valid_email($address)) {
							$viewVars['email'][$id] = implode(',', $addresses);
							$viewVars['error'][] = 'Email address(es) for session "' . escape($session->getTitle()) .'" are not valid.';
							continue 2;
						}
					}
					
					require_once BASEPATH . '/application/libraries/StringTokenGenerator.php';
					$generator = new StringTokenGenerator($session->getAllStringTokens());
					
					$token = $session->getClaimToken();
					if(empty($token)) {
						$session->setClaimToken($generator->generate())->save();
						$token = $session->getClaimToken();
					}
					
					/** Mailer */
					require_once BASEPATH . 'application/libraries/Mailer.php';
					
					foreach($addresses as $address) {
						$mail = new Mailer(array (
							'to' => $address,
							'from' => $this->config->item('mail_events'),
							'subject' => 'Session Code from join.in: ' . escape($session->getTitle())
						));
						$mail->setBodyFromFile(
							'mail/session_code', 
							escape($session->getTitle()),
							escape($session->getEventTitle()),
							$token
						);
						$mail->send();
					}
				}
			}
		}

		$this->template->write_view('content', 'event/codes', $viewVars);
		$this->template->render();
	}
	
}

?>
