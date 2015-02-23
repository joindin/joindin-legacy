<?php 
/**
 * Joindin webservice for deleting comments
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
 * Joindin webservice for deleting comments
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Deletecomment extends BaseWsRequest
{
    
    public $CI  = null;
    public $xml = null;
    
    /**
     * Instantiates the webservie to delete a comment
     *
     * @param string $xml XML sent to web service
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Only event admins and talk owners can remove comments
     *
     * @param string $xml XML sent to web service
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        $this->CI->load->model('user_admin_model', 'uam');
        $this->CI->load->model('user_model');
        
        // Check for a valid login
        if ($this->isValidLogin($xml) || $this->checkPublicKey()) {
            if (!isset($xml->action->eid) || !isset($xml->action->cid)) {
                return false;
            }

            $eid = $xml->action->eid;
            // Now check to see if they're a site admin or an event admin
            $is_site = $this->CI->user_model
                ->isSiteAdmin((string)$xml->auth->user);
            $is_evt  = $this->CI->user_model
                ->isAdminEvent((int)$eid, (string)$xml->auth->user);
 
            return ($is_site || $is_evt) ? true : false;
        }
        return false; 
    }

    /**
     * Runs the webservice to delete the comment
     *
     * @return array
     */
    public function run() 
    {
        // Be sure we're getting out right input
        if (!isset($this->xml->action->cid)) {
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Missing Input Values!')
                    )
                );
        }
        
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('talk_comments_model', 'tcm');
        
        $com_id = $this->xml->action->cid;
        $this->CI->tcm->deleteComment($com_id);
        
        return array(
            'output'=>'json',
            'data'=>array(
                'items'=>array(
                    'msg'=>'Success')
                )
            );
    }
    
}
