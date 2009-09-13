<?php
/**
 * Class Feed
 * @package Core
 * @subpackage Controllers
 */

/**
 * Prepares data to be exported as an RSS feed.
 *
 * @author Chris Cornut <enygma@phpdeveloper.org>
 * @author Mattijs Hoitink <mattijshoitink@gmail.com>
 */
class Feed extends Controller {
	
	function Feed(){
		parent::Controller();
	}
	
	/**
	 * Displays an empty page
	 */
	function index()
	{}
	
	/**
	 * Collects an prepares blog post data for an RSS feed.
	 */
	function blog(){
		$this->load->model('BlogPostModel');
		$items = array();
		
		foreach($this->BlogPostModel->findAll(null, '`date` DESC') as $post){
			$items[] = array(
				'guid' => "http://joind.in/blog/view/{$post->getId()}",
				'title' => $post->getTitle(),
				'link' => "http://joind.in/blog/view/{$post->getId()}",
				'description' => $post->getContent(),
				'pubDate' => date('r', $post->getDate())
			);
		}
		
		$this->load->view('feed/feed', array('items' => $items));
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
	
	
	/**
	 * Provides an rss feed with events or a list of comments for a specific 
	 * event.
	 * @param int $id
	 */
	function event($id = null)
	{
	    $this->load->model('EventModel');
	    
	    $title = '';
	    $items = array();
	    $eventObject = new EventModel();
	    
	    if(null === $id) {
	        $title = 'Events';

	        $events = $eventObject->findAll();
	        foreach($events as $event) {
	            $items[] = array (
	                'guid' => 'http://joind.in/event/' . $event->getId(),
	                'title' => $event->getTitle(),
	                'link' => 'http://joind.in/event/' . $event->getId(),
	                'description' => $event->getDescription(),
	                'pubDate' => date('r', $event->getStart())
	            );
	        }
	    }
	    else { 
	        $event = $eventObject->find($id);
	        
	        if(null === $event) {
	            show_404();
	        }
	        
	        $title = 'Comments on Event ' . $event->getTitle();
	        $comments = $event->getComments();
	        foreach($comments as $comment) {
	            $items[] = array (
	                'guid' => "http://joind.in/event/{$event->getId()}#comments",
	                'title' => "Comment on {$event->getTitle()}",
	                'link' => "http://joind.in/event/{$event->getId()}#comments",
	                'description' => $comment->getComment(),
	                'pubDate' => date('r', $comment->getDate())
	            );
	        }
	    }
	    
	    $this->load->view('feed/feed', array('title' => $title, 'items' => $items));
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
	function event($eid){
		$this->load->model('event_model');
		$this->load->model('event_comments_model','ecm');
		
		$ret    =$this->ecm->getEventComments($eid);
		$edata	=$this->event_model->getEventDetail($eid);
		$items=array();
		foreach($ret as $k=>$v){ //print_r($v);
			$items[]=array(
				'guid'			=>'http://joind.in/event/view/'.$eid.'#comments',
				'title'			=>'Comment on Event "'.$edata[0]->event_name.'"',
				'link'			=>'http://joind.in/event/view/'.$eid.'#comments',
				'description'	=>$v->comment,
				'pubDate'		=>date('r',$v->date_made)
			);
		}
		$this->load->view('feed/feed',array('items'=>$items,'title'=>'Event Comments - "'.$edata[0]->event_name.'"'));
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
