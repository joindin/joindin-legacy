<?php 
/**
 * Joindin webservice for claiming a talk
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
 * Joindin webservice for claiming 
 * an event
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Claim extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;

    /**
     * Instantiate the web service
     *
     * @param string $xml XML sent to service
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Public service
     *
     * @param string $xml XML sent to webservice
     *
     * @return true
     */
    public function checkSecurity($xml) 
    {
        // public function!
        return true;
    }

    /**
     * Does the work to claim the event
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('user_admin_model');
        $this->CI->load->model('event_model');

        $rules = array(
            'eid'=>'required|isevent'
        );

        $eid = $this->xml->action->eid;
        $ret = $this->CI->wsvalidate->validate($rules, $this->xml->action);

        if (!$ret) {
            // Passed validation...
            // Be sure they're logged in
            if ($this->CI->wsvalidate->validate_loggedin()) {
                error_log('logged in!');
                $uid = $this->CI->session->userdata('ID');
                $arr = array(
                    'uid'   => $uid,
                    'rid'   => $eid,
                    'rtype' => 'event',
                    'rcode' => 'pending'
                );

                // Be sure we don't already have a claim pending
                $q   = $this->CI->db->get_where('user_admin', $arr);
                $ret = $q->result();
                if (isset($ret[0]->ID)) {
                    return array(
                        'output'=>'json',
                        'data'=>array(
                            'items'=>array(
                                'msg'=>'You already have an outstanding '.
                                'claim on this event'
                            )
                        )
                    );
                } else {
                    //we're good isert the row!
                    $this->CI->db->insert('user_admin', $arr);
                    return array('output'=>'json',
                        'data'=>array(
                            'items'=>array(
                                'msg'=>'Success')
                            )
                        );
                }
            }
        }
        return array('output'=>'json','items'=>array('msg'=>'Fail'));
    }

}
