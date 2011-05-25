<?php

class EventCommentModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'comment_id' => 'ID',
            'event_id' => 'event_id',
            'user_id' => 'user_id',
            'comment' => 'comment'
            );
        return $fields;
    }

    public static function getVerboseFields() {
        $fields = array(
            'comment_id' => 'ID',
            'event_id' => 'event_id',
            'user_id' => 'user_id',
            'comment' => 'comment',
            'created_date' => 'date_made'
            );
        return $fields;
    }

    public static function getEventCommentsByEventId($db, $event_id, $verbose = false) {
        $sql = 'select * from event_comments '
            . 'where event_id = :event_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':event_id' => $event_id
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

}
