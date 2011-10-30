<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Gettalks extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Gettalks($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        //public function!
        // Be sure they've given us an event ID
        if (!isset($xml->action->event_id)) { return false; }

        return true;
        
    }
    //-----------------------
    public function run() {
        $this->CI->load->library('wsvalidate');
        
        $rules=array(
            'event_id'		=>'required|isevent',
            //'reqkey'	=>'required|reqkey'
        );
        $eid=$this->xml->action->event_id;
        $valid=$this->CI->wsvalidate->validate($rules, $this->xml->action);
        if (!$valid) {
            $this->CI->load->model('event_model');
            $this->CI->load->model('talk_track_model');
            $ret=$this->CI->event_model->getEventTalks($eid, false);

            // add the track and format the speaker information for each talk
            foreach ($ret as $talk) {
                $talk->tracks = $this->CI->talk_track_model->getSessionTrackInfo($talk->ID);
                $speaker = '';
                if (count($talk->speaker)) {
                    foreach ($talk->speaker as $speaker_obj) {
                        $speaker .= $speaker_obj->speaker_name . ', ';
                    }
                    $speaker = substr($speaker, 0, -2);
                }
                $talk->speaker = $speaker;
            }
            return array('output'=>'json','data'=>array('items'=>$ret));
        } else {
            return array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid Event ID!')));
        }
    }
    
}
?>
