<?php

/*
 * this controller is a one-off, to handle authentication steps
 * basic concepts taken from : http://toys.lerdorf.com/archives/55-Writing-an-OAuth-Provider-Service.html
 */

class OauthController {

    public function setUpOAuthAndDb($db) {
        $this->db = $db;
        try {
            $this->provider = new OAuthProvider();
            $this->provider->consumerHandler(array($this,'lookupConsumer'));    
            $this->provider->timestampNonceHandler(array($this,'timestampNonceChecker'));
            $this->provider->tokenHandler(array($this,'tokenHandler'));
            $this->provider->setRequestTokenPath('/v2/oauth/request_token');  // No token needed for this end point
            $this->provider->checkOAuthRequest();
        } catch (OAuthException $E) {
            error_log(OAuthProvider::reportProblem($E));
            return false;
        }
        return true;
    }

    public function handle($request, $db) {
        $this->setUpOAuthAndDb($db);
        $tokens = OAuthModel::newRequestToken($db, $this->provider, $request->parameters['oauth_callback']);
        $retval = array(
            'request_token' => $tokens['request_token'],
            'request_token_secret' => $tokens['request_token_secret'],
            'auth_url' => urlencode('http://' . $request->host . '/v2/oauth/login?request_token=' . $tokens['request_token'])
        );
        return $retval;
    }

    /*
     * need to set $provider->consumer_secret to the stored version, this then gets checked
     */
    public function lookupConsumer($provider) {
        $consumer = OAuthModel::getConsumerSecretByKey($this->db, $provider->consumer_key);
        $provider->consumer_secret = $consumer['consumer_secret'];

        return OAUTH_OK;
    }
   
    public function timestampNonceChecker() {
        // TODO actually add some checking
        return OAUTH_OK;
    }

    public function tokenHandler() {
        // TODO actually check!
        return OAUTH_OK;
    }
}
