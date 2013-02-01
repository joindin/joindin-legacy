<?php
/**
 * Joindin webservice for fetching comments
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
 * Joindin webservice for fetching comments
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Getcomments extends BaseWsRequest
{
    
    public $CI  = null;
    public $xml = null;
    
    /**
     * Instantiates web service for fetching comments for a talk
     *
     * @param string $xml XML sent to web service
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Public method, so this just returns true.
     *
     * @param string $xml XML passed to web service
     *
     * @return true
     */
    public function checkSecurity($xml) 
    {
        // public method!
        return true;
    }

    /**
     * Web service execution
     * Get the talk comments
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->model('talks_model');
        
        $id   = $this->xml->action->talk_id;
        $priv = false;
        
        // If they're giving us credentials, check to see if 
        // they have permission to grab the private comments
        
        if (isset($this->xml->auth)) {
            $this->CI->load->model('user_admin_model', 'uam');
            $this->CI->load->model('user_model', 'um');
            
            $udata = $this->CI->um
                ->getUserByUsername($this->xml->action->username);
            if (!empty($udata)) {
                $priv = ($this->CI->uam->hasPerm(
                    $udata[0]->ID,
                    $id,
                    'talk'
                )) ? true : false;
            }
        }
        $comments = $this->CI->talks_model->getTalkComments($id, null, $priv);
        
        return array('output'=>'json', 'data'=>array('items'=>$comments));
    }
}
