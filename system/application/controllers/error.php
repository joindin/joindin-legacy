<?php
/**
 * Class Error
 * @package Core
 * @package Controllers
 */

/**
 * Handles 404 pages
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 */
class Error extends Controller {
	
	function error_404(){
		$arr=array('msg'=>"404 - File not found");
		$this->template->write_view('content','error/404',$arr);
		$this->template->render();
	}
	
}

