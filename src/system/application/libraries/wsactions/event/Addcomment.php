<?php 
/**
 * Joindin webservice for adding a comment
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
 * Joindin webservice for adding a comment
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Addcomment extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;
    
    /**
     * Instantiates web service for adding a comment
     *
     * @param string $xml XML provided to web service
     *
     * @return void
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Publicly accessible service
     *
     * @param string $xml XML sent to webservice
     *
     * @return true
     */
    public function checkSecurity($xml) 
    {
        // users can comment anonymously, don't require login
        return true;
    }

    /**
     * Adds a comment
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');
        $unq = false;
        
        $rules = array(
            'event_id' =>'required',
            'comment'  =>'required'
        );

        $ret = $this->CI->wsvalidate->validate($rules, $this->xml->action);
        if ($ret) {
            return $this->throwError($ret);
        }

        $unq = $this->CI->wsvalidate->validate_unique(
            'event_comments',
            $this->xml->action
        );

        if ($unq) {
            $in   = (array)$this->xml->action;            
            $user = $this->CI->user_model
                ->getUserByUsername((string)$this->xml->auth->user);
            if ($user && !$this->isValidLogin($this->xml)) {
                return $this->throwError('Invalid permissions');
            }
            
            // Check to see if you can submit a comment to the event....
            $this->CI->load->model('event_model');
            $event_detail = $this->CI->event_model
                ->getEventDetail($in['event_id']);
            
            $adv_mo = strtotime('+3 months', $event_detail[0]->event_start);
            if (time()>$adv_mo) {
                return $this->throwError('Comments not allowed for this talk!');
            }

            $arr = array(
                'event_id'  => $in['event_id'],
                'comment'   => $in['comment'],
                'source'    => isset($in['source']) ? $in['source'] : 'api',
                'date_made' => time(),
                'active'    => 1
            );

            if ($user) {
                $arr['user_id'] = $user[0]->ID;
                $arr['cname']   = $user[0]->full_name;
            }

            $this->CI->db->insert('event_comments', $arr);
            $this->CI->event_model->cacheCommentCount($in['event_id']);

            return $this->throwError('Comments added');
        } else { 
            if (!$unq) {
                $ret = 'Non-unique entry!';
            }
            return $this->throwError($ret);
        }
        return $ret;
    }
}
