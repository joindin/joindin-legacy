<?php 
/**
 * Defensio class
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed'); 
}

/**
 * Defensio functionality for protecting against malicious content within
 * comments and other user provided data
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */
class Defensio
{
    public $http  = 'http://api.defensio.com';
    public $key   = null;
    public $owner = null;
    public $CI    = null;
    
    /**
     * Creates the Defensio object
     */
    public function __construct()
    {
        $this->CI = &get_instance();
    }
    
    /**
     * Provides connection to service which audits comments for malicious
     * content.
     *
     * @param string $name    Comment author
     * @param string $comment Content of comment for checking
     * @param string $trust   Not used
     * @param string $url     URL for permalink when combined with owner
     *
     * @return mixed
     */
    public function check($name, $comment, $trust, $url)
    {
        $this->key   = $this->CI->config->item('defensio_key');
        $this->owner = $this->CI->config->item('defensio_owner');
        $resp        = '';
        $loc         = '/app/1.2/audit-comment/' . $this->key . '.xml';
        $arr         = array(
            'user-ip'         => $_SERVER['REMOTE_ADDR'],
            'owner-url'       => $this->owner,
            'article-date'    => date('Y/m/d'),
            'comment-author'  => $name,
            'comment-type'    => 'comment',
            'comment-content' => $comment,
            'permalink'       => $this->owner.$url
        );
        if (isset($_SERVER['HTTP_REFERER'])) {
            $arr['referrer'] = $_SERVER['HTTP_REFERER'];
        }
        $msg = '';
        foreach ($arr as $k=>$v) {
            $msg .= $k . '=' . urlencode($v) . '&';
        }
        $str  = "POST ".$loc." HTTP/1.0\r\n";
        $str .= "Host: api.defensio.com\r\n";
        $str .= "Content-type: application/x-www-form-urlencoded\r\n";
        $str .= "Content-length: ".strlen($msg)."\r\n";
        $str .= "Connection: close\r\n";
        $str .= "\r\n";
        $str .= $msg;

        $fp = fsockopen('api.defensio.com', 80, $errno, $errstr);
        if ($fp) {
            fwrite($fp, $str);
            while (!feof($fp)) {
                $resp .= fread($fp, 1024);
            }
            fclose($fp);
        }
        if ($resp) {
            error_log($resp);
            $p   = explode("\r\n\r\n", $resp);
            $xml = simplexml_load_string($p[1]);
            //echo 'response: <pre>'; print_r($xml); echo '</pre>';
            return $xml;
        } else {
            return false;
        }
    }
}
