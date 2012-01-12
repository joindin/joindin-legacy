<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Web Service Action: Add a talk comment
 */
class Addcomment extends BaseWsRequest {

    var $CI	= null;
    var $xml= null;

    public function Addcomment($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        $this->CI->load->model('user_model');

        // Check to see if what they gave us is a valid login
        // Check for a valid login
        return ($this->isValidLogin($xml)) ? true : false;
    }
    //-----------------------
    public function run() {
        $this->CI->load->library('wsvalidate');

        $rules=array(
            'talk_id'	=>'required',
            'rating'	=>'required|range[1,5]',
            'comment'	=>'required',
            'private'	=>'required|range[0,1]'
        );
        $ret=$this->CI->wsvalidate->validate($rules, $this->xml->action);
        $unq=$this->CI->wsvalidate->validate_unique('talk_comments', $this->xml->action);

        if (!$ret && $unq) {
            $this->CI->load->model('talks_model');
            $this->CI->load->model('event_model');

            $in			 = (array)$this->xml->action;
            $talk_detail = $this->CI->talks_model->getTalks($in['talk_id']);
            $user		 = $this->CI->user_model->getUser($this->xml->auth->user);

            // event ID in $talk_detial[0]->eid

            if (!$talk_detail[0]->allow_comments) {
                // we can't comment on this! same logic as fromtend
                return $this->throwError('Comments not allowed on the event/talk!');
            }

            // Ensure this is a valid talk
            if (empty($talk_detail)) {
                $ret=array('output'=>'json','data'=>array('items'=>array('msg'=>'Invalid talk ID!')));
                return $this->throwError('Invalid talk ID!');
            }
            // Ensure that they can comment on it (time-based)
            if (empty($talk_detail[0]->allow_comments)) {
                return $this->throwError('Comments not allowed for this talk!');
            }
            // Ensure that speakers cannot rate their own talks
            if (isset($talk_detail[0]->uid) && ($user[0]->id === $talk_detail[0]->uid) && !empty($in['rating'])) {
                return $this->throwError('Speakers are not allowed to rate their own talks!');
            }


            $arr=array(
                'talk_id'	=> $in['talk_id'],
                'rating'	=> $in['rating'],
                'user_id'	=> $user[0]->ID,
                'comment'	=> $in['comment'],
                'date_made'	=> time(),
                'private'	=> $in['private'],
                'active'	=> 1,
                'source'	=> isset($in['source']) ? $in['source'] : 'api'
            );

            $this->CI->db->insert('talk_comments', $arr);
            return $this->throwError('Comment added!');
        } else {
            if (!$unq) { $ret='Non-unique entry!'; }
            return $this->throwError($ret);
        }
        return $ret;
    }
}
