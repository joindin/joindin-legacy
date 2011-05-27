<?php

/*
 * Request object
 */

class Request
{
    public $verb;
    public $url_elements;
    public $path_info;
    public $accept;
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
        
        $this->accept = explode(',', $_SERVER['HTTP_ACCEPT']);
        $this->host = $_SERVER['HTTP_HOST'];
        
        parse_str($_SERVER['QUERY_STRING'], $parameters);
        $this->parameters = $parameters;
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
    
    public function accepts($header, $strict = false)
    {
        $result = false;
        foreach ($this->accept as $accept) {
            if ($strict) {
                if ($accept == $header) {
                    return true;
                }
            } else {
                if (strstr($accept, $header) !== false) {
                    return true;
                }
            }
        }
    }
}
