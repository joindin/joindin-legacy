<?php
/**
 * Class User
 * @package Core
 * @subpackage Controllers
*/

/**
 * Controls user actions
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class User extends Controller {
	
	function User(){
		parent::Controller();
	}
	
	function index(){
		$this->load->helper('url');
		redirect('user/login');
	}
	
	/**
	 * Displays the login form and handles login attempts.
	 */
	function login(){
		$this->load->library('validation');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('UserModel');
		
		// We use validation only to check the values in the form
		$this->validation->set_fields(array (
		    'user' => 'Username',
		    'password' => 'Password'
		));
		$this->validation->set_rules(array (
		    'user' => 'required',
		    'password' => 'required|callback_validate_user_password'
		));
		
		if($this->validation->run() === false) {
		    // Display errors
		    $referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->session->userdata('referer'));
		    $this->session->set_userdata('referer', $referer);
		}
		else {
    		// Success!, get the user by username
    		$user = $this->UserModel->findByUsername($this->input->post('user'));
    		
    		// We assume only one user was found for the username
    		$user = array_shift($user);
    		
    		// Add the userdata to the session
    		$userData = $user->getData();
    		$this->session->set_userdata($userData);
    		
    		// Update the login information
    		$user->setLastLogin(time());
    		$user->save();
    		
    		$referer = $this->session->userdata('referer');
    		if(!empty($referer) && false === strpos($referer, 'user/login')) {
    		    redirect(str_replace("http://{$_SERVER['HTTP_HOST']}/", '', $referer));
    		} else {
    		    redirect('/user/main');
    		}
    		
		}
		
		$this->template->write_view('content', 'user/login');
		$this->template->render();
	}
	
	/**
	 * Callback for the validator to check if the user is valid.
	 * @param string $password
	 * @return boolean
	 */
	function validate_user_password($password)
	{
		$username = $this->input->post('user');
		
		$user = $this->UserModel->findByUsername($username);
		
		if(null === $user) {
		    $this->validation->set_message('validate_user_password', 'User does not exist!');
		    return false;
		}
		
		// We assume there is only one user found
		$user = array_shift($user);
		
		if(md5($password) !== $user->getPassword()) {
		    $this->validation->set_message('validate_user_password', 'Invalid password!');
            return false;
		}
		
		return true;
	}
	
	/**
	 * Logs out the user by destroying the session. Then redirects to the front page.
	 */
	function logout(){
		$this->load->helper('url');
		$this->session->sess_destroy();
		redirect();
	}
	
	/**
	 * Lets users enter their username or email to reset their password. 
	 * A new password is generated using the StringTokenGenerator and send to 
	 * the user using Mailer.
	 */
	function forgot(){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('UserModel');
		
		$viewVars = array();
		
		// We use validation only to check the values in the form
		$this->validation->set_fields(array (
		    'user' => 'Username',
		    'email' => 'Email Address'
		));
		$this->validation->set_rules(array (
		    'user' => 'trim|xss_clean|callback_validate_account_exists',
		    'email' => 'trim|xss_clean|valid_email|callback_validate_email_exists'
		));
		
		if($this->validation->run() !== false) {
		    
		    $email = $this->input->post('email');
		    $username = $this->input->post('username');
		    // Check which value to select the account by
		    if(!empty($email)) {
		        $user = $this->UserModel->findByEmail($email);
		    } 
		    else if(!empty($username)) {
		        $user = $this->UserModel->findByUsername($username);
		    } 
		    else if(empty($email) && empty($username)) {
		        $viewVars['error'] = 'Either an username or an email address must be specified.';
		    }
		    else {
		        $viewVars['error'] = 'Password reset failed!';
		    }
		    
		    if(isset($user)) {
		        /** StringTokenGenerator */
		        require_once BASEPATH . 'application/libraries/StringTokenGenerator.php';
		        
		        // We assume only one user was found
		        $user = array_shift($user);
		        
		        // Generate a new password
		        $generator = new StringTokenGenerator();
		        $newPassword = $generator->generate();
                
                // Update the user data		        
		        $user->setPassword(md5($newPassword));
		        $user->save();
		        
		        // Send an email
		        /** Mailer */
		        require_once BASEPATH . 'application/libraries/Mailer.php';
		        
		        $mailer = new Mailer(array (
	                'to' => $user->getEmail(),
	                'from' => 'Joind.in <info@joind.in>',
	                'subject' => 'Password reset'
		        ));
		        $mailer->setBodyFromFile('mail/forgot_mail', $user->getUsername(), $newPassword);
		        $mailer->send();
		    }
		} 
		else {
		    // Add validation error string to the view variables
		    $viewVars['error'] = $this->validation->error_string;
		}
		
		$this->template->write_view('content', 'user/forgot', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Shows a registration form and handles registration requests.
	 */
	function register(){
			$this->load->helper('form');
			$this->load->model('UserModel');
			
			$viewVars = array();
			$user = new UserModel();
			
			if($_SERVER['REQUEST_METHOD'] === 'POST') {
			    $user->setData($_POST);
			    if(!$user->validate()) {
			        $viewVars['error'] = $user->getErrors();
			    }
			    else if(!$this->matchPasswords($user->getPassword(), $_POST['password_confirm'])) {
			        $viewVars['error'] = 'Passwords are not identical!';
			    }
			    else {
			        // Encrypt the password
			        $user->setPassword(md5($user->getPassword()));
			        // Set some defautl values
			        $user->setData(array (
			            'active' => 1,
			            'blog' => 0,
			            'admin' => 0,
			            'last_login' => time()
			        ));
			        $user->save();
			        
			        // Log in the user
			        $this->session->set_userdata($user->getData());
    				redirect('user/main');
			    }
			}
			
			$viewVars['user'] = $user;
			
			$this->template->write_view('content', 'user/register', $viewVars);
			$this->template->render();
	}
	
	/**
	 * Checks if the supplied email address exists in the database.
	 * @param string $email
	 * @return boolean
	 */
	function validate_email_exists($email){
	    $user = $this->UserModel->getByEmail($email);
	    
	    if(null === $user) {
	        $this->validation->_error_messages['validate_email_exists'] = 'Email address not found!';
			return false;
	    }
	    
	    return true;
	}
	
	/**
	 * Checks if the supplied username exists
	 * @param string $username
	 * @return boolean
	 */
	function validate_account_exists($account){
	    $user = $this->UserModel->findByUsername($account);
	    
		if(null === $user){
			$this->validation->_error_messages['validate_account_exists'] = 'Username not found!';
			return false;
		}
		
		return true;
	}
	
	/**
	 * Checks if two passwords match
	 * @param string $password
	 * @param string $passwordConfirm
	 * @return boolean
	 */
	function matchPasswords($password, $passwordConfirm)
	{
	    // Clean the confirm password
	    $passwordConfirm = htmlentities($passwordConfirm);
	    
	    return ($password == $passwordConfirm);
	}
	
	/**
	 * Displays an overview page for an authenticated user
	 */
	function main(){
		$this->load->model('UserModel');
		
		if (!user_is_authenticated()) {
		    redirect('/login');
		}
		
		$user = $this->UserModel->find(user_get_id());
		$viewVars = array('user' => $user);
		
		
		/*$fields=array(
			'talk_code'=>'Talk Code'
		);
		$rules=array(
			'talk_code'=>'required'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()!=FALSE){
			$code=$this->input->post('talk_code');
			$ret=$this->talks_model->getTalkByCode($code); //print_r($ret);
			if(!empty($ret)){
				//link our user and talk
				$uid=$this->session->userdata('ID');
				$rid=$ret[0]->ID;
				$this->talks_model->linkUserRes($uid,$rid,'talk');
				$arr['msg']='Talk claimed successfully!';
			}
		}
		$arr['talks']	= $this->talks_model->getUserTalks($this->session->userdata('ID'));
		$arr['comments']= $this->talks_model->getUserComments($this->session->userdata('ID'));
		$arr['is_admin']= $this->user_model->isSiteAdmin();
		*/
		
		
		$this->template->write_view('content', 'user/main', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Shows details for a User
	 * @param int $id
	 */
	function view($id){
		//$this->load->model('talks_model');
		//$this->load->model('user_attend_model','uam');
		$this->load->model('UserModel');
		
		$user = $this->UserModel->find($id);
		if(null === $user) {
			redirect();
		}
		
		$viewVars = array('user' => $user);

        // Get attendance for the user
        $attendance = $user->getAttendance();
        
        // Split the attendance in to list, past and future
        $pastEventsAttended = array();
        $futureEventsAttending = array();
        
        foreach($attendance as $attending) {
            if(null === $attending->getEvent()) {
                continue;
            }
            
            if($attending->getEvent()->getEnd() < time()) {
                $pastEventsAttended[] = $attending->getEvent();
            }
            else {
                $futureEventsAttending[] = $attending->getEvent();
            }
        }

        $viewVars['pastEventsAttended'] = $pastEventsAttended;
        $viewVars['futureEventsAttending'] = $futureEventsAttending;

        // Gather data for the sidebar block        
        $otherSpeakersBlock = array (
            'title' => 'Other Speakers',
            'user' => $user,
            'speakers' => array()
        );
        
		$this->template->write_view('sidebar2', 'sidebar/other-speakers', $otherSpeakersBlock);
		$this->template->write_view('content', 'user/view', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Lets a user manage their information
	 */
	function manage(){
		$this->load->helper('user');
		if(!user_is_authenticated()) {
		    redirect('/user/login');
		}
		
		$this->load->helper('form');
		$this->load->model('UserModel');
		
		$viewVars = array();
		
		$user = $viewVars['user'] = $this->UserModel->find($this->session->userdata('id'));
		
		
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
		    // If the password is empty, remove it from the post array to prevent a validation error
		    if(empty($_POST['password'])) {
		        unset($_POST['password']);
		        unset($_POST['password_confirm']);
		    }
		    
		    // Set the new data
		    $user->setData($_POST);
		    
		    if(!$user->validate()) {
		        $viewVars['error'] = $user->getErrors();
		    }
		    else if(isset($_POST['password_confirm']) && !$this->matchPasswords($user->getPassword(), $_POST['password_confirm'])) {
		        $viewVars['error'] = 'Passwords are not identical!';
		    }
		    else {
		        $user->setPassword(md5($user->getPassword()));
		        $user->save();
		    }
		}
		
		$this->template->write_view('content', 'user/manage', $viewVars);
		$this->template->render();
	}
	
	/**
	 * Shows a list of users for administrative purposes
	 */
	function admin(){
		$this->load->helper('user');
		$this->load->model('UserModel');
		
		if(!user_is_administrator()) {
		    redirect('/user');
		}
		
		$this->template->write_view('content', 'user/admin', array('users' => $this->UserModel->findAll()));
		$this->template->render();
	}
	
}
?>
