<?php 
/**
 * Provides helpers related to oauth functionality
 *
 * @category Helper
 * @package  Views
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Defines the different OAuth Signing algorithms. You 
 * should use this instead of writing them out each time.
 *
 * @category Helper
 * @package  Oauth
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class OAUTH_ALGORITHMS
{
    const HMAC_SHA1 = 'HMAC-SHA1';
    const RSA_SHA1  = 'RSA-SHA1';
}

/**
 * Signs an array of oauth parameters according to the 1.0 spec using
 * the hmac-sha1 hasing algorithm
 *
 * @param string $method     either GET or POST
 * @param string $baseurl    the baseurl we are authenticating againts
 * @param string $secret     the consumer secret key
 * @param array  $parameters all parameters that need to be signed 
 *                           (NOTE: the token secret key should be added here)
 *
 * @return string the signature
 */
function sign_hmac_sha1($method, $baseurl, $secret, array $parameters)
{
    $data  = $method.'&';
    $data .= urlencode($baseurl).'&';
    $oauth = '';
    ksort($parameters);
    //Put the token secret in if it does not exist. It
    //will be empty if it does not exist as per the spec.
    if (!array_key_exists('oauth_token_secret', $parameters)) {
        $parameters['oauth_token_secret'] = '';
    }

    foreach ($parameters as $key => $value) {
        //Don't include the token secret into the base string
        if (strtolower($key) != 'oauth_token_secret') {
            $oauth .= "&{$key}={$value}";
        }
    }    
    $data   .= urlencode(substr($oauth, 1));
    $secret .= '&'.$parameters['oauth_token_secret'];
    
    return base64_encode(hash_hmac('sha1', $data, $secret, true));
}

/**
 * Signs an array of oauth parameters according to the 1.0 spec using
 * the rsa-sha1 hasing algorithm
 *
 * @param string $method     either GET or POST
 * @param string $baseurl    the baseurl we are authenticating againts
 * @param string $certfile   the location of your private certificate file
 * @param array  $parameters all parameters that need to be signed
 * 
 * @return string the signature
 */
function sign_rsa_sha1($method, $baseurl, $certfile, array $parameters)
{
    $fp      = fopen($certfile, "r");
    $private = fread($fp, 8192);
    fclose($fp);

    $data  = $method.'&';
    $data .= urlencode($baseurl).'&';
    $oauth = '';
    ksort($parameters);

    foreach ($parameters as $key => $value) {
        $oauth .= "&{$key}={$value}";
    }
    $data .= urlencode(substr($oauth, 1));

    $keyid = openssl_get_privatekey($private);
    openssl_sign($data, $signature, $keyid);
    openssl_free_key($keyid);

    return base64_encode($signature);
}

/**
 * Assembles the auth params array into a string that can
 * be put into an http header request.
 *
 * @param array $authparams the oauth parameters
 *
 * @return string the header authorization portion with trailing \r\n
 */
function build_auth_string(array $authparams)
{
    $header = "Authorization: OAuth ";
    $auth   = '';
    foreach ($authparams AS $key=>$value) {
        //Don't include token secret
        if ($key != 'oauth_token_secret') {
            $auth .= ",{$key}=\"{$value}\"";
        }
    }
    return $header.substr($auth, 1)."\r\n";
}

/**
 * Assemble an associative array with oauth values
 *
 * @param string $baseurl the base url we are authenticating against.
 * @param string $key     your consumer key
 * @param string $secret  either your consumer secret key or the file 
 *                        location of your rsa private key.
 * @param array  $extra   additional oauth parameters that should be 
 *                        included (you must urlencode, if appropriate,
 *                        before calling this function)
 * @param string $method  either GET or POST
 * @param string $algo    either HMAC-SHA1 or RSA-SHA1 (NOTE: this affects 
 *                        what you put in for the secret parameter)
 *
 * @return array of all the oauth parameters
 */
function build_auth_array(
    $baseurl, 
    $key, 
    $secret, 
    $extra = array(), 
    $method = 'GET', 
    $algo = OAUTH_ALGORITHMS::RSA_SHA1
) {
    $auth['oauth_consumer_key']     = $key;
    $auth['oauth_signature_method'] = $algo;
    $auth['oauth_timestamp']        = time();
    $auth['oauth_nonce']            = md5(uniqid(rand(), true));
    $auth['oauth_version']          = '1.0';

    $auth = array_merge($auth, $extra);
    if (strtoupper($algo) == OAUTH_ALGORITHMS::HMAC_SHA1) { 
        $auth['oauth_signature'] = sign_hmac_sha1(
            $method, 
            $baseurl, 
            $secret, 
            $auth
        );
    } else if (strtoupper($algo) == OAUTH_ALGORITHMS::RSA_SHA1) { 
        $auth['oauth_signature'] = sign_rsa_sha1(
            $method, 
            $baseurl, 
            $secret, 
            $auth
        );
    }
  
    $auth['oauth_signature'] = urlencode($auth['oauth_signature']);
    return $auth;
}

/**
 * Creates the authorization portion of a header NOTE: This does not
 * create a complete http header. Also NOTE: the oauth_token parameter
 * should be passed in using the $extra array.
 *
 * @param string $baseurl the base url we are authenticating against.
 * @param string $key     your consumer key
 * @param string $secret  either your consumer secret key or the file 
 *                        location of your rsa private key.
 * @param array  $extra   additional oauth parameters that should be included 
 *                        (you must urlencode a parameter, if appropriate, 
 *                        before calling this function)
 * @param string $method  either GET or POST
 * @param string $algo    either HMAC-SHA1 or RSA-SHA1 (NOTE: this affects 
 *                        what you put in for the secret parameter)
 *
 * @return string the header authorization portion with trailing \r\n
 */
function get_auth_header(
    $baseurl, 
    $key, 
    $secret, 
    $extra = array(), 
    $method = 'GET', 
    $algo = OAUTH_ALGORITHMS::RSA_SHA1
) {
    $auth = build_auth_array($baseurl, $key, $secret, $extra, $method, $algo);
    return build_auth_string($auth);
}

/* ./system/application/helpers/oauth_helper.php */
