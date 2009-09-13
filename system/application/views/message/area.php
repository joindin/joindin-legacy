<?php
/** 
 * View message/area
 * @package Frontend
 * @subpackage Views
 *
 * Looks for $message and $error and display theses messages formatted.
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */

// Check for normal messages
if(isset($message) && !empty($message)) {
    $this->load->view('message/info', array('message' => $message));
}
// Check for error messages
if(isset($error) && !empty($error)) {
    $this->load->view('message/error', array('message' => $error));
}
