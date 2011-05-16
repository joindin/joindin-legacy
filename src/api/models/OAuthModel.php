<?php

class OAuthModel {
    /**
     * Fetches the consumer secret from the database so the oauth extension can verify it
     * 
     * @param PDO $db the database handle
     * @param string $key oauth consumer key
     * @return string/bool the secret, or false if it couldn't be retrieved
     */
    public function getConsumerSecretByKey(PDO $db, $key) {
        $sql = 'select consumer_secret from oauth_consumers '
            . 'where consumer_key = :key';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':key' => $key
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // just one row needed
            return $results[0];
        }
        return false;
    }

    /**
     * generates request token and secret, saves to the database with consumer key, then returns them
     * 
     * @param PDO $db connection to the joind.in database
     * @param string $callback where to forward the user to, or "oob" for devices
     * @return array the request_token and request_token secret that were generated and saved
     */
    public function newRequestToken(PDO $db, $callback) {
        // bin 2 hex because the binary isn't friendly
        $request_token = bin2hex($this->provider->generateToken(4));
        $request_token_secret = bin2hex($this->provider->generateToken(12));

        $sql = 'insert into oauth_request_tokens set '
            . 'consumer_key = :consumer_key, '
            . 'request_token = :request_token, '
            . 'request_token_secret = :request_token_secret, '
            . 'callback = :callback';
        try{
            $stmt = $db->prepare($sql);
            $result = $stmt->execute(array(
                "consumer_key" => $this->provider->consumer_key,
                "request_token" => $request_token,
                "request_token_secret" => $request_token_secret,
                "callback" => $callback));
            if($result) {
                return array('request_token' => $request_token,
                    'request_token_secret' => $request_token_secret);
            } else {
                $error = $stmt->errorInfo();
                error_log('Error saving request token: ' . $error[2]);
                return false;
            }
        } catch(PDOException $e) {
            error_log('Could not insert request token ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate, store and return a new access token to the user
     * 
     * @param PDO $db connection to the joind.in db
     * @param string $request_token supplied by the consumer
     * @param string $verifier supplied by the consumer
     * @return array containing the access token and secret 
     */
    public function newAccessToken(PDO $db, $request_token, $verifier) {
        // bin 2 hex because the binary isn't friendly
        $access_token = bin2hex($this->provider->generateToken(8));
        $access_token_secret = bin2hex($this->provider->generateToken(16));

        // get request data
        $request_sql = 'select authorised_user_id as user_id
            from oauth_request_tokens
            where request_token = :request_token
            and verification = :verifier';
        try {
            $request_stmt = $db->prepare($request_sql);
            $request_response = $request_stmt->execute(array(
                "request_token" => $request_token,
                "verifier" => $verifier
                ));
            $request_data = $request_stmt->fetch();
            if($request_data) {
                // now delete this token, we don't need it
                $delete_sql = 'delete from oauth_request_tokens
                    where request_token = :request_token';
                $delete_stmt = $db->prepare($delete_sql);
                $delete_stmt->execute(array('request_token' => $request_token));
            } else {
                error_log('request token not found');
                return false;
            }
        } catch (PDOException $e) {
            error_log('Could not retrieve/delete request token data ' . $e->getMessage());
            return false;
        }

        // store new token
        $sql = 'insert into oauth_access_tokens set '
            . 'access_token = :access_token,' 
            . 'access_token_secret = :access_token_secret,'
            . 'consumer_key = :consumer_key, '
            . 'user_id = :user_id';
        try{
            $stmt = $db->prepare($sql);
            $stmt->execute(array(
                "consumer_key" => $this->provider->consumer_key,
                "access_token" => $access_token,
                "access_token_secret" => $access_token_secret,
                "user_id" => $request_data['user_id']));
            return array('oauth_token' => $access_token,
                'oauth_token_secret' => $access_token_secret);
        } catch(PDOException $e) {
            error_log('Could not insert access token ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch the request token secret for this token
     * 
     * @param PDO $db the joind.in DB
     * @param string $token request token supplied by consumer
     * @return string the token secret
     */
    public function getRequestTokenSecretByToken(PDO $db, $token) {
        $sql = 'select request_token_secret from oauth_request_tokens '
            . 'where request_token = :token';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':token' => $token
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // just one row needed
            return $results[0];
        }
        return false;

    }

    /**
     * Fetch the access token secret for this token
     * 
     * @param PDO $db the joind.in DB
     * @param string $token access token supplied by consumer
     * @return string the token secret
     */
    public function getAccessTokenSecretByToken(PDO $db, $token) {
        $sql = 'select access_token_secret, user_id from oauth_access_tokens '
            . 'where access_token = :token';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':token' => $token
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // just one row needed
            return $results[0];
        }
        return false;

    }

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

    public function lookupConsumer($provider) {
        $consumer = $this->getConsumerSecretByKey($this->db, $provider->consumer_key);
        $provider->consumer_secret = $consumer['consumer_secret'];

        return OAUTH_OK;
    }
   
    public function timestampNonceChecker() {
        // TODO actually add some checking
        return OAUTH_OK;
    }

    public function tokenHandler($provider) {
        if(isset($this->in_flight) && $this->in_flight) {
            $token = $this->getAccessTokenSecretByToken($this->db, $provider->token);
            // set the user_id on the object, index.php uses it
            $this->user_id = $token['user_id'];
            $provider->token_secret = $token['access_token_secret'];
        } else {
            $token = $this->getRequestTokenSecretByToken($this->db, $provider->token);
            $provider->token_secret = $token['request_token_secret'];
        }
        return OAUTH_OK;
    }
}
