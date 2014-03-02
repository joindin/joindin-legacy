<?php 
/**
 * Joindin webservice for getting event lists
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
 * Joindin webservice for getting event lists
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Getlist extends BaseWsRequest
{
    protected $CI         = null;
    protected $xml        = null;
    private $_valid_types = array(
        'hot', 'upcoming', 'past', 'pending'
    );
    
    /**
     * Instantiates the webservice
     *
     * @param string $xml XML sent to service
     */
    public function Getlist($xml) 
    {
        $this->CI  = &get_instance();
        $this->xml = $xml;
    }

    /**
     * Public method
     *
     * @param string $xml XML sent to service
     *
     * @return true
     */
    public function checkSecurity($xml) 
    {
        // public method!
        return true;
    }

    /**
     * Does the work to fetch the list of events
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        
        $rules = array(
            'event_type' =>'required',
        );

        $valid = $this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$valid) {
            $this->CI->load->model('event_model');
            $this->CI->load->model('user_attend_model');
            
            $type = strtolower($this->xml->action->event_type);
            if (!in_array($type, $this->_valid_types)) {
                return array(
                    'output'=>'json',
                    'data'=>array(
                        'items'=>array(
                            'msg'=>'Invalid event type!')
                        )
                    );
            }

            // retrieve the events
            $events = $this->CI->event_model->getEventsOfType($type, 50);
            
            // identify user so we can do the attending (or not if they're not 
            // identified)
            $uid  = false;
            $user = $this->CI->user_model
                ->getUserByUsername((string)$this->xml->auth->user);
            if ($user) {
                $uid = $user[0]->ID;
            }

            // Filter out a few things first
            foreach ($events as $k=>$evt) {
                unset($events[$k]->score);
                
                if ($uid) {
                    $evt->user_attending = $this->CI->user_attend_model
                        ->chkAttend($uid, $evt->ID);
                }
                if (($evt->private==1 
                    || $evt->private == 'Y') 
                    && !$evt->user_attending
                ) {
                    // not allowed to see the event!
                    unset($events[$k]);
                }
            }

            // Re-index the array, as the unsetting may have upset the apple-cart
            $events = array_values($events);

            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>$events)
                );
        } else {
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Invalid event type!')
                    )
                );
        }
    }
}
