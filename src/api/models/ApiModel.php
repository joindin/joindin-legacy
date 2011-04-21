<?php

class ApiModel {
    public static function getDefaultFields() {
        return array();
    }
    public static function getVerboseFields() {
        return array();
    }

    public static function transformResults($results, $verbose) {
        $fields = $verbose ? static::getVerboseFields() : static::getDefaultFields();
        $retval = array();

        // format results to only include named fields
        foreach($results as $row) {
            $entry = array();
            foreach($fields as $key => $value) {
                // special handling for dates
                if(substr($key, -5) == '_date' && !empty($row[$value])) {
                    $entry[$key] = date('c', $row[$value]);
                    /*
                } elseif($value == 'talk_title') {
                    var_dump($row[$value]);
                    */
                } else {
                    $entry[$key] = mb_convert_encoding($row[$value], 'UTF-8');
                }
            }
            $retval[] = $entry;
        }
        return $retval;
    }

    protected static function buildLimit($resultsperpage, $page) {
        if($resultsperpage == 0) {
            // special case, no limits
            $limit = '';
        } else {
            $limit = ' LIMIT '
                . $resultsperpage * ($page - 1) . ','
                . $resultsperpage;
        }
        return $limit;
    }
}
