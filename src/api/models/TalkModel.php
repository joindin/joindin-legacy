<?php

class TalkModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'talk_id' => 'ID',
            'event_id' => 'event_id',
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'start_date' => 'date_given',
            'speaker_name' => 'speaker_name'
            );
        return $fields;
    }

    public static function getVerboseFields() {
        $fields = array(
            'talk_id' => 'ID',
            'event_id' => 'event_id',
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'slides_link' => 'slides_link',
            'language' => 'lang_name',
            'start_date' => 'date_given',
            'speaker_name' => 'speaker_name'
            );
        return $fields;
    }
    public static function getTalksByEventId($db, $event_id, $resultsperpage, $start, $verbose = false) {
        $sql = static::getBasicSQL();
        $sql .= ' and t.event_id = :event_id';
        $sql .= static::buildLimit($resultsperpage, $start);

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
        if(is_array($list) && count($list)) {
            foreach($list as $key => $row) {
                $list[$key]['uri'] = 'http://' . $host . '/v2/talks/' . $row['talk_id'];
                $list[$key]['verbose_uri'] = 'http://' . $host . '/v2/talks/' . $row['talk_id'] . '?verbose=yes';
                $list[$key]['comments_link'] = 'http://' . $host . '/v2/talks/' . $row['talk_id'] . '/comments';
                $list[$key]['event_link'] = 'http://' . $host . '/v2/events/' . $row['event_id'];
            }
        }

        return $list;
    }

    public static function getTalkById($db, $talk_id, $verbose = false) {
        $sql = static::getBasicSQL();
        $sql .= ' and t.ID = :talk_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array("talk_id" => $talk_id));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

    public function getBasicSQL() {
        $sql = 'select t.*, l.lang_name, ts.speaker_name from talks t '
            . 'inner join events e on e.ID = t.event_id '
            . 'inner join lang l on l.ID = t.lang '
            . 'left join talk_speaker ts on ts.talk_id = t.ID '
            . 'where t.active = 1 and '
            . 'e.active = 1 and '
            . 'e.pending = 0 and '
            . 'e.private <> "y"';
        return $sql;

    }
}
