<?php

class User_admin_model extends Model {

    /** constructor */
    function User_admin_model() {
        parent::Model();
    }
    
    /**
     * Remove a specific permission row
     *
     * @param integer $aid Resource ID
     * @return void
     */
    public function removePerm($aid) {
        //$arr=array('uid'=>$uid,'rid'=>$rid);
        $this->db->delete('user_admin', array('ID'=>$aid));
    }
    
    /**
     * Remove permission for a user on a resource
     * @param integer $uid User ID
     * @param integer $rid Resource ID
     * @param string $type Resource type (ex. "talk")
     * @return void
     */
    public function removeRidPerm($uid, $rid, $type) {
        $det=array(
            'rid'=>$rid,
            'uid'=>$uid,
            'rtype'=>$type
        );
        $this->db->delete('user_admin', $det);
    }
    
    /**
     * Add permissions for a user to a resource
     *
     * @param integer $uid User ID
     * @param integer $rid Resource ID
     * @param string $type Resource type (ex. "talk")
     * @return void
     */
    public function addPerm($uid, $rid, $type) {
        error_log($uid.'-'.$rid.'-'.$type);
        $arr=array(
            'uid'	=>$uid,
            'rid'	=>$rid,
            'rtype'	=>$type,
            'rcode'	=>''
        );
        $this->db->insert('user_admin', $arr);
    }
    
    /**
     * Update the permissions in the table based on the table ID
     * @param integer $id Table ID
     * @param array $perms Permission settings to change
     * @return void
     */
    public function updatePerm($id, $perms) {
        $this->db->where('id', $id);
        $this->db->update('user_admin', $perms);
    }
    
    /**
     * Check to see if given user has a claim on the ID+type combo
     *
     * @param integer $uid User ID
     * @param integer $rid Resource ID (ex. talk ID)
     * @param string $rtype Resource type (ex. "talk")
     * @return boolean If they have permission or not
     */
    public function hasPerm($uid, $rid, $rtype) {
        $q=$this->db->get_where('user_admin', array('uid'=>$uid,'rid'=>$rid,'rtype'=>$rtype));
        $ret=$q->result(); //print_r($ret);
        return (empty($ret)) ? false : true;
    }

    public function getPendingPerm($uid, $rid, $rtype) {
        error_log($uid.' - '.$rid.' - '.$rtype);
        $q=$this->db->get_where('user_admin', array('uid'=>$uid,'rid'=>$rid,'rtype'=>$rtype,'rcode'=>'pending'));
        $result = $q->result();
        error_log('result: '.print_r($result, true));
        return $result;
    }

    /**
     * Check to see if given permission ID is of rtype/rid type.
     * 
     * @param  $id ID
     * @param  $rid Resource ID (ex talk ID)
     * @param  $rtype Resource type (ex. talk)
     * @return boolean If they have permission or not
     */
    public function checkPerm($id, $rid, $rtype) {
        $q = $this->db->get_where('user_admin', array('ID'=>$id,'rid'=>$rid,'rtype'=>$rtype));
        $ret=$q->result();
        return (empty($ret)) ? false : true;
    }
    
    /**
     * Get detail for a given user - their talks and events
     *
     * @param integer $uid User ID
     * @param array $types[optional] Admin types (talk, event, etc)
     * @param boolean $pending Toggle to show pending claims or not
     * @return array $ret User claim information
     */
    public function getUserTypes($uid, $types=null, $pending=false) {
        $CI=&get_instance();
        
        $CI->load->model('talks_model');
        $CI->load->model('event_model');
        
        $tadd=($types) ? " and ua.rtype in ('".implode("','", $types)."')" : '';
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
        ", $this->db->escape($uid), $pend, $tadd);
        $q=$this->db->query($sql);
        $ret=$q->result();
        
        foreach ($ret as $k=>$v) {
            switch($v->rtype) {
                case 'talk': 
                    $ret[$k]->detail=$CI->talks_model->getTalks($v->rid);
                    break;
                case 'event':
                    $ret[$k]->detail=$CI->event_model->getEventDetail($v->rid);
                    break;
            }
        }
        
        return $ret;
        //$q=$this->db->get_where('user_admin', array('uid'=>$uid));
        //return $q->result(); //print_r($ret);
    }
    
