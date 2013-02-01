<?php 
/**
 * Class to send email to user to claim their talk
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed'); 
}

/**
 * Class to send email to user to claim their talk
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */
class Events
{
    /**
     * Sends the code email to someone to claim their talk
     *
     * @param string  $email   Email address to send email address
     * @param string  $code    Code to claim talk
     * @param array   $details Array with extra parts like an object with the event
     *                         name
     * @param integer $tid     Talk ID                      
     *
     * @return null
     */
    public function sendCodeEmail($email, $code, $details, $tid)
    {
        $CI = &get_instance();
        $CI->load->model('talks_model');
        $ret = $CI->talks_model->getTalks($tid);
        $msg = sprintf(
            'You have been sent this code to claim your talk "%s" for %s. Please
            log in to %s and enter the code below to claim the talk.

            By claiming the talk you will be able to update its information and
            view any private comments from visitors to the site.

            Code: %s',
            $ret[0]->talk_title,
            $details[0]->event_name,
            $this->_config->site_url(),
            $code
        );

        $to   = $email;
        $subj = 'Talk Code from ' . $this->config->item('site_name') . ': '.
            $ret[0]->talk_title;
        mail($to, $subj, $msg, 'From: ' . $this->config->item('email_events'));
    }
}
