<?php

/*
 * Request object
 */

class Request
{
    public $verb;
    public $url_elements;
    public $path_info;
    public $accept = array();
    public $host;
    public $parameters = array();
    public $view;

    public function __construct()
    {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        
        if (isset($_SERVER['PATH_INFO'])) {
            $this->url_elements = explode('/', $_SERVER['PATH_INFO']);
            $this->path_info = $_SERVER['PATH_INFO'];
        }

        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $this->accept = explode(',', $_SERVER['HTTP_ACCEPT']);
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $this->host = $_SERVER['HTTP_HOST'];
        }

        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
            $this->parameters = $parameters;
        }
    }

    public function getParameter($param, $default = '')
    {
        $value = $default;
        if (isset($this->parameters[$param])) {
            $value = $this->parameters[$param];
        }
        return $value;
    }
    
    public function getUrlElement($index, $default = '') 
    {
        $index = (int)$index;
        $element = $default;
        
        if (isset($this->url_elements[$index])) {
            $element = $this->url_elements[$index];
        }

        return $element;
    }
    
    public function accepts($header)
    {
        $result = false;
        foreach ($this->accept as $accept) {
            if (strstr($accept, $header) !== false) {
                return true;
            }
        }
    }
    
    /**
     * Determine if one of the accept headers matches one of the desired
     * formats
     * 
     * @param array $formats
     * @return string
     */
    public function preferredContentTypeOutOf($formats)
    {
        foreach($formats as $format) {
            if ($this->accepts($format)) {
                return $format;
            }
        }
        
        return 'json';
    } 

    public function identifyUser($db, $auth_header) {
        // identify the user
        $oauth_pieces = explode(' ', $auth_header);
        if(count($oauth_pieces) <> 2) {
            throw new Exception('Invalid Authorization Header', '400');
        }
        if(strtolower($oauth_pieces[0]) != "oauth") {
            throw new Exception('Unknown Authorization Header Received', '400');
        }
        $oauth_model = new OAuthModel($db);
        $user_id = $oauth_model->verifyAccessToken($oauth_pieces[1]);
        $this->user_id = $user_id;
        return true;
    }
}
