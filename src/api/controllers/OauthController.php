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
        switch($request->url_elements[3]) {
            case 'request_token':
                $tokens = OAuthModel::newRequestToken($db, $this->provider, $request->parameters['oauth_callback']);
                if($tokens) {
                    // bypass the view handling
                    echo 'login_url=http://lorna.rivendell.local/user/oauth_allow?' .
                                         'request_token='.$tokens['request_token'].
                                         '&request_token_secret='.$tokens['request_token_secret'].
                                         '&oauth_callback_confirmed=true';
                }
                break;
            case 'access_token':
                $tokens = OAuthModel::newAccessToken($db, $this->provider, 
                    $request->parameters['oauth_token'],
                    $request->parameters['oauth_verifier']);
                if($tokens) {
                    echo "oauth_token=" . $tokens['oauth_token'] . '&oauth_token_secret=' . $tokens['oauth_token_secret'];
                }
                break;
        }
        exit;
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

    public function tokenHandler($provider) {
        $token = OAuthModel::getRequestTokenSecretByToken($this->db, $provider->token);
        $provider->token_secret = $token['request_token_secret'];

        return OAUTH_OK;
    }
}
