<?php
/**
 * Caching class. It doesn't appear to be used and is only partially 
 * implemented. Can we delete it?
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
 * Caching class. Seemingly unused.
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Cache
{

    private $_CI    = null;
    private $_type  = 'file';
    private $_cpath = '/tmp';

    /**
     * Our default timeout is an hour...
     *
     * @var int
     */
    private $_timeout = 3600;

    /**
     * Does nothing
     *
     * @param mixed $path Not used
     *
     * @return null
     */
    public function setCachePath($path) 
    {

    }

    /**
     * Does nothing
     *
     * @param mixed $sec Does nothing
     *
     * @return null
     */
    public function setCacheTime($sec) 
    {

    }

    /**
     * Retrieve data from the cache file
     *
     * @param string $name Name of cache file
     *
     * @return mixed
     */
    public function getData($name) 
    {
        $file = $this->_cpath . '/' . $name . '.cache';

        if (is_file($file)) {
            $data = unserialize(file_get_contents($file));
            return $data;
        } else { 
            return false; 
        }
    }

    /**
     * Pass in the data to be cached, it'll figure out the
     * right method to use
     *
     * @param string $name Cache key
     * @param mixed  $data Data to store
     *
     * @return null
     */
    public function cacheData($name, $data) 
    {
        $func = 'ctype_'.$this->_type;
        if (method_exists($this, $func)) {
            $this->$func($name, $data);
        } else {
            throw new Exception('Invalid cache type!');
        }
    }

    /**
     * Caching to a file, assume we're always overwriting
     *
     * @param string $name Name of cache key
     * @param mixed  $data Data to cache
     *
     * @return null
     */
    private function ctype_file($name, $data) 
    {
        $file  = $this->_cpath . '/' . $name . '.cache';
        $sdata = serialize($data);
        file_put_contents($file, $sdata);
    }

    /**
     * Does nothing
     *
     * @param mixed $data Does nothing
     *
     * @return null
     */
    private function ctype_db($data)
    {

    }
}

