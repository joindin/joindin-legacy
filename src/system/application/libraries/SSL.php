<?php
/**
 * SSL Class
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * SSL Class
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class SSL
{

    /**
     * Array listing of secure URLs
     */
    private $_secure_url = array(
        "/user/login"
    );
    /**
     * CodeIgniter instance
     */
    protected  $ci = null;

    /**
     * Instantiate the class and get codeigniter instance
     */
    public function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * Main routing function
     *
     * @param string $path User-defined path
     *
     * @return void
     */
    public function sslRoute($path = '')
    {
        // Check to see if the "USE_SSL" is in config
        $use_ssl = $this->ci->config->item('use_ssl');
        if (!$use_ssl) {
            return;
        }

        if (empty($path)) {
            $path = $_SERVER['REQUEST_URI'];
        }
        if ($this->isSecure($path) && !$this->isRequestSecure()) {
            header('Location: ' . $this->buildRedirect($path));
        }
    }

    /**
     * Check to see if the given path is secure
     *
     * @param string $path User-defined path
     *
     * @return boolean
     */
    private function isSecure($path)
    {
        return (in_array($path, $this->_secure_url)) ? true : false;
    }

    /**
     * Check to see if the current request is on HTTPS
     *
     * @return boolean
     */
    private function isRequestSecure()
    {
        return (isset($_SERVER['SECURE']) && $_SERVER['SECURE'] == 1) ? true : false;
    }

    /**
     * Make our redirect link to the other side...
     *
     * @param string $path Path to redirect or request uri if empty
     *
     * @return string
     */
    private function buildRedirect($path = '')
    {
        if (empty($path)) {
            $path = $_SERVER['REQUEST_URI'];
        }
        $base_url = $this->ci->config->item('base_url');

        return str_replace('http', 'https', $base_url) . $path;
    }

}

