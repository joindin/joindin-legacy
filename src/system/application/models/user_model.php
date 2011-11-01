<?php

class User_model extends Model {

    function User_model() {
        parent::Model();
    }
    
    /**
     * Check to see if the user is authenticated
     * @return mixed Return value is either the username or false
     */
    function isAuth() {
        if ($u=$this->session->userdata('username')) {
            return $u;
        } else { return false; }
    }
    
    /**
     * Get the user's ID from the session
     * @return integer User ID
     */
    function getID() {
        // this only works for web users!
        return $this->session->userdata('ID');
    }
    
    /**
     * Validate that the given username and password are valid
     * @param $user string Username
     * @param $pass string Password
     * @param $plaintxt boolean Flag to treat incoming password as plaintext or md5
     */
    function validate($user, $pass, $plaintxt=false) {
        $ret=$this->getUser($user);
        $pass=($plaintxt) ? $pass : md5($pass);
        $valid = (isset($ret[0]) && $ret[0]->password==$pass) ? true : false;
        return $valid;
    }
    
    /**
     * Output the "logged in"/"logged out" HTML for the template based on login status
     * Directly writes out the HTML to the template
     *
     * @return null
     */
    function logStatus() {
        //piece to handle the login/logout
        $u=$this->isAuth();
        $lstr=($u) ? '<a href="/user/main">'.$u.'</a> <a href="/user/logout">[logout]</a>':'<a href="/user/login">login</a>';
        $this->template->write('logged', $lstr);
    }
    
    /**
     * Check to see if the given user is a site admin
     * If the user is logged in, check their session. If not, search the database
     *
     * @param $user User ID/username
     * @return boolean User's admin status
     */
    function isSiteAdmin($user=null) {
        if (!$this->isAuth()) {
            // get our user information
            if ($user) {
                $udata=$this->getUser($user);
                return (isset($udata[0]) && $udata[0]->admin==1) ? true : false;
            } else { return false; }
        } else {
            return ($this->session->userdata('admin')==1) ? true : false;
        }
    }
    
    /**
     * Check to see if the given user is an admin for the event
     *
     * @param $eid integer Event ID
     * @param $uid integer User ID/username
     * @return boolean User's site admin status
     */
    function isAdminEvent($eid, $uid=null) {
        if ($this->isAuth()) {
            $uid=$this->session->userdata('ID');
        } elseif (!$this->isAuth() && $uid) {
            $udata=$this->getUser($uid);
            if ($udata) {
                $uid=$udata[0]->ID;
            } else { return false; }
        } else { return false; }
        
        $this->db->select('*');
        $this->db->from('user_admin');
        $this->db->where(array('uid'=>$uid,'rid'=>$eid,'rtype'=>'event','IFNULL(rcode,0) !='=>'pending'));
        $q = $this->db->get();
        
        $ret=$q->result();
        return (isset($ret[0]->ID) || $this->isSiteAdmin()) ? true : false;
    }
    
    /**
     * Check to see if the logged in user is an admin for the given talk
     * Looks to see if the user has claimed the talk and if they're an event admin
     * 
     * @param $tid integer Talk ID
     * @return boolean User's admin status related to the talk
     */
    function isAdminTalk($tid) {
        if ($this->isAuth()) {
            $ad		= false;
            $uid	= $this->session->userdata('ID');
            
            $this->db->select('*');
            $this->db->from('talk_speaker');
            $this->db->where(array('speaker_id'=>$uid,'talk_id'=>$tid,'IFNULL(status,0) !='=>'pending'));
            $query = $this->db->get();
            $talk	= $query->result();
            if (isset($talk[0]->ID)) { $ad=true; }
            
            //also check to see if the user is an admin of the talk's event
            $talkDetail = $this->talks_model->getTalks($tid); //print_r($ret);
            if (isset($talkDetail[0]->event_id) && $this->isAdminEvent($talkDetail[0]->event_id)) { $ad=true; }
            return $ad;
        } else { return false; }
    }
    
    /**
     * Toggle the user's status - active/inactive
     * @param $uid integer User ID
     * @return null
     */
    public function toggleUserStatus($uid) {
        $udata	= $this->getUser((int)$uid);
        $up		= ($udata[0]->active==1) ? array('active'=>'0') : array('active'=>'1');
        $this->updateUserinfo($uid, $up);
    }
    
    /**
     * Toggle the user's admin status
     *
     * @param $uid integer User ID
     * @return null
     */
    function toggleUserAdminStatus($uid) {
        $udata=$this->getUser((int)$uid); //echo $uid; print_r($udata);
        $up=($udata[0]->admin==1) ? array('admin'=>null) : array('admin'=>'1');
        $this->updateUserinfo($uid, $up);
    }
    
