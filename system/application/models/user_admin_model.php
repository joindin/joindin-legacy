<?php

class User_admin_model extends Model {

	function User_admin_model(){
		parent::Model();
	}
	//----------------------
	function hasPerm($uid,$rid,$rtype){
		$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rid'=>$rid,'rtype'=>$rtype));
		$ret=$q->result(); //print_r($ret);
		return (empty($ret)) ? false : true;
	}
	function getPendingClaims(){
		$sql=sprintf("
			select
				ua.uid,
				ua.rid,
				t.talk_title,
				t.speaker,
				t.ID talk_id,
				ua.id ua_id,
				u.username claiming_user,
				u.full_name claiming_name,
				u.email,
				e.ID eid,
				e.event_name
			from
				user_admin ua,
				talks t,
				user u,
				events e
			where
				ua.rcode='pending' and
				ua.rtype='talk' and
				t.id=ua.rid and
				u.id=ua.uid and
				e.id=t.event_id
		");
		$q=$this->db->query($sql);
		return $q->result();
	}
}

?>