<?php

class User_admin_model extends Model {

	function User_admin_model(){
		parent::Model();
	}
	//----------------------
	function removePerm($aid){
		//$arr=array('uid'=>$uid,'rid'=>$rid);
		$this->db->delete('user_admin',array('ID'=>$aid));
	}
	function addPerm($uid,$rid,$type){
		error_log($uid.'-'.$rid.'-'.$type);
		$arr=array(
			'uid'	=>$uid,
			'rid'	=>$rid,
			'rtype'	=>$type,
			'rcode'	=>''
		);
		$this->db->insert('user_admin',$arr);
	}
	//----------------------
	function hasPerm($uid,$rid,$rtype){
		$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rid'=>$rid,'rtype'=>$rtype));
		$ret=$q->result(); //print_r($ret);
		return (empty($ret)) ? false : true;
	}
	function getUserTypes($uid,$types=null){
		$CI=&get_instance();
		
		$CI->load->model('talks_model');
		$CI->load->model('event_model');
		
		$tadd=($types) ? " and ua.rtype in ('".implode("','",$types)."')" : '';
		$sql=sprintf("
			select
				ua.uid,
				ua.rid,
				ua.rtype,
				ua.rcode,
				ua.ID admin_id
			from
				user_admin ua
			where
				ua.uid=%s
				%s
		",$uid,$tadd);
		$q=$this->db->query($sql);
		$ret=$q->result();
		
		foreach($ret as $k=>$v){
			switch($v->rtype){
				case 'talk': 
					$ret[$k]->detail=$CI->talks_model->getTalks($v->rid);
					break;
				case 'event':
					$ret[$k]->detail=$CI->event_model->getEventDetail($v->rid);
					break;
			}
		}
		
		return $ret;
		//$q=$this->db->get_where('user_admin',array('uid'=>$uid));
		//return $q->result(); //print_r($ret);
	}

	function getPendingClaims($type='talk'){
	    switch($type){
               case 'talk':    return $this->getPendingClaims_Talks(); break;
               case 'event':   return $this->getPendingClaims_Events(); break;
	    }
	}

	function getPendingClaims_Talks(){
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
	function getPendingClaims_Events(){
           $sql=sprintf("
               select
                   u.ID,
                   u.username claiming_user,
                   u.full_name claiming_name,
                   u.email,
                   e.event_name,
                   e.ID eid,
                   ua.ID ua_id,
                   ua.uid,
                   ua.rid
               from
                   events e,
                   user_admin ua,
                   user u
               where
                   ua.rtype='event' and
                   ua.rcode='pending' and
                   ua.rid=e.ID and
                   ua.uid=u.ID
           ");
           $q=$this->db->query($sql);
           return $q->result();
       }
}

?>