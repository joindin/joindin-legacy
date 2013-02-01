<?php
/**
 * Joindin validation class
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
 * Joindin validation class
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Wsvalidate
{

    // If the check is valid - i.e. the email address is good, return true

    public $CI          = null;
    public $default_err = 'Missing value: %s';
    public $errs        = array();
    public $seed        = 'th1st0k3n';

    /**
     * Instantiates the validation class
     */
    public function __construct()
    {
        $this->CI = & get_instance();
    }

    /**
     * Validates an object based on some rules
     *
     * @param array  $rules Rules to validate with
     * @param object $obj   Object to validate
     *
     * @return array|bool
     */
    public function validate($rules, $obj)
    {
        $fail = array();

        //print_r($rules); print_r($obj);
        foreach ($rules as $k => $v) {
            $m = explode('|', $v); //print_r($m);
            //echo $v.' '.$k.' '.$obj->$k."\n";
            foreach ($m as $mk => $mv) {
                //$str=(string)$obj->$k;
                //chek to see if we're anything more complex
                if (preg_match('/\[(.*?)\]/', $mv, $matches)) {
                    //print_r($matches);
                    $par  = array_merge(array($k, $obj), explode(',', $matches[1]));
                    $func = str_replace($matches[0], '', $mv);
                    $ret  = call_user_func_array(
                        array(&$this, 'validate_' . $func), $par
                    );
                } else {
                    $ret = $this->{'validate_' . $mv}($k, $obj);
                }
                if (!$ret) {
                    if ($msg = $this->getCustErr($k)) {
                        $fail[] = $msg;
                    } else {
                        $fail[] = sprintf($this->default_err, $k);
                    }
                }
            }
        }

        return (count($fail) > 0) ? $fail : false;
    }

    /**
     * Sets an error message
     *
     * @param string $k   Key for error message
     * @param string $msg Error message
     *
     * @return void
     */
    public function setCustErr($k, $msg)
    {
        $this->errs[$k] = $msg;
    }

    /**
     * Retrieves the custom error messages. Returns false if the key doesn't exist
     *
     * @param string $k Key to retrieve
     *
     * @return string|boolean
     */
    public function getCustErr($k)
    {
        return (isset($this->errs[$k])) ? $this->errs[$k] : false;
    }

    /**
     * Gets a request token from the config, echos it and ends the script
     *
     * @return string
     *
     * @todo Fix or remove
     */
    public function generateReqKey()
    {
        //print_r($_SERVER);
        $t = $this->config->item('token');
        echo $t;
        die();
        $str  = $_SERVER['SCRIPT_FILENAME'];
        $str .= $this->seed;
        $str .= $_SERVER['REQUEST_TIME'];

        return md5($str);
    }

    /**
     * Validates that a required field is set
     *
     * @param string $k   Key to check
     * @param object $obj Object to check
     *
     * @return bool
     */
    public function validate_required($k, $obj)
    {
        $str = $str = (string)$obj->$k;

        //return (!empty($str)) ? true : false; <-- used this but "false" tripped it
        return (strlen($str) > 0) ? true : false;
    }

    /**
     * Validates that a field is an email address
     *
     * @param string $k   Key to check
     * @param object $obj Object to check
     *
     * @return bool
     */
    public function validate_email($k, $obj)
    {
        $str = $str = (string)$obj->$k;

        return (filter_var($str, FILTER_VALIDATE_EMAIL)) ? true : false;
    }

    /**
     * Validates that a provided date is in the future
     *
     * @param string $k   Key that contains date
     * @param object $obj Object to check
     *
     * @return bool
     */
    public function validate_date_future($k, $obj)
    {
        $str = $str = (string)$obj->$k;

        return ($str >= time()) ? true : false;
    }

    /**
     * Does nothing. Literally it is empty
     *
     * @param string $k   Key to check
     * @param object $obj Object to check
     *
     * @return void
     */
    public function validate_int($k, $obj)
    {

    }

    /**
     * Validates that a value is within a specified range
     *
     * @param string  $k   Key to check
     * @param object  $obj Object to check
     * @param integer $min Minimum value
     * @param integer $max Maximum value
     *
     * @return bool
     */
    public function validate_range($k, $obj, $min, $max)
    {
        $this->setCustErr($k, $k . ': Number out of range!');
        $num = (float)$obj->$k;
        if (ctype_digit((string)$num)) {
            return ($num >= $min && $num <= $max) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Does nothing but return true.
     *
     * @param string $k   Key to check
     * @param object $obj Object to check
     *
     * @return bool
     */
    public function validate_reqkey($k, $obj)
    {
        return true;
    }

    /**
     * Validates that a model is unique
     *
     * @param string $tbl Table to check
     * @param object $obj Model object to check
     *
     * @return boolean
     */
    public function validate_unique($tbl, $obj)
    {
        $arr = (array)$obj;
        //if there's attributes, unset them
        if (isset($arr['@attributes'])) {
            unset($arr['@attributes']);
        }

        $mod = $tbl . '_model';
        $this->CI->load->model($mod);

        return $this->CI->$mod->isUnique($arr);
    }

    /**
     * Returns true if a user is logged in
     *
     * @return bool
     */
    public function validate_loggedin()
    {
        return ($this->CI->user_model->isAuth()) ? true : false;
    }

    /**
     * Validates that an object represents an event
     *
     * @param string $k   Key to check
     * @param object $obj Object to check
     *
     * @return bool
     */
    public function validate_isevent($k, $obj)
    {
        $eid = ($obj->eid) ? $obj->eid : $obj->event_id;
        $this->CI->load->model('event_model');
        $ret = $this->CI->event_model->getEventDetail($eid);

        return (!empty($ret)) ? true : false;
    }

    /**
     * Validates that an object represents a talk
     *
     * @param string $k   Key to check
     * @param object $obj Object to check
     *
     * @return bool
     */
    public function validate_istalk($k, $obj)
    {
        $this->CI->load->model('talks_model');
        $ret = $this->CI->talks_model->getTalks($obj->talk_id);

        return (!empty($ret)) ? true : false;
    }

}
