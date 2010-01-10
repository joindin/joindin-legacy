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
	function getID() {
		// this only works for web users!
		return $this->session->userdata('ID');
	}
	function validate($user,$pass,$plaintxt=false){
		$ret=$this->getUser($user);
		$pass=($plaintxt) ? $pass : md5($pass);
		return (isset($ret[0]) && $ret[0]->password==$pass) ? true : false;
	}
	function logStatus(){
		//piece to handle the login/logout
		$u=$this->isAuth();
		$lstr=($u) ? '<a href="/user/main">'.$u.'</a> <a href="/user/logout">[logout]</a>':'<a href="/user/login">login</a>';
		$this->template->write('logged',$lstr);
	}
	function isSiteAdmin($user=null){
		if(!$this->isAuth()){
			// get our user information
			if($user){
				$udata=$this->getUser($user);
				return (isset($udata[0]) && $udata[0]->admin==1) ? true : false;
			}else{ return false; }
		}else{
			return ($this->session->userdata('admin')==1) ? true : false;
		}
	}
	function isAdminEvent($rid,$uid=null){
		if($this->isAuth()){
			$uid=$this->session->userdata('ID');
		}elseif(!$this->isAuth() && $uid){
			$udata=$this->getUser($uid);
			if($udata){
				$uid=$udata[0]->ID;
			}else{ return false; }
		}else{ return false; }
		
		$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rid'=>$rid,'rtype'=>'event'));
		$ret=$q->result();
		return (isset($ret[0]->ID) || $this->isSiteAdmin()) ? true : false;
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
	function toggleUserStatus($uid){
		$udata=$this->getUser((int)$uid); //echo $uid; print_r($udata);
		$up=($udata[0]->active==1) ? array('active'=>'0') : array('active'=>'1');
		$this->updateUserinfo($uid,$up);
	}
	function toggleUserAdminStatus($uid){
		$udata=$this->getUser((int)$uid); //echo $uid; print_r($udata);
		$up=($udata[0]->admin==1) ? array('admin'=>null) : array('admin'=>'1');
		$this->updateUserinfo($uid,$up);
	}
	function updateUserInfo($uid,$arr){
		$this->db->where('ID',$uid);
		$this->db->update('user',$arr);
	}
	//---------------------
	function getUser($in){
		if(is_numeric($in)){
			$q=$this->db->get_where('user',array('ID'=>$in));
		}else{ 
			$q=$this->db->get_where('user',array('username'=>(string)$in));
		}
		return $q->result();
	}
	function getUserByEmail($in){
		$q=$this->db->get_where('user',array('email'=>$in));
		return $q->result();
	}
	function getSiteAdminEmail(){
		$this->db->select('email')
			->where('admin',1);
		$q=$this->db->get('user');
		return $q->result();
	}
	function getAllUsers(){
		$this->db->order_by('username','asc');
		$q=$this->db->get('user');
		return $q->result();
	}
	function getOtherUserAtEvt($uid,$limit=15){
		//find speakers (users attending too?) that have spoken at conferences this speaker did too
		$other_speakers=array();
		$sql=sprintf("
			select
				distinct u.ID as user_id,
				t.event_id,
				u.username,
				u.full_name
			from
				user u,
				user_admin ua,
				talks t
			where
				ua.uid=u.ID and ua.rtype='talk' and ua.rid=t.ID and
				t.event_id in (
					select 
						distinct it.event_id 
					from
						user_admin iua,
						talks it
					where
						iua.uid=%s and
						iua.rtype='talk' and
						iua.rid=it.ID
				) and
				u.ID!=%s
			order by rand()
			limit %s
		",$uid,$uid,$limit);
		$q=$this->db->query($sql);
		$ret=$q->result();
		foreach($ret as $k=>$v){ $other_speakers[$v->user_id]=$v; }
		return $other_speakers;
	}
	//-------------------
	function search($term,$start=null,$end=null){
		$sql=sprintf("
			select
				u.username,
				u.full_name,
				u.ID,
				u.admin,
				u.active,
				u.last_login,
				u.email,
				(select count(ID) from user_admin where rtype='talk' and uid=u.ID) talk_count,
				(select count(ID) from user_attend where uid=u.ID) event_count
			from
				user u
			where
				lower(username) like '%%%s%%' or
				lower(full_name) like '%%%s%%'
		",strtolower($term),strtolower($term));
		$q=$this->db->query($sql);
		return $q->result();
	}
}
?>
