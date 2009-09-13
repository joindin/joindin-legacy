<?php
/**
 * Class Main
 * @package Core
 * @subpackage Controllers
 */

/**
 * Shows the sites main page
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Main extends Controller {
	
	function Main(){
		parent::Controller();		
	}
	
	/**
	 * Displays global information for joind.in:
	 * <ul>
	 *  <li>hot events</li>
	 *  <li>upcoming events</li>
	 *  <li>popular sessions</li>
	 * </ul>
	 */
	function index(){
		$this->load->helper('form');
		$this->load->model('SessionModel');
		$this->load->model('EventModel');
		$this->load->helper('reqkey');
		
		// Build protection files
		$requestKey = buildReqKey();
		$secretKey = buildSecFile($requestKey);
		
		// Get hot events
		$hotEvents = $this->EventModel->getHotEvents(3);
		// Get upcoming events
		$upcomingEvents = $this->EventModel->getUpcomingEvents(3);
		// Get popular sessions
		$popularSessions = $this->SessionModel->getPopularSessions(7);
		
		$arr=array(
			'hotEvents'=> $hotEvents,
		    'upcomingEvents'=> $upcomingEvents,
		    'popularSessions'	=> $popularSessions,
			'requestKey' => $requestKey,
			'secretKey' => $secretKey
		);
		
		$this->template->write_view('content','main/index',$arr,TRUE);
		$this->template->render();
	}
}

?>
