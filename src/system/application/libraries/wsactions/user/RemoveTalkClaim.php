<?php
/**
 * Joindin webservice for removing a claim on a talk
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Joindin webservice for removing a claim on a talk
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class RemoveTalkClaim extends BaseWsRequest
{

    public $CI  = null;
    public $xml = null;

    /**
     * Builds the webservice for removing a claim on a talk
     *
     * @param string $xml XML to set
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Ensures that only site admins can use this functionality
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
     * Runs the webservices and removes a claim on a talk
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('pending_talk_claims_model');

        $result     = array();
        $claim_id   = (int)$this->xml->action->claim_id;
        $css_row_id = (string)$this->xml->action->css_row_id;

        $result['claim_id']   = $claim_id;
        $result['css_row_id'] = $css_row_id;

        $this->CI->pending_talk_claims_model->deleteClaim($claim_id);

        $result['msg'] = 'Success';
        return array('output'=>'json', 'items'=>$result);
    }
}
