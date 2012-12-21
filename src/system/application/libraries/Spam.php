<?php
/**
 * Spam Class
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
 * Spam Class
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 *
 * @todo      Determine if this is even used at all
 */
class Spam
{

    public $source = 'db';
    public $CI     = null;

    /**
     * Does something. Not sure what.
     *
     * @param mixed  $chk    A parameter
     * @param mixed  $val    Another parameter
     * @param string $source Something else
     *
     * @return bool
     */
    public function check($chk, $val, $source = 'db')
    {
        $this->CI = & get_instance();
        $pass     = true;
        if (is_array($chk)) {
            foreach ($chk as $k => $v) {
                $ret = $this->{'chk_' . $v}($val);
            }
        } else {
            $pass = $this->{'chk_' . $chk}($val);
        }

        return $pass;
    }

    /**
     * Does nothing. Empty
     *
     * @param null $type Not used
     *
     * @return void
     */
    public function fetch_db($type)
    {

    }

    /**
     * Does something and then reads from a file
     *
     * @param string $type Type of thing for doing something
     *
     * @return array
     */
    public function fetch_txt($type = 'regex')
    {
        $txt_list = $this->CI->config->item('blacklist_' . $type);

        return file($txt_list);
    }

    /**
     * Does nothing
     *
     * @param null $val Does nothing
     *
     * @return void
     */
    public function chk_substr($val)
    {

    }

    /**
     * Checks a value of something with a regex from a place
     *
     * @param string $val A value to check for something
     *
     * @return bool
     */
    public function chk_regex($val)
    {
        $pass = true;
        $rows = $this->fetch_txt('regex');
        foreach ($rows as $k => $v) {
            $m = preg_match($v, $val);
            if ($m) {
                return false;
            }
        }

        return true;
    }

}
