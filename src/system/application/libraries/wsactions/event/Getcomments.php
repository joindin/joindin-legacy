<?php 
/**
 * Joindin webservice for getting comments
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
 * Joindin webservice for getting comments
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
     * Instantiates the webservice to get comments
     *
     * @param string $xml XML sent to service
     */
    public function Getcomments($xml) 
    {
        $this->CI  = &get_instance(); 
        $this->xml = $xml;
    }

    /**
     * Ensures that the caller sent the event id, otherwise the 
     * API is public
     *
     * @param string $xml XML sent to webservice
     *
     * @return boolean
     *
     * @todo Investigate if checking on event id would give back a bogus
     * error message for when there is no event id.
     */
    public function checkSecurity($xml) 
    {
        //public function!
        // Be sure they've given us an event ID
        if (!isset($xml->action->event_id)) {
            return false;
        }

        return true;
        
    }

    /**
     * Does the work to get the comments for an event
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        
        $rules = array(
            'event_id' =>'required|isevent',
        );
        $eid   = $this->xml->action->event_id;
        $valid = $this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$valid) {
            $this->CI->load->model('event_comments_model');
            $ret = $this->CI->event_comments_model->getEventComments($eid);
            return array('output'=>'json','data'=>array('items'=>$ret));
        } else {
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Invalid Event ID!')
                    )
                );
        }
    }
    
}
