<?php
/**
 * Akismet class for helping to prevent comment spam
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
 * Akismet to help prevent comment spam
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Akismet
{
    public $key  = null;
    public $blog = null;
    public $CI   = null;

    /**
     * Instantiates the Akismet object and attaches the CodeIngiter instance
     */
    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**
     * Sends the request to Akismet
     *
     * @param string $path Path to send
     * @param array  $data Data to send
     *
     * @return mixed
     */
    public function send($path, $data)
    {
        $this->key       = $this->CI->config->item('akismet_key');
        $req_str         = '';
        $resp            = '';
        $host            = $this->key.'.rest.akismet.com';
        $port            = 80;
        $data['key']     = $this->key;
        $data['blog']    = $this->CI->config->item('akismet_blog');
        $data['user_ip'] = $_SERVER['REMOTE_ADDR'];

        foreach ($data as $k=>$v) {
            $req_str .= $k . '=' . $v . '&';
        }

        $http  = "POST ".$path." HTTP/1.0\r\n";
        $http .= "Host: ".$host."\r\n";
        $http .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http .= 'Content-length: '.strlen($req_str)."\r\n";
        $http .= "User-Agent: Joind.in/1.0\r\n";
        $http .= "\r\n";
        $http .= $req_str;

        $fp = fsockopen($host, $port, $errno, $errstr, 10);

        if ($fp) {
            fwrite($fp, $http);
            while (!feof($fp)) {
                $resp .= fgets($fp, 1024);
            }
            fclose($fp);
            $p = explode("\r\n\r\n", $resp);
            return $p[1];
        } else {
            return false;
        }
    }
}

