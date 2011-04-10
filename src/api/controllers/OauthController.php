<?php

/*
 * this controller is a one-off, to handle authentication steps
 * basic concepts taken from : http://toys.lerdorf.com/archives/55-Writing-an-OAuth-Provider-Service.html
 */

class OauthController {

    public function handle($request, $db) {
        $oauth_model = new OAuthModel();
        $oauth_model->setUpOAuthAndDb($db);
        switch($request->url_elements[3]) {
            case 'request_token':
                $tokens = $oauth_model->newRequestToken($db, $request->parameters['oauth_callback']);
                if($tokens) {
                    // bypass the view handling
                    echo 'login_url=http://lorna.rivendell.local/user/oauth_allow?' .
                                         'request_token='.$tokens['request_token'].
                                         '&request_token='.$tokens['request_token'].
                                         '&request_token_secret='.$tokens['request_token_secret'].
                                         '&oauth_callback_confirmed=true';
                }
                break;
            case 'access_token':
                $tokens = $oauth_model->newAccessToken($db, 
                    $request->parameters['oauth_token'],
                    $request->parameters['oauth_verifier']);
                if($tokens) {
                    echo "oauth_token=" . $tokens['oauth_token'] . '&oauth_token_secret=' . $tokens['oauth_token_secret'];
                }
                break;
        }
        exit;
    }

}
