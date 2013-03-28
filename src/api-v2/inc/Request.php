<?php

/*
 * Request object
 */

class Request
{
    /**
     * @var string HTTP verb
     */
    protected $verb;
    public $url_elements;
    public $path_info;
    public $accept = array();
    public $host;
    public $parameters = array();
    public $view;
    public $user_id;

    protected $oauthModel;

    /**
     * Builds the request object
     *
     * @param bool $parseParams Set to false to skip parsing parameters on
     *                          construction
     */
    public function __construct($parseParams = true)
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->setVerb($_SERVER['REQUEST_METHOD']);
        }

        if (isset($_SERVER['PATH_INFO'])) {
            $this->setPathInfo($_SERVER['PATH_INFO']);
        }

        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $this->setAccept($_SERVER['HTTP_ACCEPT']);
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $this->setHost($_SERVER['HTTP_HOST']);
        }

        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) {
            $this->setScheme('https://');
        } else {
            $this->setScheme('http://');
        }

        $this->setBase($this->getScheme() . $this->getHost());

        if ($parseParams) {
            $this->parseParameters();
        }
    }

    /**
     * Retrieves the value of a parameter from the request. If a default
     * is provided and the parameter doesn't exist, the default value
     * will be returned instead
     *
     * @param string $param   Parameter to retrieve
     * @param string $default Default to return if parameter doesn't exist
     *
     * @return string
     */
    public function getParameter($param, $default = '')
    {
        $value = $default;
        if (isset($this->parameters[$param])) {
            $value = $this->parameters[$param];
        }

        return $value;
    }

    /**
     * Retrieves a url element by numerical index. If it doesn't exist, and
     * a default is provided, the default value will be returned.
     *
     * @param integer $index Index to retrieve
     * @param string  $default
     *
     * @return string
     */
    public function getUrlElement($index, $default = '')
    {
        $index   = (int)$index;
        $element = $default;

        if (isset($this->url_elements[$index])) {
            $element = $this->url_elements[$index];
        }

        return $element;
    }

    /**
     * Determines if the headers indicate that a particular MIME is accepted based
     * on the browser headers
     *
     * @param string $header Mime type to check for
     *
     * @return bool
     */
    public function accepts($header)
    {
        foreach ($this->accept as $accept) {
            if (strstr($accept, $header) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if one of the accept headers matches one of the desired
     * formats and returns that format. If none of the desired formats
     * are found, it will return 'json'
     *
     * @param array $formats Formats that we want to serve
     *
     * @todo need some real accept header parsing here
     *
     * @return string
     */
    public function preferredContentTypeOutOf($formats)
    {
        foreach ($formats as $format) {
            if ($this->accepts($format)) {
                return $format;
            }
        }

        return 'json';
    }

    /**
     * Finds the authorized user from the oauth header and sets it into a
     * variable on the request.
     *
     * @param PDO    $db          Database adapter (needed to put into OAuthModel if it's not set already)
     * @param string $auth_header Authorization header to send into model
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function identifyUser($db, $auth_header)
    {
        // identify the user
        $oauth_pieces = explode(' ', $auth_header);
        if (count($oauth_pieces) <> 2) {
            throw new InvalidArgumentException('Invalid Authorization Header', '400');
        }
        if (strtolower($oauth_pieces[0]) != "oauth") {
            throw new InvalidArgumentException('Unknown Authorization Header Received', '400');
        }
        $oauth_model   = $this->getOauthModel($db);
        $user_id       = $oauth_model->verifyAccessToken($oauth_pieces[1]);
        $this->user_id = $user_id;

        return true;
    }

    /**
     * What format/method of request is this?  Figure it out and grab the parameters
     *
     * @return boolean true
     *
     * @todo Make paginationParameters part of this object, add tests for them
     */
    public function parseParameters()
    {
        // first of all, pull the GET vars
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
            $this->parameters = $parameters;
            // grab these again, keep them separate for building page hyperlinks
            $this->paginationParameters = $parameters;
        }

        if (!isset($this->paginationParameters['start'])) {
            $this->paginationParameters['start'] = 0;
        }
        if (!isset($this->paginationParameters['resultsperpage'])) {
            $this->paginationParameters['resultsperpage'] = 20;
        }

        // now how about PUT/POST bodies? These override what we already had
        if ($this->getVerb() == 'POST' || $this->getVerb() == 'PUT') {
            $body = $this->getRawBody();
            if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == "application/json") {
                $body_params = json_decode($body);
                if ($body_params) {
                    foreach ($body_params as $param_name => $param_value) {
                        $this->parameters[$param_name] = $param_value;
                    }
                }
            } else {
                // we could parse other supported formats here
            }
        }

        return true;
    }

    /**
     * Returns the raw body from POST or PUT calls
     *
     * @return string
     */
    public function getRawBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * Retrieves the verb of the request (method)
     *
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * Allows for manually setting of the request verb
     *
     * @param string $verb Verb to set
     *
     * @return Request
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;

        return $this;
    }

    /**
     * Returns the host from the request
     *
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the host on the request
     *
     * @param string $host Host to set
     *
     * @return Request
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Returns the scheme for the request
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Sets the scheme for the request
     *
     * @param string $scheme Scheme to set
     *
     * @return Request
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Retrieves or builds an OauthModel object. If it is already built/provided
     * then it can be retrieved without providing a database adapter. If it hasn't
     * been built already, then you must provide a PDO object to put into the
     * model.
     *
     * @param PDO $db [optional] PDO db adapter to put into OAuthModel object
     *
     * @return OAuthModel
     * @throws InvalidArgumentException
     */
    public function getOauthModel(PDO $db = null)
    {
        if (is_null($this->oauthModel)) {
            if (is_null($db)) {
                throw new \InvalidArgumentException('Db Must be provided to get Oauth Model');
            }
            $this->oauthModel = new OAuthModel($db);
        }

        return $this->oauthModel;
    }

    /**
     * Sets an OAuthModel for the request to use should it need to
     *
     * @param OAuthModel $model Model to set
     *
     * @return Request
     */
    public function setOauthModel(OAuthModel $model)
    {
        $this->oauthModel = $model;

        return $this;
    }

    /**
     * Sets a user id
     *
     * @param string $userId User id to set
     *
     * @return Request
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Retrieves the user id that's been set on the request
     *
     * @return string|null
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Sets the path info variable. Also explodes the path into url elements
     *
     * @param string $pathInfo Path info to set
     *
     * @return self
     */
    public function setPathInfo($pathInfo)
    {
        $this->path_info    = $pathInfo;
        $this->url_elements = explode('/', $pathInfo);

        return $this;
    }

    /**
     * Retrieves the original path info variable
     *
     * @return string
     */
    public function getPathInfo()
    {
        return $this->path_info;
    }

    /**
     * Sets the accepts variable from the accept header
     *
     * @param string $accepts Accepts header string
     *
     * @return self
     */
    public function setAccept($accepts)
    {
        $this->accept = explode(',', $accepts);

        return $this;
    }

    /**
     * Sets the URI base
     *
     * @param string $base Base to set
     *
     * @return Request
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Returns the url base
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }
}
