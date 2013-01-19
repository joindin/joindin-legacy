<?php
/**
 * Webservice for deleting comments on the blog
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
 * Webservice for deleting comments on the blog
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Deletecomment extends BaseWsRequest
{
    protected $CI  = null;
    protected $xml = null;

    /**
     * Instantiates the webservice
     *
     * @param string $xml The XML sent to the service
     */
    public function __construct($xml)
    {
        $this->CI  = & get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Check to ensure that they:
     * - Passed in the valid login credentials
     * - They're for a valid login
     * - They're a site admin
     *
     * @param string $xml XML string passed to service
     *
     * @return boolean
     */
    public function checkSecurity($xml)
    {
        $this->CI->load->model('user_model');

        // Check for a valid login
        //if ($this->isValidLogin($xml)) {
        if ($this->CI->user_model->isAuth() && $this->checkPublicKey()) {
            // Be sure they gave us the blog entry ID and comment ID
            if (!isset($xml->action->bid) || !isset($xml->action->cid)) {
                return false;
            }
            $user = $this->CI->session->userdata('username');

            // Now check to see if they're a site admin
            if (!$this->CI->user_model->isSiteAdmin($user)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Runs the service to delete a blog comment
     *
     * @return array
     */
    public function run()
    {
        $this->CI->load->library('wsvalidate');
        $this->CI->load->model('blog_comments_model', 'bcm');

        $com_id = $this->xml->action->cid;
        $this->CI->bcm->deleteComment($com_id);

        return array(
            'output' => 'json',
            'data' => array(
                'items' => array(
                    'msg' => 'Success'
                )
            )
        );
    }

}
