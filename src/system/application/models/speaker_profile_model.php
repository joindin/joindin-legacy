<?php
/**
 * Speaker profile model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Speaker profile model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Speaker_profile_model extends Model
{
    /**
     * Check to see if a token belongs to a user
     *
     * @param integer $uid User ID
     * @param integer $tid Token ID
     *
     * @return boolean
     */
    public function isUserToken($uid, $tid)
    {
        $sql = sprintf(
            "
            select
                speaker_profile.ID
            from
                speaker_profile,
                speaker_tokens
            where
                user_id=%s and
                speaker_profile_id=speaker_profile.ID and
                speaker_tokens.ID=%s	
        ", $this->db->escape($uid), $this->db->escape($tid)
        );
        $q   = $this->db->query($sql);
        $ret = $q->result();

        return (isset($ret[0]->ID)) ? true : false;
    }

    /**
     * Fetch the profile information for the given user ID
     *
     * @param integer $uid User id
     *
     * @return mixed
     */
    public function getProfile($uid)
    {
        $this->db->select('speaker_profile.*, countries.name AS country');
        $this->db->from('speaker_profile');
        $this->db->join(
            'countries',
            'countries.ID=speaker_profile.country_id',
            'left'
        );
        $this->db->where('user_id', $uid);
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Retrieves a profile by id
     *
     * @param integer $pid Profile id
     *
     * @return mixed
     */
    public function getProfileById($pid)
    {
        $q = $this->db->get_where('speaker_profile', array('ID' => $pid));

        return $q->result();
    }

    /**
     * Set up a new speaker profile
     *
     * @param array $data Speaker profile information
     *
     * @return void
     */
    public function setProfile($data)
    {
        $this->db->insert('speaker_profile', $data);
    }

    /**
     * Given a user ID and key/value, update the user's profile
     *
     * @param integer $uid  User id
     * @param array   $data Date to update
     *
     * @return void
     */
    public function updateProfile($uid, $data)
    {
        $this->db->where('user_id', $uid);
        $this->db->update('speaker_profile', $data);
    }

    /**
     * Get the column names for the types of the speaker profile
     *
     * @return array
     */
    public function getProfileFields()
    {
        $fields = array();
        $q      = $this->db->query('show columns from speaker_profile');
        foreach ($q->result() as $k => $v) {
            if ($v->Field != 'ID') {
                $fields[] = $v->Field;
            }
        }

        return $fields;
    }

    /**
     * Get the details from the speaker's profile based on what the token defines
     *
     * @param string $token Token name
     *
     * @return array
     */
    public function getDetailByToken($token)
    {
        $tok_detail = $this->getTokenDetail($token);

        // Get the fields they're allowing for this token
        $fields = $this->getTokenAccess($tok_detail[0]->ID);

        // And get the user's profile...
        $profile = $this->getProfileById($tok_detail[0]->speaker_profile_id);
        $profile = $profile[0];

        $details = array();
        foreach ($fields as $f) {
            $name = $f->field_name;
            if (isset($profile->$name) && !empty($profile->$name)) {
                $details[$name] = $profile->$name;
            }
        }

        return $details;
    }

    /**
     * Get the full access (all tokens/all fields) information for a profile
     *
     * @param integer $pid Profile ID
     * @param integer $tid [optional] Token ID
     *
     * @return mixed
     */
    public function getProfileAccess($pid, $tid = null)
    {
        $data          = array();
        $q             = $this->db->get_where(
            'speaker_tokens',
            array('speaker_profile_id' => $pid)
        );
        $data['token'] = $q->result();

        if (isset($data['token'][0])) {
            $data['fields'] = $this->getTokenAccess($data['token'][0]->ID);
        }

        return (empty($data)) ? false : $data;
    }

    /**
     * Based on a user ID, get the token information for the user's profile
     *
     * @param integer $uid User ID
     *
     * @return mixed
     */
    public function getUserProfileAccess($uid)
    {
        $profile = $this->getProfile($uid);

        return $this->getProfileTokens($profile[0]->ID);
    }

    /**
     * Retrieves user's public profile
     *
     * @param integer $uid    User id
     * @param boolean $public Retrieve public information
     *
     * @return mixed
     */
    public function getUserPublicProfile($uid, $public = false)
    {
        $profile = $this->getProfile($uid);
        if (!isset($profile[0])) { /* no profile! */
            return array();
        }
        $access = $this->getProfileTokens($profile[0]->ID);
        if ($public === true) {
            foreach ($access as $a) {
                if ($a->is_public == 'Y') {
                    // here's the tokens they have access to
                    $ret  = $this->getTokenAccess($a->ID);
                    $data = array();
                    foreach ($ret as $k => $v) {
                        $field        = $v->field_name;
                        $ret[$k]->val = $profile[0]->$field;
                        $data[$field] = $profile[0]->$field;
                    }

                    return array('token' => $a, 'access' => $ret, 'data' => $data);
                }
            }
        }

        return $access;
    }

    /**
     * Based on the token ID, gets the fields that it has access to
     *
     * @param integer $tid Token ID
     *
     * @return mixed
     */
    public function getTokenAccess($tid)
    {
        $q = $this->db->get_where(
            'speaker_token_fields',
            array('speaker_token_id' => $tid)
        );

        return $q->result();
    }

    /**
     * Given the profile ID, get the tokens related to the profile
     *
     * @param integer $pid Token ID
     *
     * @return mixed
     */
    public function getProfileTokens($pid)
    {
        $q = $this->db->get_where(
            'speaker_tokens',
            array('speaker_profile_id' => $pid)
        );

        return $q->result();
    }

    /**
     * Based on a token name, Get the detail from the tokens table
     *
     * @param string $token Token name
     *
     * @return mixed
     */
    public function getTokenDetail($token)
    {
        $q = $this->db->get_where(
            'speaker_tokens',
            array('access_token' => $token)
        );

        return $q->result();
    }

    /**
     * Used to set up a token and the field access that goes along with it
     *
     * @param integer $uid       User ID
     * @param string  $name      Token name (user defined)
     * @param string  $desc      Description
     * @param array   $fields    List of fields
     * @param boolean $is_public If access is public
     *
     * @return boolean
     */
    public function setProfileAccess($uid, $name, $desc, $fields, $is_public = null)
    {
        //First, insert into the token table...
        $profile = $this->getProfile($uid);
        $pid     = $profile[0]->ID;
        $arr     = array(
            'speaker_profile_id' => $pid,
            'access_token'       => $name,
            'description'        => $desc,
            'is_public'          => ($is_public !== null) ? 'Y' : null,
            'created'            => time()
        );

        //Be sure we don't already have profile access like this
        $tokens = $this->getProfileTokens($pid);
        foreach ($tokens as $t) {
            if ($t->access_token == $name) {
                return false;
            }
        }

        //Keep going and do the insert...
        $this->db->insert('speaker_tokens', $arr);
        $tid = $this->db->insert_id();

        if ($is_public !== null) {
            $this->setProfileViewable($uid, $tid, $is_public);
        }

        //Now, for each of the fields they gave us, put its name in the fields table
        foreach ($fields as $f) {
            $arr = array('speaker_token_id' => $tid, 'field_name' => $f);
            $this->db->insert('speaker_token_fields', $arr);
        }

        return true;
    }

    /**
     * Update the token's access fields
     *
     * @param integer $uid       User ID
     * @param integer $tid       Token ID
     * @param arrray  $fields    List of fields
     * @param boolean $is_public If profile is public
     *
     * @return boolean
     */
    public function updateProfileAccess($uid, $tid, $fields, $is_public = null)
    {
        // Be sure we're supposed to work on this token
        if (!$this->isUserToken($uid, $tid)) {
            return false;
        }

        // drop all of the token access fields for the token...
        $this->db->where('speaker_token_id', $tid);
        $this->db->delete('speaker_token_fields');

        if ($is_public !== null) {
            $this->setProfileViewable($uid, $tid, $is_public);
        }

        // Now add in our new ones
        foreach ($fields as $f) {
            $arr = array('speaker_token_id' => $tid, 'field_name' => $f);
            $this->db->insert('speaker_token_fields', $arr);
        }

        return true;
    }

    /**
     * Remove the access based on a token ID
     *
     * @param integer $uid User ID
     * @param integer $tid Token ID
     *
     * @return void
     */
    public function deleteProfileAccess($uid, $tid)
    {
        // Be sure it's theirs first...
        $profile = $this->getProfile($uid); //print_r($profile);

        $tokens = $this->getProfileTokens($profile[0]->ID); //print_r($tokens);
        foreach ($tokens as $t) {
            if ($t->ID == $tid) {
                $this->db->where('ID', $tid);
                $this->db->delete('speaker_tokens');
                $this->db->where('speaker_token_id', $tid);
                $this->db->delete('speaker_token_fields');
            }
        }
    }

    /**
     * Set the selected access profile's viewable status
     *
     * @param integer $uid       User ID
     * @param integer $tid       Token ID
     * @param boolean $is_public [optional] Viewable status
     *
     * @return void
     */
    public function setProfileViewable($uid, $tid, $is_public = null)
    {
        //first we need to set all of the current ones to non-public
        $sql = sprintf(
            '
            select
                st.ID
            from
                speaker_profile sp,
                speaker_tokens st
            where
                sp.user_id=%s and
                sp.ID=st.speaker_profile_id
        ', $this->db->escape($uid)
        );
        $q   = $this->db->query($sql);
        foreach ($q->result() as $token) {
            $this->db->where('ID', $token->ID);
            $this->db->update('speaker_tokens', array('is_public' => null));
        }
        // Now we can just set the one we need
        $this->db->where('ID', $tid);
        $this->db->update('speaker_tokens', array('is_public' => $is_public));
    }
}

