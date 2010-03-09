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
	* Check to see if a token belongs to a user
	* $uid integer User ID
	* $tid integer Token ID
	* Return boolean
	*/
	function isUserToken($uid,$tid){
		$sql=sprintf("
			select
				speaker_profile.ID
			from
				speaker_profile,
				speaker_tokens
			where
				user_id=%s and
				speaker_profile_id=speaker_profile.ID and
				speaker_tokens.ID=%s	
		",$uid,$tid);
		$q=$this->db->query($sql);
		$ret=$q->result();
		return (isset($ret[0]->ID)) ? true : false;
	}

    /**
     * Fetch the profile information for the given user ID
     */
    function getProfile($uid){
		$q=$this->db->get_where('speaker_profile',array('user_id'=>$uid));
		return $q->result();
    }

	function getProfileById($pid){
		$q=$this->db->get_where('speaker_profile',array('ID'=>$pid));
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

	/**
	* Get the column names for the types of the speaker profile
	*/
    function getProfileFields(){
		$fields=array();
		$q=$this->db->query('show columns from speaker_profile');
		foreach($q->result() as $k=>$v){
	    	if($v->Field!='ID'){ $fields[]=$v->Field; }
		}
		return $fields;
    }
    //----------------------

	/**
	* Get the details from the speaker's profile based on what the token defines
	* $token string Token name
	*/
	function getDetailByToken($token){
		$tok_detail=$this->getTokenDetail($token);
		
		// Get the fields they're allowing for this token
		$fields=$this->getTokenAccess($tok_detail[0]->ID);
		
		// And get the user's profile...
		$profile=$this->getProfileById($tok_detail[0]->speaker_profile_id);
		$profile=$profile[0];
		
		$details=array();
		foreach($fields as $f){
			$name=$f->field_name;
			if(isset($profile->$name) && !empty($profile->$name)){ $details[$name]=$profile->$name; }
		}
		return $details;
	}
	
	/**
	* Get the full access (all tokens/all fields) information for a profile
	* $pid integer Profile ID
	* $tid[optional] integer Token ID
	*/
    function getProfileAccess($pid,$tid=null){
		$data=array();
		$q=$this->db->get_where('speaker_tokens',array('speaker_profile_id'=>$pid));
		$data['token']=$q->result();
		
		if(isset($data['token'][0])){
			$data['fields']=$this->getTokenAccess($data['token'][0]->ID);
		}
		return (empty($data)) ? false : $data;
    }

	/**
	* Based on a user ID, get the token information for the user's profile
	* $uid integer User ID
	*/
	function getUserProfileAccess($uid){
		$profile=$this->getProfile($uid);
		return $this->getProfileTokens($profile[0]->ID);
	}
	
	/**
	* Based on the token ID, gets the fields that it has access to
	* $tid integer Token ID
	*/
	function getTokenAccess($tid){
		$q=$this->db->get_where('speaker_token_fields',array('speaker_token_id'=>$tid));
		return $q->result();
	}
	
	/**
	* Given the profile ID, get the tokens related to the profile
	* $tid integer Token ID
	*/
	function getProfileTokens($pid){
		$q=$this->db->get_where('speaker_tokens',array('speaker_profile_id'=>$pid));
		return $q->result();
	}
	
	/**
	* Based on a token name, Get the detail from the tokens table
	* $token string Token name
	*/
	function getTokenDetail($token){
		$q=$this->db->get_where('speaker_tokens',array('access_token'=>$token));
		return $q->result();
	}
	
	/**
	* Used to set up a token and the field access that goes along with it
	* $uid integer User ID
	* $name string Token name (user defined)
	* $fields array List of access fields
	*/
    function setProfileAccess($uid,$name,$desc,$fields){
		//First, insert into the token table...
		$profile= $this->getProfile($uid);
		$pid	= $profile[0]->ID;
		$arr=array(
			'speaker_profile_id'	=> $pid,
			'access_token'			=> $name,
			'description'			=> $desc,
			'created'				=> time()
		);
		
		//Be sure we don't already have profile access like this
		$tokens=$this->getProfileTokens($pid);
		foreach($tokens as $t){ if($t->access_token==$name){ return false; }}
		
		//Keep going and do the insert...
		$this->db->insert('speaker_tokens',$arr);
		$tid=$this->db->insert_id();
		
		//Now, for each of the fields they gave us, put its name in the fields table
		foreach($fields as $f){
			$arr=array('speaker_token_id'=>$tid,'field_name'=>$f); print_r($arr);
			$this->db->insert('speaker_token_fields',$arr);
		}
		return true;
    }

	/**
	* Update the token's access fields
	* $uid integer User ID
	* $tid integer Token ID
	* $fields array List of access fields
	*/
    function updateProfileAccess($uid,$tid,$fields){
		// Be sure we're supposed to work on this token
		if(!$this->isUserToken($uid,$tid)){ return false; }
		
		// drop all of the token access fields for the token...
		$this->db->where('speaker_token_id',$tid);
		$this->db->delete('speaker_token_fields');
		
		// Now add in our new ones
		foreach($fields as $f){
			$arr=array('speaker_token_id'=>$tid,'field_name'=>$f);
			$this->db->insert('speaker_token_fields',$arr);
		}
		return true;
    }

	/**
	* Remove the access based on a token ID
	* $uid integer User ID
	* $tid integer Token ID
	*/
	function deleteProfileAccess($uid,$tid){
		// Be sure it's theirs first...
		$profile=$this->getProfile($uid); //print_r($profile);
		
		$tokens=$this->getProfileTokens($profile[0]->ID); //print_r($tokens);
		foreach($tokens as $t){
			if($t->ID==$tid){ 
				$this->db->where('ID',$tid); $this->db->delete('speaker_tokens'); 
				$this->db->where('speaker_token_id',$tid); $this->db->delete('speaker_token_fields'); 
			}
		}
	}
}

?>
