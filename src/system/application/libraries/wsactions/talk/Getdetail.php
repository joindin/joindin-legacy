<?php
/**
 * Joindin webservice for retrieving talk details
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
 * Joindin webservice for retrieving talk details
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Getdetail extends BaseWsRequest
{
    protected $CI  = null;
    protected $xml = null;
    
    /**
     * Instantiates the web service
     *
     * @param string $xml XML sent to web service
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Public function so anyone can use it.
     *
     * @param string $xml XML sent to webservice 
     *
     * @return true
     */
    public function checkSecurity($xml) 
    {
        // We're a public action, we dont need security
        return true;
    }

    /**
     * Runs the web service that fetches talk details
     *
     * @return array
     */
    public function run() 
    {
        $id = $this->xml->action->talk_id;
        $this->CI->load->model('talks_model');
        $this->CI->load->model('talk_track_model');
        
        $ret['items'] = $this->CI->talks_model->getTalks($id);
        
        // if the event is private, check their credentials
        if (isset($ret['items'][0]) && $ret['items'][0]->private=='Y') {
            if (isset($this->xml->auth) 
                && isset($this->xml->auth->user) 
                && $this->auth->pass
            ) {
                $this->CI->load->model('user_model');
                $user = $this->CI->user_model
                    ->getUserByUsername((string)$this->xml->auth->user);
                
                if (empty($user)) { 
                    return $this->throwError('Not authorized for private talk');
                }
                
                $this->load->model('invite_list_model', 'ilm');
                $is_invited = $this->ilm->isInvited($ret[0]->ID, $uid);
                if (!$is_invited) {
                    return $this->throwError('Not authorized for private talk!');
                }
            } else { 
                // not allowed! no data for you!
                return $this->throwError('Not authorized for private talk!');
            }
        }

        // now reformat speakers and add in the track information before sending it
        if (!empty($ret['items']) && !empty($id)) {
            $ret['items'][0]->tracks = $this->CI->talk_track_model
                ->getSessionTrackInfo($id);

            $speaker = '';

            foreach ($ret['items'][0]->speaker as $speaker_obj) {
                $speaker .= $speaker_obj->speaker_name . ', ';
            }

            $speaker                  = substr($speaker, 0, -2);
            $ret['items'][0]->speaker = $speaker;
        }

        return array('output' => 'json', 'data'=>$ret);
    }
}
