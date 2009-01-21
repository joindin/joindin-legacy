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
}

?>