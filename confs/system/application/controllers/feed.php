<?php

class Feed extends Controller {
	
	function Feed(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		//$this->load->helper('url');
		//redirect('user/login');
	}
	function talk($tid){
		$this->load->helper('form');
		$this->load->model('talks_model');
		
		$com=$this->talks_model->getTalkComments($tid);
		$tlk=$this->talks_model->getTalks($tid);
		//echo '<pre>'; print_r($com); echo '</pre>';
		//echo '<pre>'; print_r($tlk); echo '</pre>';
		
		foreach($com as $k=>$v){
			$items[]=array(
				'guid'			=> 'http://joind.in/talk/view/'.$v->talk_id,
				'title'			=> 'Comment on: '.$tlk[0]->talk_title,
				'link'			=> 'http://joind.in/talk/view/'.$v->talk_id,
				'description'	=> $v->comment,
				'pubDate'		=> date('r',$v->date_made)
			);
		}
		//$this->template->write_view('content','feed/feed',$items,TRUE);
		//$this->template->render();
		$this->load->view('feed/feed',array('items'=>$items));
	}
}

?>