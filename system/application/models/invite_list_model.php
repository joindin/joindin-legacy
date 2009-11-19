<?php

class Invite_list_model extends Model {
	
	function Invite_list_model(){
		parent::Model();
	}
	//------------------
	/**
	* Get the current invites for an event and their status
	*/
	function getEventInvites($eid){
		$sql=sprintf("
			select
				u.username,
				u.ID uid,
				u.full_name,
				il.eid,
				il.date_added,
				il.ID ilid,
				il.accepted
			from
				invite_list il,
				user u
			where
				il.uid=u.ID and
				il.eid=%s
		",$eid);
		$q=$this->db->query($sql);
		return $q->result();
	}
	function isInvited($eid,$uid){
		$q=$this->db->get_where('invite_list',array('eid'=>$eid,'uid'=>$uid));
		$ret=$q->result();
		return (isset($ret[0])) ? true : false;
	}
	function addInvite($eid,$uid){
		$arr=array(
			'eid'			=>$eid,
			'uid'			=>$uid,
			'date_added'	=>time()
		);
		$this->db->insert('invite_list',$arr);
	}
	function acceptInvite($eid,$uid){
		$arr=array('accepted'=>'Y');
		$this->db->where('eid',$eid);
		$this->db->where('uid',$uid);
		$this->db->update('invite_list',$arr);
	}
}