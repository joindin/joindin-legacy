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
     * Instantiates the webservice for deleting comments
     *
     * @param string $xml XML sent to webservice
     */ 
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Ensures that the user calling this API is logged in or has a valid
     * API key. In addition they must be a site admin or an event admin
     * on the event they are modifying.
     *
     * @param string $xml XML sent to service
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        // Check for a valid login
        if ($this->isValidLogin($xml) || $this->checkPublicKey()) {
            // Check to be sure they've given us an event ID & 
            // comment ID
            if (!isset($xml->action->eid) || !isset($xml->action->cid)) {
                return false;
            }
            
            $eid = $xml->action->eid;
            // Now check to see if they're a site admin or an event admin
            $is_site = $this->CI->user_model
                ->isSiteAdmin((string)$xml->auth->user);
            $is_evt  = $this->CI->user_model
                ->isAdminEvent((int)$eid, (string)$xml->auth->user);
            $is_js   = $this->checkPublicKey();
            return ($is_site || $is_evt || $is_js) ? true : false;
            
        } else {
            return false;
        }
    }

    /**
     * Deletes the comment from the database
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('event_comments_model', 'ecm');
        $this->CI->load->model('event_model', 'em');

        $com_id = $this->xml->action->cid;
        $this->CI->ecm->deleteComment($com_id);
        $this->CI->em->cacheCommentCount($this->xml->action->eid);

        return array(
            'output'=>'json',
            'data'=>array(
                'items'=>array(
                    'msg'=>'Success')
                )
            );
    }
    
}