    /**
     * Update a user's information with given array values
     *
     * @param $uid integer User ID
     * @param $arr array Details to update on user account
     */
    function updateUserInfo($uid, $arr) {
        $this->db->where('ID', $uid);
        $this->db->update('user', $arr);
    }

    /**
     * Search for user information based on a user ID or username
     *
     * @param $in integer/string User ID or Username
     * @return array User details
     */
    function getUser($in) {
            
        if (is_numeric($in)) {
            $q=$this->db->get_where('user', array('ID'=>$in));
                        $result = $q->result();
        } else { 
            $q=$this->db->get_where('user', array('username'=>(string)$in));
                        $result = $q->result();
                        if (!$result)
                        {
                            $q=$this->db->get_where('user', array('email'=>(string)$in));
                            $result = $q->result();
                        }
                }
                if ($result)
                    return $result;
                else
                    return false;
    }

    /**
     * Delete a user with the given ID
     * 
     * @param $userId
     * @return void
     */
    public function deleteUser($userId)
    {
        // remove the user
        $this->db->delete('user', array('ID' => $userId));

        //set their comments to anonymous?
    }
    
    /**
     * Search for publicly-available user information based on a user ID or username
     *
     * A reduced version of the getUser() method so we can safely return these results to the service.
     * Should be used in preference to getUser wherever possible
     *
     * @param $in integer/string User ID or Username
     * @return array User details
     */
    function getUserDetail($in) {
        $this->db->select('username, full_name, ID, last_login');
        if (is_numeric($in)) {
            $q=$this->db->get_where('user', array('ID'=>$in));
        } else { 
            $q = $this->db->get_where('user', array('username'=>(string)$in));
        }
        return $q->result();
    }
    
    /**
     * Search for a user by their email address
     * @param $email string User email address
     * @return array User detail information 
     */
    function getUserByEmail($email) {
        $q=$this->db->get_where('user', array('email'=>$email));
        return $q->result();
    }
        
        function getUserByUsername($username)
        {
            $query = $this->db->get_where('user', array('username' => $username));
            $result = $query->result();
            return $result;
        }
    
    /**
     * Find email addresses for all users marked as site admins 
     * @return array Set of email addresses
     */
    function getSiteAdminEmail() {
        $this->db->select('email')
            ->where('admin',1);
        $q=$this->db->get('user');
        return $q->result();
    }
    
    /**
     * Pull a complete list of all users of the system
     *
     * @param int $limit Limit number of users returned
     * @return array User details
     */
    function getAllUsers($limit=null) {
        $this->db->order_by('username','asc');
        if ($limit != null) {
            $this->db->limit($limit);
        }
        $q=$this->db->get('user');
        return $q->result();
    }
    
    /**
     * Find other users of the system that were speakers at events the given user was a speaker at too
     *
     * @param integer $uid   User ID
     * @param integer $limit [optional] integer Limit the number of results returned
     * @return array         Return array of user's information (user_id, event_id, username, full_name)
     */
    function getOtherUserAtEvt($uid, $limit=15) {
        if (!ctype_digit((string)$limit)) {
            throw new Exception('Expected $limit to be a number but received '.$limit);
        }

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
                talks t,
                talk_speaker ts
            where
                u.ID <> %s and
                u.ID = ts.speaker_id and
                t.ID = ts.talk_id and
                t.event_id in (
                    select
                        distinct t.event_id
                    from
                        talk_speaker ts,
                        talks t
                    where
                        ts.speaker_id = %s and
                        t.ID = ts.talk_id
                )
            order by rand()
            limit %s	
        ", $uid, $uid, $limit);
        $query 		= $this->db->query($sql);
        $speakers	= $query->result();
        
        foreach ($speakers as $speaker) { $other_speakers[$speaker->user_id]=$speaker; }
        return $other_speakers;
    }
    
    /**
     * Search the user information by a string on username and full name fields
     *
     * @param $term string Search string
     * @param $start[optional] Starting point for search (not currently used)
     * @param $end[optional] Ending point for search (not currently used)
     */
    function search($term, $start=null, $end=null) {
        $ci = &get_instance();
        $ci->load->model('talks_model','talksModel');
        $ci->load->model('user_attend_model','userAttend');
        
        $term = mysql_real_escape_string(strtolower($term));
        $sql=sprintf("
            select
                u.username,
                u.full_name,
                u.ID,
                u.admin,
                u.active,
                u.last_login,
                u.email
            from
                user u
            where
                lower(username) like '%%%s%%' or
                lower(full_name) like '%%%s%%'
        ", $term, $term);
        $query	= $this->db->query($sql);
        $results = $query->result();
        foreach ($results as $key => $user) {
            $results[$key]->talk_count 	= count($ci->talksModel->getSpeakerTalks($user->ID));
            $results[$key]->event_count	= count($ci->userAttend->getUserAttending($user->ID));
        }
        return $results;
    }
}
?>
