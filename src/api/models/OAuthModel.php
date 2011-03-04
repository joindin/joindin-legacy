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
     * @param OAuthProvider $provider provider created in the controller
     * @return array the request_token and request_token secret that were generated and saved
     */
    public function newRequestToken(PDO $db, OAuthProvider $provider) {
        // bin 2 hex because the binary isn't friendly
        $request_token = bin2hex($provider->generateToken(3));
        $request_token_secret = bin2hex($provider->generateToken(12));

        $sql = 'insert into oauth_request_tokens set '
            . 'consumer_key = :consumer_key, '
            . 'request_token = :request_token, '
            . 'request_token_secret = :request_token_secret';
        try{
            $stmt = $db->prepare($sql);
            $stmt->execute(array(
                "consumer_key" => $provider->consumer_key,
                "request_token" => $request_token,
                "request_token_secret" => $request_token_secret));
            return array('request_token' => $request_token,
                'request_token_secret' => $request_token_secret);
        } catch(PDOException $e) {
            error_log('Could not insert request token ' . $e->getMessage());
            return false;
        }
    }
}
