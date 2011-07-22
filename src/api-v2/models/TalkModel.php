<?php

class TalkModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'talk_id' => 'ID',
            'event_id' => 'event_id',
            'talk_title' => 'talk_title',
            'talk_description' => 'talk_desc',
            'start_date' => 'date_given',
            'average_rating' => 'avg_rating',
            'comment_count' => 'comment_count',
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
            'average_rating' => 'avg_rating',
            'comment_count' => 'comment_count',
            'speaker_name' => 'speaker_name'
            );
        return $fields;
    }
    public static function getTalksByEventId($db, $event_id, $resultsperpage, $start, $request, $verbose = false) {
        var_dump($request);
        $sql = static::getBasicSQL();
        $sql .= ' and t.event_id = :event_id';
        $sql .= static::buildLimit($resultsperpage, $start);

        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array(
            ':event_id' => $event_id
            ));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $request, $verbose);
            return $retval;
        }
        return false;
    }

    public static function transformResults($results, $request, $verbose) {
        $list = parent::transformResults($results, $request);
        $host = $request->host;
        // loop again and add links specific to this item
        if(is_array($list) && count($list)) {
            foreach($results as $key => $row) {
                $list[$key]['uri'] = 'http://' . $host . '/v2/talks/' . $row['ID'];
                $list[$key]['verbose_uri'] = 'http://' . $host . '/v2/talks/' . $row['ID'] . '?verbose=yes';
                $list[$key]['website_uri'] = 'http://joind.in/talk/view/' . $row['ID'];
                $list[$key]['comments_link'] = 'http://' . $host . '/v2/talks/' . $row['ID'] . '/comments';
                $list[$key]['event_link'] = 'http://' . $host . '/v2/events/' . $row['event_id'];
            }

            if(count($list) > 1) {
                $list = static::addPaginationLinks($list, $request);
            }
        }

        return $list;
    }

    public static function getTalkById($db, $talk_id, $request, $verbose = false) {
        $sql = static::getBasicSQL();
        $sql .= ' and t.ID = :talk_id';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute(array("talk_id" => $talk_id));
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $request, $verbose);
            return $retval;
        }
        return false;
    }

    public static function getBasicSQL() {
        $sql = 'select t.*, l.lang_name, ts.speaker_name, '
            . '(select COUNT(ID) from talk_comments tc where tc.talk_id = t.ID) as comment_count, '
            . '(select ROUND(AVG(rating)) from talk_comments tc where tc.talk_id = t.ID) as avg_rating '
            . 'from talks t '
            . 'inner join events e on e.ID = t.event_id '
            . 'inner join lang l on l.ID = t.lang '
            . 'left join talk_speaker ts on ts.talk_id = t.ID '
            . 'where t.active = 1 and '
            . 'e.active = 1 and '
            . '(e.pending = 0 or e.pending is NULL) and '
            . 'e.private <> "y"';
        return $sql;

    }
}
