<?php 
/**
 * Joindin webservice for getting talk comments
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
 * Joindin webservice for getting talk comments
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Gettalkcomments extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;
    
    /**
     * Instantiates the service to get talk comments
     *
     * @param string $xml XML sent to service
     */
    public function Gettalkcomments($xml) 
    {
        $this->CI  = &get_instance(); 
        $this->xml = $xml;
    }

    /**
     * Public function but checks that the event id was sent in
     *
     * @param string $xml XML sent to webservice
     *
     * @return boolean
     *
     * @todo Check if event_id check is causing weird errors
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
     * Does the work to get the comments for a talk
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
            $this->CI->load->model('event_model');
            $ret = $this->CI->event_model
                ->getEventFeedback($eid, 'tc.date_made DESC');
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>$ret)
                );
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
