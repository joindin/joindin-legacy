<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Getdetail extends BaseWsRequest {
	
	private $CI		= null;
	private $xml	= null;
	
	public function Getdetail($xml){
		$this->CI=&get_instance(); //print_r($this->CI);
		$this->xml=$xml;
	}
	public function checkSecurity($xml){
		// We're a public action, we dont need security
		return true;
	}
	//-----------------------
	public function run(){
		$id=$this->xml->action->talk_id;
		$this->CI->load->model('talks_model');
		$this->CI->load->model('talk_track_model');
		$ret['items']=$this->CI->talks_model->getTalks($id);

		// now add in the track information before sending it
		$ret['items'][0]->tracks = $this->CI->talk_track_model->getSessionTrackInfo($id);

		return array('output' => 'json', 'data'=>$ret);
	}
}
