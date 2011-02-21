<?php

class EventModel extends ApiModel {
    public static function getDefaultFields() {
        $fields = array(
            'event_id' => 'ID',
            'event_name' => 'event_name',
            'event_start' => 'event_start',
            'event_end' => 'event_end',
            'event_description' => 'event_desc',
            'event_href' => 'event_href',
            'event_icon' => 'event_icon'
            );
        return $fields;
    }

    public static function getVerboseFields() {
        $fields = array(
            'event_id' => 'ID',
            'event_name' => 'event_name',
            'event_start' => 'event_start',
            'event_end' => 'event_end',
            'event_description' => 'event_desc',
            'event_href' => 'event_href',
            'event_lat' => 'event_lat',
            'event_long' => 'event_long',
            'event_tz_cont' => 'event_tz_cont',
            'event_tz_place' => 'event_tz_place',
            'event_icon' => 'event_icon',
            'event_loc' => 'event_location',
            'event_cfp_start' => 'event_cfp_start',
            'event_cfp_end' => 'event_cfp_end'
            );
        return $fields;
    }

    public static function getEventById($db, $event_id, $verbose = false) {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . 'pending = "0" and '
            . 'ID = :event_id';
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

    public static function getEventList($db, $verbose = false) {
        $sql = 'select * from events '
            . 'where active = 1 and '
            . 'pending = "0" '
            . 'order by event_start desc';
        $stmt = $db->prepare($sql);
        $response = $stmt->execute();
        if($response) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $retval = static::transformResults($results, $verbose);
            return $retval;
        }
        return false;
    }

}
