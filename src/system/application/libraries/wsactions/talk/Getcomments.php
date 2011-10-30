<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getcomments extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Getcomments($xml) {
        $this->CI=&get_instance(); //print_r($this->CI);
        $this->xml=$xml;
    }
    public function checkSecurity($xml) {
        // public method!
        return true;
    }
    //-----------------------
    /**
     * Web service execution
     * Get the talk comments
     */
    public function run() {
        $this->CI->load->model('talks_model');
        
        $id			= $this->xml->action->talk_id;
        $priv		= false;
        
        // If they're giving us credentials, check to see if they have permission
        // to grab the private comments
        
        if (isset($this->xml->auth)) {
            $this->CI->load->model('user_admin_model','uam');
            $this->CI->load->model('user_model','um');
            
            $udata=$this->CI->um->getUser($this->xml->action->username);
            if (!empty($udata)) {
                $priv=($this->CI->uam->hasPerm($udata[0]->ID, $id,'talk')) ? true : false;
            }
        }
        $comments	= $this->CI->talks_model->getTalkComments($id, null, $priv);
        
        return array('output'=>'json', 'data'=>array('items'=>$comments));
    }
}
