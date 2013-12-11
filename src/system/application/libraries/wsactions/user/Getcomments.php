<?php 
/**
 * Joindin webservice for fetching comments
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
 * Joindin webservice for fetching comments
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Getcomments extends BaseWsRequest
{
    public $CI  = null;
    public $xml = null;

    /**
     * Instantiates the webservice to retrieve comments. Sets the
     * xml that is provided and loads the CodeIngiter instance
     *
     * @param string $xml XML to set
     */
    public function __construct($xml) 
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
    * Just be sure they've given us a valid login
    *
    * Does nothing. Returns true
    *
    * @param mixed $xml Not used
    *
    * @return true
    */
    public function checkSecurity($xml) 
    {
        // public function
        return true;
    }
    
    /**
     * Runs the web service to fetch the comments
     *
     * @return array
     */
    public function run() 
    {
        // Get the comments the user has put on events and talks
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('user_model');
        $this->CI->load->model('talk_comments_model');
        $this->CI->load->model('event_comments_model');
        
        // JOINDIN-139 - Empty username will result in an empty
        // comment structure returned instead of an error.
        if ($this->xml->action->username == '') {
            return array(
                'type' => 'json',
                'data' => array(
                    'items' => array(),
                    'user'  => $this->xml->action->username
                )
            );
        }

        $rules = array(
            'username'    =>'required'
        );
        $ret   = $this->CI->wsvalidate->validate($rules, $this->xml->action);

        if (!$ret) {
            // We're good - get the user's information
            $comments = array();
            $restrict = (isset($this->xml->action->type)) ?
                strtolower($this->xml->action->type) : false;
            $udata    = $this->CI
                ->user_model
                ->getUserByUsername((string)$this->xml->action->username);

            if (empty($udata)) {
                return array(
                    'output'=>'json',
                    'data'=>array(
                        'items'=>array(
                            'msg'=>'Invalid username!')
                        )
                    );
            }

            if (!$restrict || $restrict=='talk') {
                // First, the talk comments...
                $uc_talk = $this->CI
                    ->talk_comments_model->getUserComments($udata[0]->ID);
                foreach ($uc_talk as $k=>$v) {
                    // We're just going to remove private comments for now
                    if ($v->private==1) {
                        continue;
                    }
                    $v->type    = 'talk';
                    $comments[] = $v;
                }
            }
            if (!$restrict || $restrict=='event') {
                // Now the event comments
                $uc_event = $this->CI
                    ->event_comments_model->getUserComments($udata[0]->ID);
                foreach ($uc_event as $k=>$v) {
                    $v->type    = 'event'; 
                    $comments[] = $v;
                }
            }
            
            return array('output'=>'json','data'=>array('items'=>$comments));
        } else {
            return array(
                'output'=>'json',
                'data'=>array(
                    'items'=>array(
                        'msg'=>'Required fields missing!')
                    )
                );
        }
    }
}

