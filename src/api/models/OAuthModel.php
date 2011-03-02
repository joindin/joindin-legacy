<?php

class OAuthModel {
    public function getConsumerSecretByKey($db, $key) {
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
}
