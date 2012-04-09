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

        $this->parseParameters();
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

    /**
     * What format/method of request is this?  Figure it out and grab the parameters
     * 
     * @access protected
     * @return boolean true
     */
    protected function parseParameters() {
        // first of all, pull the GET vars
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
            $this->parameters = $parameters;
        }

        // now how about PUT/POST bodies? These override what we already had
        if($this->verb == 'POST' || $this->verb == 'PUT') {
            $body = file_get_contents("php://input");
            if(isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == "application/json") {
                $body_params = json_decode($body);
                if($body_params) {
                    foreach($body_params as $param_name => $param_value) {
                        $this->parameters[$param_name] = $param_value;
                    }
                }
            } else {
                // we could parse other supported formats here
            }
        }
        return true;
    }
}
