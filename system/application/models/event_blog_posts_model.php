<?php

class Event_blog_posts_model extends Model {

	function Event_blog_posts_model(){
		parent::Model();
	}
	//-------------------
	function getPosts($eid,$first=false){
		$this->db->from('event_blog_posts')
			->join('user','user.ID=event_blog_posts.author_id')
			->where(array('event_id'=>$eid))
			->order_by('date_posted','desc');
		if($first){ $this->db->limit(1); }
		
		$q=$this->db->get();
		return $q->result();
	}
	function getPostDetail($pid){
		$q=$this->db->getWhere('event_blog_posts',array('ID'=>$pid));
		return $q->result();
	}
	function addPost($eid,$data){
		$data['date_posted']= time();
		$data['author_id']	= $this->session->userdata('ID');
		$data['event_id']	= $eid;
		$data['ID']			= null;
		$this->db->insert('event_blog_posts',$data);
	}
	function updatePost($pid,$data){
		$data['author_id']=$this->session->userdata('ID');
		$this->db->update('event_blog_posts',$data,array('ID'=>$pid));
	}
}

?>