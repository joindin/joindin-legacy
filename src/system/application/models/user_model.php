<?php
/**
 * User model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * User model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class User_model extends Model
{
    /**
     * Check to see if the user is authenticated
     *
     * @return mixed Return value is either the username or false
     */
    public function isAuth()
    {
        $u = $this->session->userdata('username');
        if ($u) {
            return $u;
        }

        return false;
    }

    /**
     * Get the user's ID from the session
     *
     * @return integer User ID
     */
    public function getID()
    {
        // this only works for web users!
        return $this->session->userdata('ID');
    }

    /**
     * Validate that the given username and password are valid
     *
     * @param string  $user     Username
     * @param string  $pass     Password
     * @param boolean $isMd5    Flag to indicate whether incoming password 
     *                          is plaintext or md5
     *
     * @return boolean
     */
    public function validate($user, $userPass, $isMd5 = false, CI_Input $input = null)
    {
        $ret   = $this->getUserByUsername($user);
        // make sure we're using an md5 format, passwords are hashed md5s (yes, really)
        $pass  = ($isMd5) ? $userPass : md5($userPass);

        // did we get a row and do the passwords match?
        if(isset($ret[0])) {
            if(password_verify($pass, $ret[0]->password)) {
                return true;
            } else {
                // may be the password in the database was stored when CI's
                // global_xss_filtering was set to true. We can only test for
                // this if the password passed in was not md5'd.
                if (false === $isMd5) {
                    $pass = $input->xss_clean($userPass);
                    $pass = md5($pass);
                    if (password_verify($pass, $ret[0]->password)) {
                        // it was! Let's store the actually $userPass
                        $password = password_hash(md5($userPass), PASSWORD_DEFAULT);

                        $this->db->where('username', $user);
                        $this->db->update('user', array('password' => $password));
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Output the "logged in"/"logged out" HTML for the template
     * based on login status
     *
     * Directly writes out the HTML to the template
     *
     * @return void
     */
    public function logStatus()
    {
        //piece to handle the login/logout
        $u    = $this->isAuth();
        $lstr = ($u) ? '<a href="/user/main">' . $u .
            '</a> <a href="/user/logout">[logout]</a>' :
            '<a href="/user/login">login</a>';
        $this->template->write('logged', $lstr);
    }

    /**
     * Check to see if the given user is a site admin
     *      Check the passed user is an admin,
     *      if no username is passed check for logged in user
     *
     * @param string $username username
     *                         (WARNING this accepted user_id once upon a time)
     *
     * @return boolean User's admin status
     */
    public function isSiteAdmin($username = null)
    {
        if ($username !== null) {
            $udata = $this->getUserByUsername($username);

            return (isset($udata[0]) && $udata[0]->admin == 1) ? true : false;
        } elseif (!$this->isAuth()) {
            return false;
        } else {
            return ($this->session->userdata('admin') == 1) ? true : false;
        }
    }

    /**
     * Check to see if the given user is an admin for the event
     *
     * @param integer $eid Event ID
     * @param integer $uid User ID/username
     *
     * @return boolean User's site admin status
     */
    public function isAdminEvent($eid, $uid = null)
    {

        if ($this->isAuth()) {
            $uid = $this->session->userdata('ID');
        } elseif (!$this->isAuth() && $uid) {
            $udata = $this->getUserByUsername($uid);
            if ($udata) {
                $uid = $udata[0]->ID;
            } else {
                return false;
            }
        } else {
            return false;
        }

        $this->db->select('*');
        $this->db->from('user_admin');
        $this->db->where(
            array(
                 'uid'                => $uid,
                 'rid'                => $eid,
                 'rtype'              => 'event',
                 'IFNULL(rcode,0) !=' => 'pending'
            )
        );
        $q = $this->db->get();

        $ret = $q->result();

        return (isset($ret[0]->ID) || $this->isSiteAdmin()) ? true : false;
    }

    /**
     * Check to see if the logged in user is an admin for the given talk
     * Looks to see if the user has claimed the talk and if they're an event admin
     *
     * @param integer $tid Talk ID
     *
     * @return boolean User's admin status related to the talk
     */
    public function isAdminTalk($tid)
    {
        if (!$this->isAuth()) {
            return false;
        }

        $ad  = false;
        $uid = $this->session->userdata('ID');

        $this->db->select('*');
        $this->db->from('talk_speaker');
        $this->db->where(
            array(
                 'speaker_id'          => $uid,
                 'talk_id'             => $tid,
                 'IFNULL(status,0) !=' => 'pending'
            )
        );
        $query = $this->db->get();
        $talk  = $query->result();
        if (isset($talk[0]->ID)) {
            $ad = true;
        }

        //also check to see if the user is an admin of the talk's event
        $talkDetail = $this->talks_model->getTalks($tid); //print_r($ret);
        if (isset($talkDetail[0]->event_id)
            && $this->isAdminEvent($talkDetail[0]->event_id)
        ) {
            $ad = true;
        }

        return $ad;

    }

    /**
     * Toggle the user's status - active/inactive
     *
     * @param integer $uid User ID
     *
     * @return null
     */
    public function toggleUserStatus($uid)
    {
        $udata = $this->getUserById((int)$uid);
        $up    = ($udata[0]->active == 1) ?
            array('active' => '0') : array('active' => '1');
        $this->updateUserinfo($uid, $up);
    }

    /**
     * Toggle the user's admin status
     *
     * @param integer $uid User ID
     *
     * @return null
     */
    public function toggleUserAdminStatus($uid)
    {
        $udata = $this->getUserById((int)$uid);
        $up    = ($udata[0]->admin == 1) ?
            array('admin' => null) : array('admin' => '1');
        $this->updateUserinfo($uid, $up);
    }

    /**
     * Update a user's information with given array values
     *
     * @param integer $uid User ID
     * @param array   $arr Details to update on user account
     *
     * @return void
     */
    public function updateUserInfo($uid, $arr)
    {
        $this->db->where('ID', $uid);
        $this->db->update('user', $arr);
    }

    /**
     * Search for user information based on a twitter screen name
     *
     * @param string|integer $screenName User ID or Username
     *
     * @return array User details
     */
    public function getUserByTwitter($screenName)
    {
        // Strip @ sign if needed
        if ($screenName[0] == '@') {
            $screenName = substr($screenName, 1);
        }
        $this->db->where('twitter_username', (string)$screenName);
        $this->db->orwhere('twitter_username', (string)'@' . $screenName);
        $q      = $this->db->get('user');
        $result = $q->result();

        return $result ? $result : false;
    }

    /**
     * Delete a user with the given ID
     *
     * @param integer $userId User id
     *
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
     * A reduced version of the getUserBy*() methods so we can safely return these
     * results to the service.
     *
     * Should be used in preference to getUser wherever possible
     *
     * @param integer $in User ID
     *
     * @return array User details
     */
    public function getUserDetail($in)
    {
        $this->db->select('username, full_name, ID, last_login');
        $q = $this->db->get_where('user', array('ID' => $in));

        return $q->result();
    }

    /**
     * Search for a user by their email address
     *
     * @param string $email User email address
     *
     * @return array User detail information
     */
    public function getUserByEmail($email)
    {
        $q = $this->db->get_where('user', array('email' => $email));

        return $q->result();
    }

    /**
     * Retrieves a user by username
     *
     * @param string $username Username to lookup
     *
     * @return bool
     */
    public function getUserByUsername($username)
    {
        if (!empty($username)) {
            $query  = $this->db->get_where('user', array('username' => $username));
            $result = $query->result();

            return $result;
        }

        return false;
    }

    /**
     * Retrieves a user by id
     *
     * @param integer $userId User id
     *
     * @return mixed
     */
    public function getUserById($userId)
    {
        $query  = $this->db->get_where('user', array('ID' => $userId));
        $result = $query->result();

        return $result;
    }

    /**
     * Find email addresses for all users marked as site admins
     *
     * @return array Set of email addresses
     */
    public function getSiteAdminEmail()
    {
        $this->db->select('email')
            ->where('admin', 1);
        $q = $this->db->get('user');

        return $q->result();
    }

    /**
     * Pull a complete list of all users of the system
     *
     * @param integer $limit  Limit number of users returned
     * @param integer $offset Offset to fetch from
     *
     * @return array User details
     */
    public function getAllUsers($limit = null, $offset = null)
    {
        $this->db->order_by('username', 'asc');
        if ($limit != null) {
            $this->db->limit($limit);
        }
        if ($offset != null) {
            $this->db->offset($offset);
        }
        $q = $this->db->get('user');

        return $q->result();
    }

    /**
     * Count all users
     *
     * @return int
     */
    public function countAllUsers()
    {
        return $this->db->count_all_results('user');
    }

    /**
     * Search the user information by a string on username and full name fields
     *
     * @param string $term  string Search string
     * @param void   $start [optional] Starting point for search (not currently used)
     * @param void   $end   [optional] Ending point for search (not currently used)
     *
     * @return array
     */
    public function search($term, $start = null, $end = null)
    {
        $ci = & get_instance();
        $ci->load->model('talks_model', 'talksModel');
        $ci->load->model('user_attend_model', 'userAttend');

        $term    = mysql_real_escape_string(strtolower($term));
        $sql     = sprintf(
            "
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
        ", $term, $term
        );
        $query   = $this->db->query($sql);
        $results = $query->result();
        foreach ($results as $key => $user) {
            $results[$key]->talk_count  = count(
                $ci->talksModel->getSpeakerTalks($user->ID)
            );
            $results[$key]->event_count = count(
                $ci->userAttend->getUserAttending($user->ID)
            );
        }

        return $results;
    }

    /**
     * Attempts to find the first available username based with a basis name.
     *
     * The given username will be checked for existance; if it does a number
     * is appended and then re-checked. As long as a user exists for the
     * derived username will the number be increased until the username is
     * available.
     *
     * @param string $username Username to look up
     *
     * @return string
     */
    public function findAvailableUsername($username)
    {
        $count = '';
        while ($this->getUserByUsername($username . $count)) {
            $count++; // incrementing an empty string gives 1; thus this works.
        }

        return $username . $count;
    }
}
