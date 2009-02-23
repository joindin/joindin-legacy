<?php

class User_attend_model extends Model {

	function User_attend_model(){
		parent::Model();
	}
	//--------------
	function chkAttend($uid,$eid){
		$q=$this->db->get_where('user_attend',array('uid'=>$uid,'eid'=>$eid));
		$ret=$q->result();
		return (empty($ret)) ? false : true;
	}
	function chgAttendStat($uid,$eid){
		if($this->chkAttend($uid,$eid)){
			//they are attending, remove them
			$this->db->delete('user_attend',array('uid'=>$uid,'eid'=>$eid));
		}else{ 
			//they're not attending, add them
			$this->db->insert('user_attend',array('uid'=>$uid,'eid'=>$eid));
		}
	}
	function getAttendCount($eid){
		$sql='select count(ID) attend_ct from user_attend where eid='.$eid;
		$q=$this->db->query($sql);
		$res=$q->result();
		return (isset($res[0]->attend_ct)) ? $res[0]->attend_ct : 0;
	}
	
    function getAttendees($eid){
		$this->db->select('user.*');
	    $this->db->from('user_attend');
		$this->db->join('user', 'user.ID = user_attend.uid', 'inner');
		$this->db->where('user_attend.eid='.(int)$eid);
		$this->db->order_by('user_attend.ID','desc');

		$q=$this->db->get();
		return $q->result();
	}
	
}
?>