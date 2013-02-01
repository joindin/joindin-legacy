<?php 
/**
 * Joindin webservice for adding comments to talks
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Joindin webservice for adding comments to talks
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Addcomment extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;

    /**
     * Instantiates the webservice to add comments to a talk
     *
     * @param string $xml XML sent into webservice
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Ensures that the user is logged in before allowing the
     * webservice to run
     *
     * @param string $xml XML sent to the webservice
     *
     * @return boolean
     */
    public function checkSecurity($xml) 
    {
        $this->CI->load->model('user_model');

        // Check to see if what they gave us is a valid login
        // Check for a valid login
        return ($this->isValidLogin($xml)) ? true : false;
    }

    /**
     * Performs the work to add a comment to a talk
     *
     * @return array
     */
    public function run() 
    {
        $this->CI->load->library('wsvalidate');

        $rules = array(
            'talk_id'    =>'required',
            'rating'    =>'required|range[1,5]',
            'comment'    =>'required',
            'private'    =>'required|range[0,1]'
        );

        $ret = $this->CI->wsvalidate
            ->validate($rules, $this->xml->action);
        $unq = $this->CI->wsvalidate
            ->validate_unique('talk_comments', $this->xml->action);

        if (!$ret && $unq) {
            $this->CI->load->model('talks_model');
            $this->CI->load->model('event_model');

            $in          = (array)$this->xml->action;
            $talk_detail = $this->CI->talks_model
                ->getTalks($in['talk_id']);
            $user        = $this->CI->user_model
                ->getUserByUsername((string)$this->xml->auth->user);

            // event ID in $talk_detial[0]->eid

            if (!$talk_detail[0]->allow_comments) {
                // we can't comment on this! same logic as fromtend
                return $this->throwError('Comments not allowed on the event/talk!');
            }

            // Ensure this is a valid talk
            if (empty($talk_detail)) {
                $ret = array(
                    'output'=>'json',
                    'data'=>array(
                        'items'=>array(
                            'msg'=>'Invalid talk ID!')
                        )
                    );
                return $this->throwError('Invalid talk ID!');
            }
            // Ensure that they can comment on it (time-based)
            if (empty($talk_detail[0]->allow_comments)) {
                return $this->throwError('Comments not allowed for this talk!');
            }
            // Ensure that speakers cannot rate their own talks
            if (isset($talk_detail[0]->uid) 
                && ($user[0]->id === $talk_detail[0]->uid) 
                && !empty($in['rating'])
            ) {
                return $this->throwError(
                    'Speakers are not allowed to rate their own talks!'
                );
            }


            $arr = array(
                'talk_id'    => $in['talk_id'],
                'rating'    => $in['rating'],
                'user_id'    => $user[0]->ID,
                'comment'    => $in['comment'],
                'date_made'    => time(),
                'private'    => $in['private'],
                'active'    => 1,
                'source'    => isset($in['source']) ? $in['source'] : 'api'
            );

            $this->CI->db->insert('talk_comments', $arr);
            return $this->throwError('Comment added!');
        } else {
            if (!$unq) {
                $ret = 'Non-unique entry!';
            }
            return $this->throwError($ret);
        }
        return $ret;
    }
}
