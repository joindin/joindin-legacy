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
	function blog(){
		$this->load->model('blog_posts_model','bpm');
		$items=array();
		
		foreach($this->bpm->getPostDetail() as $k=>$v){
			//print_r($v);
			$items[]=array(
				'guid'			=>'http://joind.in/blog/view/'.$v->ID,
				'title'			=>$v->title,
				'link'			=>'http://joind.in/blog/view/'.$v->ID,
				'description'	=>$v->content,
				'pubDate'		=>date('r',$v->date_posted)
			);
		}
		$this->load->view('feed/feed',array('items'=>$items));
	}
	function user($in){
		$this->load->model('talks_model');
		$this->load->model('talk_comments_model','tcm');
		$this->load->model('event_comments_model','ecm');
		$udata=$this->user_model->getUser($in);
		$talks		= array();
		$comments	= array();
		
		if(!empty($udata)){
			$uid=$udata[0]->ID;
			//get the upcoming talks for this user
			$ret=$this->talks_model->getUserTalks($uid); //echo '<pre>'; print_r($ret); echo '</pre>';
			//resort them by date_given
			$tmp=array(); $out=array();
			foreach($ret as $k=>$v){ $tmp[$k]=$v->date_given; } arsort($tmp);
			foreach($tmp as $k=>$v){ $out[]=$ret[$k]; }
			
			//$v['title'],$v['desc'],$v['speaker'],$v['date'],$v['tid']);
			
			foreach($out as $k=>$v){
				$talks[]=array(
					'title'		=> $v->talk_title,
					'desc'		=> $v->talk_desc,
					'speaker'	=> $v->speaker,
					'date'		=> date('r',$v->date_given),
					'tid'		=> $v->tid,
					'link'		=> 'http://joind.in/talk/view/'.$v->tid
				);
			}
			$coms=array();
			
			//echo '<pre>';
			//on to the comments!
			$ecom=$this->ecm->getUserComments($uid);
			//print_r($ecom);
			foreach($ecom as $k=>$v){
				$comments[]=array(
					'content'		=> $v->comment,
					'date'			=> date('r',$v->date_made),
					'type'			=> 'event',
					'event_id'		=> $v->event_id
				);
			}

			$tcom=$this->tcm->getUserComments($uid);
			//print_r($tcom);
			foreach($tcom as $k=>$v){
				$comments[]=array(
					'content'		=> $v->comment,
					'date'			=> date('r',$v->date_made),
					'type'			=> 'talk',
					'event_id'		=> ''
				);
			}
			//echo '</pre>';
		}
		$data=array(
			'talks'		=> $talks,
			'comments'	=> $comments,
			'username'	=> $this->session->userdata('username')
		);
		$this->load->view('feed/user',$data);
	}
}

?>