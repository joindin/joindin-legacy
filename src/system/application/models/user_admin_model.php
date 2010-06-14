<?php

class User_admin_model extends Model {

	function User_admin_model(){
		parent::Model();
	}
	//----------------------
	public function removePerm($aid){
		//$arr=array('uid'=>$uid,'rid'=>$rid);
		$this->db->delete('user_admin',array('ID'=>$aid));
	}
	public function removeRidPerm($uid,$rid,$type){
		$det=array(
			'rid'=>$rid,
			'uid'=>$uid,
			'rtype'=>$type
		);
		$this->db->delete('user_admin',$det);
	}
	public function addPerm($uid,$rid,$type){
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
	public function hasPerm($uid,$rid,$rtype){
		$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rid'=>$rid,'rtype'=>$rtype));
		$ret=$q->result(); //print_r($ret);
		return (empty($ret)) ? false : true;
	}
	public function getUserTypes($uid,$types=null,$pending=false){
		$CI=&get_instance();
		
		$CI->load->model('talks_model');
		$CI->load->model('event_model');
		
		$tadd=($types) ? " and ua.rtype in ('".implode("','",$types)."')" : '';
		$pend=($pending) ? " and rcode='pending'" : '';
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
				ua.uid=%s %s
				%s
		",$uid,$pend,$tadd);
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
	
	/**
	 * Find the claims for a given talk ID
	 * @param integer $talk_id Talk ID #
	 * @param boolean $pending[optional] Whether to include pending claims or not
	 */
	public function getTalkClaims($talk_id,$pending=false){
		$this->db->select('*');
		$this->db->from('user_admin');
		$this->db->join('user','user_admin.uid=user.ID');
		$this->db->where('rid',$talk_id);
		if(!$pending){ 
			$this->db->where(array('rcode !='=>'pending'));
		}
		
		$q=$this->db->get();
		$ret=$q->result();
		
		return $ret;
	}
	
	public function getPendingClaims($type='talk',$rid=null){
	    switch($type){
               case 'talk':    return $this->getPendingClaims_Talks($rid); break;
               case 'event':   return $this->getPendingClaims_Events($rid); break;
	    }
	}

	/**
	* Get the pending talk claims
	* @param $eid[optional] integer Event ID to restrict on
	*/
	public function getPendingClaims_Talks($eid=null){
		$addl=($eid) ? ' e.ID='.$eid.' and ' : '';
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
			    u.id=ua.uid and %s
			    e.id=t.event_id
	    ",$addl);
	    $q=$this->db->query($sql);
	    return $q->result();
	}
	public function getPendingClaims_Events($eid=null){
		$addl=($eid) ? ' e.ID='.$eid.' and ' : '';
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
	            ua.rid=e.ID and %s
	            ua.uid=u.ID
	    ",$addl);
	    $q=$this->db->query($sql);
	    return $q->result();
	}
}

?>