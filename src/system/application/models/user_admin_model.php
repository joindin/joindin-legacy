<?php
/**
 * User admin model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * User admin model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

class User_admin_model extends Model
{
    /**
     * Remove a specific permission row
     *
     * @param integer $aid Resource ID
     *
     * @return void
     */
    public function removePerm($aid)
    {
        //$arr=array('uid'=>$uid,'rid'=>$rid);
        $this->db->delete('user_admin', array('ID' => $aid));
    }

    /**
     * Remove permission for a user on a resource
     *
     * @param integer $uid  User ID
     * @param integer $rid  Resource ID
     * @param string  $type Resource type (ex. "talk")
     *
     * @return void
     */
    public function removeRidPerm($uid, $rid, $type)
    {
        $det = array(
            'rid'   => $rid,
            'uid'   => $uid,
            'rtype' => $type
        );
        $this->db->delete('user_admin', $det);
    }

    /**
     * Add permissions for a user to a resource
     *
     * @param integer $uid  User ID
     * @param integer $rid  Resource ID
     * @param string  $type Resource type (ex. "talk")
     *
     * @return void
     */
    public function addPerm($uid, $rid, $type)
    {
        error_log($uid . '-' . $rid . '-' . $type);
        $arr = array(
            'uid'   => $uid,
            'rid'   => $rid,
            'rtype' => $type,
            'rcode' => ''
        );
        $this->db->insert('user_admin', $arr);
    }

    /**
     * Update the permissions in the table based on the table ID
     *
     * @param integer $id    Table ID
     * @param array   $perms Permission settings to change
     *
     * @return void
     */
    public function updatePerm($id, $perms)
    {
        $this->db->where('id', $id);
        $this->db->update('user_admin', $perms);
    }

    /**
     * Check to see if given user has a claim on the ID+type combo
     *
     * @param integer $uid   User ID
     * @param integer $rid   Resource ID (ex. talk ID)
     * @param string  $rtype Resource type (ex. "talk")
     *
     * @return boolean If they have permission or not
     */
    public function hasPerm($uid, $rid, $rtype)
    {
        $q   = $this->db->get_where(
            'user_admin',
            array('uid' => $uid, 'rid' => $rid, 'rtype' => $rtype)
        );
        $ret = $q->result(); //print_r($ret);
        return (empty($ret)) ? false : true;
    }

    /**
     * Retrieves pending permissions
     *
     * @param integer $uid   User id
     * @param integer $rid   Resource id
     * @param string  $rtype Resource type
     *
     * @return mixed
     */
    public function getPendingPerm($uid, $rid, $rtype)
    {
        error_log($uid . ' - ' . $rid . ' - ' . $rtype);
        $q      = $this->db->get_where(
            'user_admin',
            array(
                 'uid'   => $uid,
                 'rid'   => $rid,
                 'rtype' => $rtype,
                 'rcode' => 'pending'
            )
        );
        $result = $q->result();
        error_log('result: ' . print_r($result, true));

        return $result;
    }

    /**
     * Check to see if given permission ID is of rtype/rid type.
     *
     * @param integer $id    ID
     * @param integer $rid   Resource ID (ex talk ID)
     * @param string  $rtype Resource type (ex. talk)
     *
     * @return boolean If they have permission or not
     */
    public function checkPerm($id, $rid, $rtype)
    {
        $q   = $this->db->get_where(
            'user_admin',
            array('ID' => $id, 'rid' => $rid, 'rtype' => $rtype)
        );
        $ret = $q->result();

        return (empty($ret)) ? false : true;
    }

    /**
     * Get detail for a given user - their talks and events
     *
     * @param integer $uid     User ID
     * @param array   $types   [optional] Admin types (talk, event, etc)
     * @param boolean $pending Toggle to show pending claims or not
     *
     * @return array $ret User claim information
     */
    public function getUserTypes($uid, $types = null, $pending = false)
    {
        $CI =& get_instance();

        $CI->load->model('talks_model');
        $CI->load->model('event_model');

        $tadd = ($types) ?
            " and ua.rtype in ('" . implode("','", $types) . "')" : '';
        $pend = ($pending) ? " and rcode='pending'" : '';
        $sql  = sprintf(
            "
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
        ", $this->db->escape($uid), $pend, $tadd
        );
        $q    = $this->db->query($sql);
        $ret  = $q->result();

        foreach ($ret as $k => $v) {
            switch ($v->rtype) {
            case 'talk':
                $ret[$k]->detail = $CI->talks_model->getTalks($v->rid);
                break;
            case 'event':
                $ret[$k]->detail = $CI->event_model->getEventDetail($v->rid);
                break;
            }
        }

        return $ret;
    }

    /**
     * Get the event details of the events the user is an admin on
     *
     * @param integer $uid User ID
     *
     * @return array User admin data
     */
    public function getUserEventAdmin($uid)
    {
        $sql = sprintf(
            "
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
        ", $this->db->escape($uid)
        );
        $q   = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Find the claims for a given talk ID
     *
     * @param integer $talk_id Talk ID #
     * @param boolean $pending [optional] Whether to include pending claims or not
     *
     * @return mixed
     */
    public function getTalkClaims($talk_id, $pending = false)
    {
        $this->db->select('*');
        $this->db->from('user_admin');
        $this->db->join('user', 'user_admin.uid=user.ID');
        $this->db->where('rid', $talk_id);
        $this->db->where('rtype', 'talk');
        if (!$pending) {
            $this->db->where(array('rcode !=' => 'pending'));
        }

        $q   = $this->db->get();
        $ret = $q->result();

        return $ret;
    }

    /**
     * Given the ID from the user_admin table, check to see if the
     * claim is valid & pending
     *
     * @param integer $claim_id ID from the claim table
     *
     * @return boolean Is valid claim or not
     */
    public function isPendingClaim($claim_id)
    {
        $q   = $this->db->get_where(
            'user_admin',
            array('ID' => $claim_id, 'rcode' => 'pending')
        );
        $ret = $q->result();

        return (empty($ret)) ? false : true;
    }

    /**
     * Get the pending claims for a talk
     *
     * @param string  $type Type to get claims for
     * @param integer $rid  [optional] Talk ID
     *
     * @return array Claim data
     */
    public function getPendingClaims($type = 'talk', $rid = null)
    {
        return $this->getPendingClaim_TalkSpeaker($rid);
    }

    /**
     * Get the pending talk clams for the event
     *
     * @param integer $eid [optional] Event Id
     *
     * @return mixed
     */
    public function getPendingClaim_TalkSpeaker($eid = null)
    {
        $CI =& get_instance();
        $CI->load->model('talk_speaker_model', 'talkSpeaker');

        $sql = sprintf(
            "
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
        ", $eid
        );

        $query   = $this->db->query($sql);
        $results = $query->result();

        foreach ($results as $talkKey => $talk) {
            $results[$talkKey]->speakers = $CI->talkSpeaker
                ->getSpeakerByTalkId($talk->talk_id);
        }

        return $results;
    }

    /**
     * Get the pending talk claims
     *
     * @param integer $eid [optional] integer Event ID to restrict on
     *
     * @return mixed
     */
    public function getPendingClaims_Talks($eid = null)
    {
        $addl = ($eid) ? ' e.ID=' . $this->db->escape($eid) . ' and ' : '';
        $sql  = sprintf(
            "
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
        ", $addl
        );
        $q    = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Check that the API key actually exists and is valid
     *
     * @param string $key      the value of incoming api key
     * @param string $callback Callback
     *
     * @return boolean true if it exists, false otherwise
     */
    public function oauthVerifyApiKey($key, $callback)
    {
        $sql   = 'SELECT application FROM oauth_consumers
            WHERE consumer_key = ' . $this->db->escape($key)
            . ' AND callback_url = ' . $this->db->escape($callback);
        $query = $this->db->query($sql);

        $result = $query->result();
        if (count($result) > 0) {
            return true;
        }

        return false;
    }

    /**
     * This user granted access for this application using this request
     * token, record this and give a verification token
     *
     * @param string  $api_key The request token the user is authorising
     * @param integer $user_id The user's database ID (comes from the session
     *                         when called from the webcontroller)
     *
     * @access public
     * @return array containing verification code and callback url,
     * or false if something went wrong
     */
    public function oauthAllow($api_key, $user_id)
    {
        $access = $this->newAccessToken($api_key, $user_id);

        return $access;
    }

    /**
     * oauthGenerateConsumerCredentials
     *
     * @param int    $user_id      The user that requested the credentials
     * @param string $application  The display name of the application
     * @param string $description  What the app does (not displayed)
     * @param string $callback_url Callback URL
     *
     * @return boolean
     */
    public function oauthGenerateConsumerCredentials(
        $user_id,
        $application,
        $description,
        $callback_url
    ) {
        // The first 30 bytes should be plenty for the consumer_key
        // We use the last 10 for the shared secret
        $hash = $this->generateToken();
        $key  = array(substr($hash, 0, 30), substr($hash, 30, 10));

        $sql = "INSERT INTO oauth_consumers SET user_id = "
            . $this->db->escape($user_id) . ",
            application = " . $this->db->escape($application) . ", 
            description = " . $this->db->escape($description) . ", 
            callback_url = " . $this->db->escape($callback_url) . ", 
            consumer_key = " . $this->db->escape($key[0]) . ", 
            consumer_secret = " . $this->db->escape($key[1]);

        $this->db->query($sql);

        return true;
    }

    /**
     * oauthGetConsumerKeysByUser
     *
     * @param int $user_id The ID of the user whose keys we want
     *
     * @return array The application name, key and secret for each key
     * associated with this user id
     */
    public function oauthGetConsumerKeysByUser($user_id)
    {
        $sql = "SELECT id, application, callback_url, consumer_key, consumer_secret"
            . " FROM oauth_consumers WHERE user_id = "
            . $this->db->escape($user_id);

        $query  = $this->db->query($sql);
        $result = $query->result();

        return $result;
    }

    /**
     * Generate, store and return a new access token to the user
     *
     * @param string $api_key the identifier for the consumer
     * @param int    $user_id the user granting access
     *
     * @return string access token
     */
    public function newAccessToken($api_key, $user_id)
    {
        $hash                = $this->generateToken();
        $access_token        = substr($hash, 0, 16);
        $access_token_secret = substr($hash, 16, 16);

        // store new token
        $sql   = 'insert into oauth_access_tokens set '
            . 'access_token = ' . $this->db->escape($access_token) . ','
            . 'access_token_secret = ' . $this->db->escape($access_token_secret) .
            ','
            . 'consumer_key = ' . $this->db->escape($api_key) . ', '
            . 'user_id = ' . $this->db->escape($user_id);
        $query = $this->db->query($sql);
        if ($query) {
            return $access_token;
        }

        return false;
    }

    /**
     * generateToken
     *
     * taken mostly from
     * http://toys.lerdorf.com/archives/55-Writing-an-OAuth-Provider-Service.html
     *
     * @return string
     */
    public function generateToken()
    {
        $fp      = fopen('/dev/urandom', 'rb');
        $entropy = fread($fp, 32);
        fclose($fp);
        // in case /dev/urandom is reusing entropy from its pool,
        // let's add a bit more entropy
        $entropy .= uniqid(mt_rand(), true);
        $hash     = sha1($entropy); // sha1 gives us a 40-byte hash
        return $hash;
    }

    /**
     * deleteApiKey remove an API key for this user with this ID
     *
     * @param integer $user_id User id
     * @param integer $api_id  API id
     *
     * @return boolean whether the query was successful
     */
    public function deleteApiKey($user_id, $api_id)
    {
        $id = (int)$api_id;

        $sql = 'delete from oauth_consumers
            where user_id=' . $this->db->escape($user_id) . '
            and id=' . $this->db->escape($id);

        return $this->db->query($sql);
    }

    /**
     * Show all the access tokens currently available for this user
     *
     * @param int $user_id User id
     *
     * @return an array of all the keys and which consumers they relate to
     */
    public function oauthGetAccessKeysByUser($user_id)
    {
        $sql = "SELECT t.id, t.consumer_key, t.access_token, t.last_used_date,
            c.application 
            FROM oauth_access_tokens t
            INNER JOIN oauth_consumers c USING (consumer_key)
            WHERE t.user_id = "
            . $this->db->escape($user_id);

        $query  = $this->db->query($sql);
        $result = $query->result();

        return $result;
    }

    /**
     * Deletes an access token from the database
     *
     * @param integer $user_id User id
     * @param integer $api_id  API id
     *
     * @return mixed
     */
    public function deleteAccessToken($user_id, $api_id)
    {
        $id = (int)$api_id;

        $sql = 'delete from oauth_access_tokens
            where user_id=' . $this->db->escape($user_id) . '
            and id=' . $this->db->escape($id);

        return $this->db->query($sql);
    }


}
