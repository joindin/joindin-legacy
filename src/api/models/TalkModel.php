<?php

class TalkModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'talk_id' => 'ID',
            'event_id' => 'event_id',
            'talk_title' => 'talk_title'
            );
        return $fields;
    }

    public static function getTalksByEventId($db, $event_id, $resultsperpage, $page, $verbose = false) {
        $sql = 'select * from talks '
            . 'where active = 1 and '
            . 'event_id = :event_id';
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

    public static function addHyperMedia($list, $host) {
        // loop again and add links specific to this item
        foreach($list as $key => $row) {
            $list[$key]['uri'] = 'http://' . $host . '/v2/talks/' . $row['talk_id'];
            $list[$key]['comments_link'] = 'http://' . $host . '/v2/talks/' . $row['talk_id'] . '/comments';
        }
        return $list;
    }
}
