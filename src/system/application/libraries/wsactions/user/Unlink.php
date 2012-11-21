<?php 
/**
 * Joindin webservice for unlinking a speaker from a talk
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   WebServices
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Unlink a user from a talk

/**
 * Joindin webservice for unlinking a speaker from a talk
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   WebServices
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Unlink extends BaseWsRequest
{

    public $CI  = null;
    public $xml = null;

    /**
     * Instantiates the unlink object. Sets the xml value on the object
     * and gets the CodeIgniter instance
     *
     * @param string $xml XML to set
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Only site admins can use this functionality
     *
     * Determines if the user is an admin and able to use this functionality
     *
     * @param string $xml XML to check
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        $this->CI->load->model('user_model');

        // Check for a valid login
        //if ($this->isValidLogin($xml) || $this->CI->user_model->isAuth()) {
        if ($this->CI->user_model->isAuth()) {
            // Now check to see if they're a site admin
            $user = $this->CI->session->userdata('username');
            if (!$this->CI->user_model->isSiteAdmin($user)) {
                return false;
            } else {
                return true; 
            }

        } else {
            return false; 
        }
    }

    /**
     * Runs the user unlink webservice code
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('talk_speaker_model');

        $result               = array();
        $talk_id              = (int)$this->xml->action->talk_id;
        $speaker_id           = (int)$this->xml->action->speaker_id;
        $css_row_id           = (string)$this->xml->action->css_row_id;
        $result['talk_id']    = $talk_id;
        $result['speaker_id'] = $speaker_id;
        $result['css_row_id'] = $css_row_id;

        $this->CI->talk_speaker_model->unlinkSpeaker($talk_id, $speaker_id);

        $result['msg'] = 'Success';
        return array('output'=>'json', 'items'=>$result);
    }

}
