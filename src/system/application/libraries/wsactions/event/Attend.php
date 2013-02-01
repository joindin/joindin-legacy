<?php 
/**
 * Joindin webservice for indicating someone attended
 * an event
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
 * Joindin webservice for indicating someone attended
 * an event
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Attend extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;
    
    /**
     * Builds the webservice
     *
     * @param string $xml XML setn to service
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Determines if the user is logged in or has a valid public
     * key to use this API
     *
     * @param string $xml XML sent to service
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        // Just check the key combination on the URL
        if ($this->isValidLogin($xml) || $this->checkPublicKey()) {
            return true;
        }

        return false;
    }

    /**
     * Does the work to mark someone as attending an event
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('user_attend_model');
        
        $rules = array(
            'eid' =>'required|isevent',
            //'reqkey'    =>'required|reqkey'
        );

        $eid = $this->xml->action->eid;
        $ret = $this->CI->wsvalidate->validate($rules, $this->xml->action);

        if (!$ret) {
            //see if were logged in - if not, we return the redirect: message back
            if ($this->CI->wsvalidate->validate_loggedin() 
                || $this->isValidLogin($this->xml)
            ) {                
                $uid = $this->CI->session->userdata('ID');
                if (!$uid) {
                    // its an API call, grab from the XML
                    $user = $this->CI->user_model
                        ->getUserByUsername((string)$this->xml->auth->user);
                    $uid  = $user[0]->ID;
                }
                
                //check to see if they have a record - if they do, remove
                //if they don't, add...
                $this->CI->user_attend_model->chgAttendStat($uid, $eid);
                
                return array(
                    'output'=>'json',
                    'data'=>array(
                        'items'=>array(
                            'msg'=>'Success')
                        )
                    );
            } else {
                $this->CI->session->set_userdata('ref_url', 'event/view/'.$eid);

                return array(
                    'output'=>'json',
                    'data'=>array(
                        'items'=>array(
                            'msg'=>'redirect:/user/login')
                        )
                    );
            }
        } else { 
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Fail')
                    )
                );
        }
    }
}
