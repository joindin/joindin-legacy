<?php
/**
 * Webservice for getting comment details
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
 * Webservice for getting comment details
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 *
 * @todo make this work with any given type - blog, event, talk
 */
class Getdetail extends BaseWsRequest
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
     * Runs the webservice to get details for a comment
     *
     * @return array
     */
    public function run()
    {
        $id   = $this->xml->action->cid;
        $type = $this->xml->action->rtype;

        //getTalkComments
        $ret = array();
        if ($this->xml->action->rtype == 'talk') {
            $this->CI->load->model('talk_comments_model');
            $ret = $this->CI->talk_comments_model->getCommentDetail($id);
        } elseif ($this->xml->action->rtype == 'event') {
            $this->CI->load->model('event_comments_model');
            $ret = $this->CI->event_comments_model->getCommentDetail($id);
        }
        if (count($ret) > 0) {
            $ret = array(
                'output' => 'json',
                'data' => array('items' => $ret)
            );
        } else {
            $ret = array(
                'output' => 'json',
                'data' => array(
                    'items' => array(
                        'msg' => 'Comment not found!')
                )
            );
        }
        return $ret;
    }
}
