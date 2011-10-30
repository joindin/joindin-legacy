<?php

class SSL {
    
    /**
     * Array listing of secure URLs
     */
    private $_secure_url	= array(
        "/user/login"
    );
    /**
     * CodeIgniter instance
     */
    private $ci				= null;
    
    public function __construct() {
        $this->ci=&get_instance();
    }
    
    /**
     * Main routing function
     * @param string $path User-defined path
     */
    public function sslRoute($path='') {
        // Check to see if the "USE_SSL" is in config
        $use_ssl=$this->ci->config->item('use_ssl');
        if (!$use_ssl) return;
        
        if (empty($path)) { $path=$_SERVER['REQUEST_URI']; }
        if ($this->isSecure($path) && !$this->isRequestSecure()) {
            header('Location: '.$this->buildRedirect($path));
        }
    }
    
    /**
     * Check to see if the given path is secure
     * @param string $path User-defined path
     */
    private function isSecure($path) {
        return (in_array($path, $this->_secure_url)) ? true : false;
    }
    
    /**
     * Check to see if the current request is on HTTPS
     */
    private function isRequestSecure() {
        return (isset($_SERVER['SECURE']) && $_SERVER['SECURE']==1) ? true : false;
    }
    
    /**
     * Make our redirect link to the other side...
     * @param string $path
     */
    private function buildRedirect($path='') {
        if (empty($path)) { $path=$_SERVER['REQUEST_URI']; }
        $base_url=$this->ci->config->item('base_url');
        return str_replace('http','https', $base_url).$path;
    }
    
}

?>
