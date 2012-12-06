<?php 
/**
 * Joindin webservice for adding an event
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
 * Joindin webservice for adding an event
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Addevent extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;
    
    /**
     * Instantiates the webservice
     *
     * @param string $xml XML sent to webservice
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
    * Right now, only site admins can add events via the web interface
    *
    * @param string $xml XML sent to web service
    *
    * @return boolean
    */
    public function checkSecurity($xml) 
    {
        // Check for a valid login
        if ($this->isValidLogin($xml)) {
            // Now check to see if they're a site admin
            if (!$this->CI->user_model->isSiteAdmin((string)$xml->auth->user)) {
                return false;
            } else { 
                return true; 
            }
            
        } else {
            return false;
        }
    }

    /**
     * Adds the event
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        $id     = $this->xml->action->id;
        $unique = true;
        
        $rules = array(
            'event_name'  =>'required',
            'event_start' =>'required|date_future',
            'event_end'   =>'required',
            'event_loc'   =>'required',
            'event_tz'    =>'required',
            'event_desc'  =>'required'
        );

        $ret = $this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$ret) {
            $unique = $this->CI->wsvalidate
                ->validate_unique('event', $this->xml->action);
        }

        if (!$ret && $unique) {
            $this->CI->load->model('event_model');
            $data = (array)$this->xml->action;
            $arr  = array(
                'event_name'  => $data['event_name'],
                'event_start' => $data['event_start'],
                'event_end'   => $data['event_end'],
                'event_loc'   => $data['event_loc'],
                'event_desc'  => $data['event_desc'],
                'event_tz'    => $data['event_tz'],
                'active'      => '1',
            );

            $this->CI->db->insert('events', $arr);
            
            return array(
                'output'=>'msg',
                'data'=>array(
                    'msg'=>'Event added successfully!')
                );
        } else {
            if (!$unique) {
                $ret = 'Non-unique entry!';
            }

            if (is_array($ret)) {
                $msg = '';
                foreach ($ret as $k=>$v) {
                    $msg .= $v."\n";
                }
                $ret = $msg;
            }
            return array(
                'output'=>'msg',
                'data'=>array(
                    'msg'=>$ret)
                ); 
        }
    }
}
