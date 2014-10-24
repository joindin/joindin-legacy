<?php
/**
 * Twitter oauth authorization
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
 * Unlike what the name might indicate this library should work with most
 * general OAuth implementations all you would need to change are the HOST,
 * AUTHORIZE_URI, REQUEST_URI, and ACCESS_URI. To the appropriate values. I
 * can't guarantee this will work but I know it works for twitter hence the
 * name. Twitter only uses HMAC-SHA1 signing but this library can handle
 * both RSA-SHA1 and HMAC-SH1. It defaults to HMAC and uses GET as its HTTP
 * connection method. Both of these can be changed.
 *
 * Enjoy the OAuthy goodness <3 JimDoesCode.
 *
 * Twitter oauth authorization
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class twitter_oauth
{
    const SCHEME        = 'https';
    const HOST          = 'api.twitter.com';
    const AUTHORIZE_URI = '/oauth/authorize';
    const REQUEST_URI   = '/oauth/request_token';
    const ACCESS_URI    = '/oauth/access_token';

    //Array that should contain the consumer secret and
    //key which should be passed into the constructor.
    private $_consumer = false;

    /**
     * Pass in a parameters array which should look as follows:
     * array('key'=>'example.com', 'secret'=>'mysecret');
     * Note that the secret should either be a hash string for
     * HMAC signatures or a file path string for RSA signatures.
     *
     * @param array $params Parameters
     *
     * @return void
     */
    public function twitter_oauth($params)
    {
        $this->CI = get_instance();
        $this->CI->load->helper('oauth');

        if (!array_key_exists('method', $params)) {
            $params['method'] = 'GET';
        }
        if (!array_key_exists('algorithm', $params)) {
            $params['algorithm'] = OAUTH_ALGORITHMS::HMAC_SHA1;
        }

        $this->_consumer = $params;
    }

    /**
     * This is called to begin the oauth token exchange. This should only
     * need to be called once for a user, provided they allow oauth access.
     * It will return a URL that your site should redirect to, allowing the
     * user to login and accept your application.
     *
     * @param string $callback the page on your site you wish to return to
     *                         after the user grants your application access.
     *
     * @return mixed either the URL to redirect to, or if they specified HMAC
     *         signing an array with the token_secret and the redirect url
     */
    public function get_request_token($callback)
    {
        $baseurl = self::SCHEME . '://' . self::HOST . self::REQUEST_URI;

        //Generate an array with the initial oauth values we need
        $auth = build_auth_array(
            $baseurl, $this->_consumer['key'], $this->_consumer['secret'],
            array('oauth_callback' => urlencode($callback)),
            $this->_consumer['method'], $this->_consumer['algorithm']
        );
        //Create the "Authorization" portion of the header
        $str = "";
        foreach ($auth as $key => $value) {
            $str .= ",{$key}=\"{$value}\"";
        }
        $str = 'Authorization: OAuth ' . substr($str, 1);
        //Send it
        $response = $this->_connect($baseurl, $str);
        //We should get back a request token and secret which
        //we will add to the redirect url.
        parse_str($response, $resarray);
        //Return the full redirect url and let the user decide what to do from there.
        $redirect = self::SCHEME . '://' .
            self::HOST . self::AUTHORIZE_URI .
            "?oauth_token={$resarray['oauth_token']}";
        // If they are using HMAC then we need to return the
        // token secret for them to store.
        if ($this->_consumer['algorithm'] == OAUTH_ALGORITHMS::RSA_SHA1) {
            return $redirect;
        } else {
            return array(
                'token_secret' => $resarray['oauth_token_secret'],
                'redirect'     => $redirect
            );
        }
    }

    /**
     * This is called to finish the oauth token exchange. This too should
     * only need to be called once for a user. The token returned should
     * be stored in your database for that particular user.
     *
     * @param string $token    this is the oauth_token returned with your
     *                         callback url
     * @param string $secret   this is the token secret supplied from the
     *                         request (Only required if using HMAC)
     * @param string $verifier this is the oauth_verifier returned with
     *                         your callback url
     *
     * @return array access token and token secret
     */
    public function get_access_token(
        $token = false,
        $secret = false,
        $verifier = false
    ) {
        //If no request token was specified then attempt to get one from the url
        if ($token === false && isset($_GET['oauth_token'])) {
            $token = $_GET['oauth_token'];
        }
        if ($verifier === false && isset($_GET['oauth_verifier'])) {
            $verifier = $_GET['oauth_verifier'];
        }
        //If all else fails attempt to get it from the request uri.
        if ($token === false && $verifier === false) {
            $uri      = $_SERVER['REQUEST_URI'];
            $uriparts = explode('?', $uri);

            $authfields = array();
            parse_str($uriparts[1], $authfields);
            $token    = $authfields['oauth_token'];
            $verifier = $authfields['oauth_verifier'];
        }

        $tokenddata = array(
            'oauth_token'    => urlencode($token),
            'oauth_verifier' => urlencode($verifier)
        );
        if ($secret !== false) {
            $tokenddata['oauth_token_secret'] = urlencode($secret);
        }

        $baseurl = self::SCHEME . '://' . self::HOST . self::ACCESS_URI;
        //Include the token and verifier into the header request.
        $auth     = get_auth_header(
            $baseurl, $this->_consumer['key'], $this->_consumer['secret'],
            $tokenddata, $this->_consumer['method'], $this->_consumer['algorithm']
        );
        $response = $this->_connect($baseurl, $auth);
        //Parse the response into an array it should contain
        //both the access token and the secret key. (You only
        //need the secret key if you use HMAC-SHA1 signatures.)
        parse_str($response, $oauth);

        //Return the token and secret for storage
        return $oauth;
    }

    /**
     * Connects to the server and sends the request,
     * then returns the response from the server.
     *
     * @param string $url  Url to auth with
     * @param string $auth Auth
     *
     * @return mixed
     */
    private function _connect($url, $auth)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($auth));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
