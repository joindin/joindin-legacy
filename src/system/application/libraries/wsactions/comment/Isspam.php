<?php
/**
 * Webservice for informing administrators that a comment is
 * probably spam
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
 * Webservice for informing administrators that a comment is
 * probably spam
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Isspam extends BaseWsRequest
{

    public $CI  = null;
    public $xml = null;

    /**
     * Instantiates the web service
     *
     * @param string $xml XML sent to service
     */
    public function __construct($xml)
    {
        $this->CI  = &get_instance(); //print_r($this->CI);
        $this->xml = $xml;
    }

    /**
     * Public method. Returns true.
     *
     * @param string $xml XML sent to service
     *
     * @return boolean
     */
    public function checkSecurity($xml)
    {
        // public method!
        return true;
    }

    /**
     * Sends emails to administrators to let them know that a comment
     * may be spam.
     *
     * @return array
     */
    public function run()
    {
        $this->CI->load->model('user_model');

        $cid   = $this->xml->action->cid;
        $rtype = $this->xml->action->rtype;
        $tid   = $this->xml->action->tid;

        $msg = 'Spam comment on : ' . $this->CI->config->site_url() .
            $rtype . '/view/' . $tid . "#comment-" . $cid;

        $admin_emails = $this->CI->user_model->getSiteAdminEmail();
        foreach ($admin_emails as $user) {
            mail(
                $user->email,
                'Suggested spam comment!',
                $msg,
                'From: ' . $this->CI->config->item('email_info')
            );
        }

        return array(
            'output' => 'json',
            'data' => array(
                'items' => array('msg' => 'Success')
            )
        );
    }
}