    /**
     * Get the event details of the events the user is an admin on
     *
     * @param integer $uid User ID
     * @return array User admin data
     */
    public function getUserEventAdmin($uid) {
        $sql=sprintf("
            select
                e.event_name,
                e.ID as event_id
            from
                events e,
                user_admin ua
            where
                ua.rid=e.ID and
                ua.rtype='event' and
                ua.uid = %s
        ", $this->db->escape($uid));
        $q=$this->db->query($sql);
        return $q->result();
    }
    
    /**
     * Find the claims for a given talk ID
     * @param integer $talk_id Talk ID #
     * @param boolean $pending[optional] Whether to include pending claims or not
     */
    public function getTalkClaims($talk_id, $pending=false) {
        $this->db->select('*');
        $this->db->from('user_admin');
        $this->db->join('user','user_admin.uid=user.ID');
        $this->db->where('rid', $talk_id);
        $this->db->where('rtype', 'talk');
        if (!$pending) { 
            $this->db->where(array('rcode !='=>'pending'));
        }
        
        $q=$this->db->get();
        $ret=$q->result();
        return $ret;
    }
    
    /**
     * Given the ID from the user_admin table, check to see if the claim is valid & pending
     * 
     * @param integer $claim_id ID from the claim table
     * @return boolean Is valid claim or not
     */
    public function isPendingClaim($claim_id) {
        $q=$this->db->get_where('user_admin', array('ID'=>$claim_id,'rcode'=>'pending'));
        $ret=$q->result();
        return (empty($ret)) ? false : true;
    }
    
    /**
     * Get the pending claims for either a talk or an even
     *
     * @param string $type Type to get claims for
     * @param integer $rid[optional] Resource ID (could be talk ID or event ID)
     * @return array Claim data
     */
    public function getPendingClaims($type='talk', $rid=null) {
        switch($type) {
            //case 'talk':    return $this->getPendingClaims_Talks($rid); break;
            case 'talk':    return $this->getPendingClaim_TalkSpeaker($rid); break;
            case 'event':   return $this->getPendingClaims_Events($rid); break;
        }
    }
    
    /**
     * Get the pending talk clams for the event 
     * @param integer $eid[optional] Event Id
     */
    public function getPendingClaim_TalkSpeaker($eid=null)
    {
        $CI=&get_instance();
        $CI->load->model('talk_speaker_model','talkSpeaker');
        
        $sql = sprintf("
            select
                ts.talk_id,
                ts.speaker_name,
                u.username,
                u.ID as user_id,
                u.full_name as claiming_name,
                ts.ID,
                t.talk_title,
                e.event_name,
                t.ID as talk_id,
                ts.status
            from
                talks t,
                user u,
                talk_speaker ts,
                events e
            where
                ts.talk_id = t.ID and
                u.ID = ts.speaker_id and
                ts.status = 'pending' and
                t.event_id = %s and
                t.event_id = e.ID
        ", $eid);
        
        $query = $this->db->query($sql);
        $results = $query->result();
        
        foreach ($results as $talkKey => $talk) {
            $results[$talkKey]->speakers = $CI->talkSpeaker->getSpeakerByTalkId($talk->talk_id);
        }
        
        return $results;
    }

    /**
    * Get the pending talk claims
    * @param $eid[optional] integer Event ID to restrict on
    */
    public function getPendingClaims_Talks($eid=null) {
        $addl=($eid) ? ' e.ID='.$this->db->escape($eid).' and ' : '';
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
        ", $addl);
        $q=$this->db->query($sql);
        return $q->result();
    }
    public function getPendingClaims_Events($eid=null) {
        $addl=($eid) ? ' e.ID='.$this->db->escape($eid).' and ' : '';
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
        ", $addl);
        $q=$this->db->query($sql);
        return $q->result();
    }

    /**
     * Check that the request token actually exists and is valid
     * 
     * @param string $token the request token supplied by the API
     * @access public
     * @return boolean true if the token is OK, false otherwise
     */
    public function oauthRequestTokenVerify($token)
    {
        $sql = 'SELECT request_token FROM oauth_request_tokens
            WHERE request_token = ' . $this->db->escape($token) . '
            AND authorised_user_id IS NULL';
        $query = $this->db->query($sql);

        $result = $query->result();
        if (count($result) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Deny access for this request token
     * 
     * @param string $token The request token given to the user
     * @access public
     * @return boolean true
     */
    public function oauthDeny($token)
    {
        $sql = 'DELETE from oauth_request_tokens 
            WHERE request_token = ' . $this->db->escape($token);
        $query = $this->db->query($sql);

        return true;
    }

    /**
     * This user granted access for this application using this request 
     * token, record this and give a verification token
     * 
     * @param string $token The request token the user is authorising
     * @param int $user_id The user's database ID (comes from the session 
     * when called from the webcontroller)
     * @access public
     * @return array containing verification code and callback url, or false if something went wrong
     */
    public function oauthAllow($token, $user_id)
    {
        $verification_code = $this->oauthGenerateVerificationCode();
        $sql = 'UPDATE oauth_request_tokens SET authorised_user_id = '
            . $this->db->escape($user_id) . ',
            verification = "' . $verification_code . '"
            WHERE request_token = ' . $this->db->escape($token);
        $query = $this->db->query($sql);

        if ($this->db->affected_rows() == 1) {
            $fetch_sql = 'SELECT callback, verification FROM oauth_request_tokens
                WHERE request_token = ' . $this->db->escape($token);
            $fetch_query = $this->db->query($fetch_sql);

            $result = $fetch_query->result();
            return $result[0];
        }
        return false;
    }

    /**
     * Generate a verification code to send back to the oauth consumer with the user
     * 
     * @access public
     * @return string the verification code
     */
    public function oauthGenerateVerificationCode()
    {
        return substr(md5(rand()), 0, 6);
    }

}

?>
