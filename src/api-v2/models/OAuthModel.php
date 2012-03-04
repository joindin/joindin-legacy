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

    /**
     * just sets the DB - this did more under oauth1
     * 
     * @param $db 
     * @access public
     * @return void
     */
    public function setUpOAuthAndDb(PDO $db) {
        $this->db = $db;
        return true;
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
