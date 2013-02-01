<?php 
/**
 * Joindin webservice for claiming talks
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
 * Joindin webservice for claiming talks
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
     * Instantiates web service for claiming talks
     *
     * @param string $xml XML sent to web service
     */
    public function __construct($xml)
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Ensures the user is logged in or has a valid key
     *
     * @param string $xml XML sent to web service
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        // Just check the key combination on the URL
        return ($this->checkPublicKey() || $this->isValidLogin($xml));
    }
    
    /**
     * Runs the webservice to allow for claimin of talks
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        $this->CI->load->library('sendemail');
        $this->CI->load->model('user_admin_model');
        $this->CI->load->model('user_model');
        $this->CI->load->model('talks_model');
        $this->CI->load->model('event_model');
        
        $rules = array(
            'talk_id' =>'required|istalk',
            //'reqkey'    =>'required|reqkey'
        );

        $tid           = $this->xml->action->talk_id;
        $talkSpeakerId = (int)$this->xml->action->talk_speaker_id;
        
        $ret = $this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$ret) {

            if ($this->CI->wsvalidate->validate_loggedin() 
                || $this->isValidLogin($this->xml)
            ) {
                $uid_session = $this->CI->session->userdata('ID');
                if (empty($uid_session)) {
                    //They're not logged in, coming from the web service
                    // If it is, we need to be sure they've given us the 
                    // user to add the claim for

                    // can only claim talks for ourselves - use logged in user
                    $username = (string)$this->xml->auth->user;
                    if (!isset($username)) {
                        return array(
                            'output'=>'json',
                            'data'=>array(
                                'items'=>array(
                                    'msg'=>'Fail: Username required!')
                                )
                            );
                    }
                    $this->CI->load->model('user_model');
                    $udata = $this->CI->user_model->getUserByUsername($username);
                    if (!empty($udata)) {
                        $uid = $udata[0]->ID;
                    } else {
                        return array(
                            'output'=>'json',
                            'data'=>array(
                                'items'=>array(
                                    'msg'=>'Fail: User not found!')
                                )
                            );
                    }
                } else {
                    // They're logged in, so let's go with that user
                    $uid = $this->CI->session->userdata('ID');
                }

                // take the currently logged in user and insert 
                // them as a pending record
                $speakerClaim = array(
                    'id'         => $talkSpeakerId,
                    'status'     => 'pending',
                    'speaker_id' => $uid
                );
                
                // Be sure there's not one pending
                $query        = $this->CI->db
                    ->get_where('talk_speaker', $speakerClaim);
                $pendingClaim = $query->result();
                
                error_log(print_r($speakerClaim, true));
                error_log(print_r($pendingClaim, true));
                
                if (empty($pendingClaim)) {
                    
                    $talkQuery   = $this->CI->talks_model->getTalks($tid);
                    $talk_detail = $talkQuery[0];
                    $eventAdmins = $this->CI->event_model
                        ->getEventAdmins($talk_detail->event_id);
                    
                    // get our admin emails
                    if (count($eventAdmins)>0) {
                        foreach ($eventAdmins as $admin) { 
                            error_log($admin->email);
                            $to[] = $admin->email; 
                        }
                    }
                    
                    // Insert the claim
                    $this->CI->db->where('ID', $talkSpeakerId);
                    $this->CI->db->update('talk_speaker', $speakerClaim);

                    $this->CI->sendemail->sendPendingClaim($talk_detail, $to);
                    return $this->sendJsonMessage('Success');
                } else {
                    return $this->sendJsonMessage('Fail: Duplicate Claim!');
                }
            
            } else { 
                return $this->sendJsonMessage('redirect:/user/login');
            }
        } else {
            return $this->sendJsonMessage('Fail');
        }
    }
}
