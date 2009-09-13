<?php
/**
 * Class About
 * @package Core
 * @subpackage Controllers
 */

/**
 * Handles information about the joind.in website.
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class About extends Controller {
	
	function About(){
		parent::Controller();
	}
	
	/**
	 * Shows the about page.
	 */
	function index(){
		$this->load->helper('form');
		
		$this->template->write_view('content','about/main');
		$this->template->render();
	}
	
	/**
	 * Shows a contact form that can be used to send message to the site's 
	 * administrators.
	 */
	function contact(){
		$arr=array();
		$this->load->helper('form');
		$this->load->library('akismet');
		$this->load->library('validation');
		
		$viewVars = array();
		
		$fields=array(
			'your_name'	=>'Name',
			'your_email'=>'Email',
			'your_com'	=>'Comments'
		);
		$rules=array(
			'your_name'	=> 'required',
			'your_com'	=> 'required'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);

		if($this->validation->run()!=FALSE){
			$akismetData = array(
				'comment_type' => 'comment',
				'comment_author' => $this->input->post('your_name'),
				'comment_author_email' => $this->input->post('your_email'),
				'comment_content' => $this->input->post('your_com')
			);
			$akismetResult = $this->akismet->send('/1.1/comment-check', $akismetData);
			
			/** Mailer */
			require_once BASEPATH . 'application/library/Mailer.php';
			$mail = new Mailer(array (
			    'to' => 'enygma@phpdeveloper.org',
			    'from' => 'feedback@joind.in',
			    'subject' => 'Feedback from joind.in'
			));
			$mail->setBodyFromViewFile(
			    'contact/contact_mail', 
			    $this->input->post('your_name'),
			    $this->input->post('your_email'),
			    $this->input->post('your_com'),
			    ($akismetResult == 'false') ? 'no' : 'yes'
			);
			$mail->send(); 
			
			$viewVars['msg'] = 'Comments sent! Thanks for the feedback!';
			
			//clear out the values so they know it was sent..
			$this->validation->your_name = '';
			$this->validation->your_email = '';
			$this->validation->your_com = '';						
		}
		
		$this->template->write_view('content', 'about/contact', $viewVars);
		$this->template->render();
	}
}
?>
