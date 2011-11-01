<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Cache {

    private $_CI	= null;
    private $_type	= 'file';
    private $_cpath	= '/tmp';
    /**
     * Our default timeout is an hour...
     */
    private $_timeout	= 3600;

    //-------------------
    public function setCachePath($path) {
    
    }
    public function setCacheTime($sec) {
    
    }
    public function getData($name) {
    $file	= $this->_cpath.'/'.$name.'.cache';
    if (is_file($file)) {
        $data=unserialize(file_get_contents($file));
        return $data;
    } else { return false; }
    }
    //-------------------
    
    /**
     * Pass in the data to be cached, it'll figure out the
     * right method to use
     */
    public function cacheData($name, $data) {
    //$this->_CI =& get_instance();
    $func='ctype_'.$this->_type;
    if (method_exists($this, $func)) {
        $this->$func($name, $data);
    } else {
        throw new Exception('Invalid cace type!');
    }
    }
    //-------------------
    /**
     * Caching to a file, assume we're always overwriting
     */
    private function ctype_file($name, $data) {
    $file	= $this->_cpath.'/'.$name.'.cache';
    $sdata	= serialize($data);
    file_put_contents($file, $sdata);
    }

    /**
     * Caching to a database
     */
    private function ctype_db($data) {
    
    }

}

?>
