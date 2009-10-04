<?php
/* 
 * Speaker profile model
 */

class Speaker_profile_model extends Model {

    function Speaker_profile_model(){
	parent::Model();
    }
    //----------------------
    /**
     * Fetch the prfile information for the given user ID
     */
    function getProfile($uid){
	$q=$this->db->get_where('speaker_profile',array('user_id'=>$uid));
	return $q->result();
    }
    /**
     * Set up a new speaker profile
     */
    function setProfile($data){
	$this->db->insert('speaker_profile',$data);
    }
    /**
     * Given a user ID and key/value, update the user's profile
     */
    function updateProfile($uid,$data){
	$this->db->where('user_id',$uid);
	$this->db->update('speaker_profile',$data);
    }

}

?>
