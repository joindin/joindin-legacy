<?php

class User_model extends Model {

	function User_model(){
		parent::Model();
	}
	//---------------------
	function isAuth(){
		if($u=$this->session->userdata('username')){
			return $u;
		}else{ return false; }
	}
	function validate($user,$pass){
		$ret=$this->getUser($user);
		return (isset($ret[0]) && $ret[0]->password==md5($pass)) ? true : false;
	}
	function logStatus(){
		//piece to handle the login/logout
		$u=$this->isAuth();
		$lstr=($u) ? '<a href="/user/main">'.$u.'</a> <a href="/user/logout">[logout]</a>':'<a href="/user/login">login</a>';
		$this->template->write('logged',$lstr);
	}
	function isSiteAdmin(){
		return ($this->session->userdata('admin')==1) ? true : false;
	}
	function isAdminEvent($rid){
		if($this->isAuth()){
			$uid=$this->session->userdata('ID');
			$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rid'=>$rid,'rtype'=>'event'));
			$ret=$q->result();
			return (isset($ret[0]->ID) || $this->isSiteAdmin()) ? true : false;
		}else{ return false; }
	}
	function isAdminTalk($tid){
		if($this->isAuth()){
			$ad=false;
			$uid=$this->session->userdata('ID');
			$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rid'=>$tid,'rtype'=>'talk'));
			$ret=$q->result();
			//return (isset($ret[0]->ID)) ? true : false;
			if(isset($ret[0]->ID)){ $ad=true; }
			
			//also check to see if the user is an admin of the talk's event
			$ret=$this->talks_model->getTalks($tid); //print_r($ret);
			if(isset($ret[0]->event_id) && $this->isAdminEvent($ret[0]->event_id)){ $ad=true; }
			return $ad;
		}else{ return false; }
	}
	//---------------------
	function updateUserInfo($uid,$arr){
		$this->db->where('ID',$uid);
		$this->db->update('user',$arr);
	}
	//---------------------
	function getUser($in){
		if(is_numeric($in)){
			$q=$this->db->get_where('user',array('ID'=>$in));
		}else{ 
			$w="username='".$in."'";
			$q=$this->db->get_where('user',$w);
		}
		return $q->result();
	}
	function getUserByEmail($in){
		$q=$this->db->get_where('user',array('email'=>$in));
		return $q->result();
	}
	function getAllUsers(){
		$q=$this->db->get('user');
		return $q->result();
	}
	function getOtherUserAtEvt($uid){
		//find speakers (users attending too?) that have spoken at conferences this speaker did too
		
		//first, we get all of the events that the user has spoken at
		$sql=sprintf("
			select
				distinct t.event_id
			from
				user_admin ua,
				talks t
			where
				ua.uid=%s and
				ua.rtype='talk' and
				ua.rid=t.ID
		",$uid);
		$q=$this->db->query($sql);
		$eid_list=$q->result();
		
		//now find speakers that spoke at those events
		foreach($eid_list as $k=>$v){
			$sql=sprintf("
				select
					u.ID,
					u.username,
					u.full_name,
					t.event_id
				from
					user u,
					user_admin ua,
					talks t
				where
					ua.uid=u.ID and
					ua.rtype='talk' and
					ua.rid=t.ID and
					t.event_id=%s
			",$v->event_id);
			$q=$this->db->query($sql);
			$user_list=$q->result();
			//echo '<pre>'; print_r($user_list); echo '</pre>'; echo '<br/>';
		}
	}
}
?>