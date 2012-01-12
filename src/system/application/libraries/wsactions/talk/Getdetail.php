<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getdetail extends BaseWsRequest {
    
    private $CI		= null;
    private $xml	= null;
    
    public function Getdetail($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        // We're a public action, we dont need security
        return true;
    }
    //-----------------------
    public function run() {
        $id=$this->xml->action->talk_id;
        $this->CI->load->model('talks_model');
        $this->CI->load->model('talk_track_model');
        
        $ret['items']=$this->CI->talks_model->getTalks($id);
        
        // if the event is private, check their credentials
        if (isset($ret['items'][0]) && $ret['items'][0]->private=='Y') {
            if (isset($this->xml->auth) && isset($this->xml->auth->user) && $this->auth->pass) {
                $this->CI->load->model('user_model');
                $user=$this->CI->user_model->getUser($this->xml->auth->user);
                
                if (empty($user)) { return $this->throwError('Not authorized for private talk'); }
                
                $this->load->model('invite_list_model','ilm');
                $is_invited=$this->ilm->isInvited($ret[0]->ID, $uid);
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
            $ret['items'][0]->tracks = $this->CI->talk_track_model->getSessionTrackInfo($id);
            $speaker = '';
            foreach ($ret['items'][0]->speaker as $speaker_obj) {
                $speaker .= $speaker_obj->speaker_name . ', ';
            }
            $speaker = substr($speaker, 0, -2);
            $ret['items'][0]->speaker = $speaker;
        }

        return array('output' => 'json', 'data'=>$ret);
    }
}
