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
 */
class Spam
{

    public $source = 'db';
    public $CI     = null;

    /**
     * Check if a value is spam using the appropriate function
     *
     * @param mixed  $chk    Type of check
     * @param mixed  $val    Value to check
     * @param string $source Where to get criteria from
     *
     * @return bool returns true if the value is NOT spam, i.e. the value is OK
     */
    public function check($chk, $val, $source = 'db')
    {
        $this->CI = & get_instance();
        $pass     = $this->{'chk_' . $chk}($val);

        return $pass;
    }

    /**
     * Placeholder for getting criteria from the DB
     *
     * @param string $type Type of data to get criteria for
     *
     * @return void
     */
    public function fetch_db($type)
    {

    }

    /**
     * Look for blacklist_* setting in config.php, fetch definitions 
     * from that file
     *
     * @param string $type Type of definitions to look for filename of
     *
     * @return array
     */
    public function fetch_txt($type = 'regex')
    {
        $txt_list = $this->CI->config->item('blacklist_' . $type);
        if (!$txt_list) {
            return array();
        }
        return file($txt_list);
    }

    /**
     * Placeholder function for checking substrings
     *
     * @param string $val value to check
     *
     * @return void
     */
    public function chk_substr($val)
    {

    }

    /**
     * Check a value against the regexes found in the file specified in
     * the config settings (see fetch_txt())
     *
     * @param string $val A value to check 
     *
     * @return bool returns false if anything fails, true if all good
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
