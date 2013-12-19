<?php
/**
 * Joind.in router
 *
 * @category Joind.in
 * @package  Libraries
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link     http://github.com/joindin/joind.in
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Joind.in router
 *
 * @category Joind.in
 * @package  Libraries
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link     http://github.com/joindin/joind.in
 */
class MY_Router extends CI_Router
{
 
    var $error_controller = 'error';
    var $error_method_404 = 'error_404';

    /**
     * Builds the object, sets up the router
     */
    public function __construct()
    {
        parent::CI_Router();
    }
 
    // this is just the same method as in Router.php, with show_404() replaced by
    // $this->error_404();
    /**
     * Validates request, handles 404 if it's no good
     *
     * @param array $segments Segments
     *
     * @return array
     */
    function _validate_request($segments)
    {
        // Does the requested controller exist in the root folder?
        if (file_exists(APPPATH.'controllers/'.$segments[0].EXT)) {
            return $segments;
        }
 
        // Is the controller in a sub-folder?
        if (is_dir(APPPATH.'controllers/'.$segments[0])) {
            // Set the directory and remove it from the segment array
            $this->set_directory($segments[0]);
            $segments = array_slice($segments, 1);
 
            if (count($segments) > 0) {
                // Does the requested controller exist in the sub-folder?
                $fileExists = file_exists(
                    APPPATH . 'controllers/' . $this->fetch_directory()
                    . $segments[0] . EXT
                );
                if (!$fileExists) {
                    return $this->error_404();
                }
            } else {
                $this->set_class($this->default_controller);
                $this->set_method('index');
 
                // Does the default controller exist in the sub-folder?
                $fileExists = file_exists(
                    APPPATH . 'controllers/' . $this->fetch_directory() .
                    $this->default_controller . EXT
                );
                if (!$fileExists) {
                    $this->directory = '';
                    return array();
                }
            }
 
            return $segments;
        }
 
        // Can't find the requested controller...
        return $this->error_404();
    }

    /**
     * Sets segments to the 404 error
     *
     * @return array
     */
    function error_404()
    {
        $segments   = array();
        $segments[] = $this->error_controller;
        $segments[] = $this->error_method_404;
        return $segments;
    }

    /**
     * Loads a class, if method doesn't exist changes to an error 404
     *
     * @return string
     */
    function fetch_class()
    {
        // if method doesn't exist in class, change
        // class to error and method to error_404
        $this->check_method();
 
        return $this->class;
    }

    /**
     * Determines if the method exists and loads if not
     *
     * @return void
     */
    function check_method()
    {
        $class = $this->class;
        if (class_exists($class)) {
            $hasMethod = in_array(
                strtolower($this->method),
                array_map('strtolower', get_class_methods($class))
            );

            if ( !$hasMethod) {
                $this->class  = $this->error_controller;
                $this->method = $this->error_method_404;
                include APPPATH . 'controllers/' . $this->fetch_directory() .
                    $this->error_controller.EXT;
            }
        }
    }	
}
 
/* End of file MY_Router.php */
/* Location: ./system/application/libraries/MY_Router.php */
