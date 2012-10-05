<?php

class ApiMapper
{
    /**
     * Object constructor, sets up the db and some objects need request too
     * 
     * @param PDO     $db      The database connection handle
     * @param Request $request The request object (optional not all objects need it)
     */
    public function __construct(PDO $db, Request $request = null) 
    {
        $this->_db = $db;
        if (isset($request)) {
            $this->_request = $request;
        }
        return true;
    }

    public function getDefaultFields() 
    {
        return array();
    }
    public function getVerboseFields() 
    {
        return array();
    }

    public function transformResults($results, $verbose) 
    {
        $fields = $verbose ? $this->getVerboseFields() : $this->getDefaultFields();
        $retval = array();

        // format results to only include named fields
        foreach ($results as $row) {
            $entry = array();
            foreach ($fields as $key => $value) {
                // special handling for dates
                if (substr($key, -5) == '_date' && !empty($row[$value])) {
                    if ($row['event_tz_place'] != '' && $row['event_tz_cont'] != '') {
                        $tz = $row['event_tz_cont'] . '/' . $row['event_tz_place'];
                    } else {
                        $tz = 'UTC';
                    }
                    $entry[$key] = Timezone::formattedEventDatetimeFromUnixtime($row[$value], $tz, 'c');
                } else {
                    $entry[$key] = $row[$value];
                }
            }
            $retval[] = $entry;
        }
        return $retval;
    }

    protected function buildLimit($resultsperpage, $start) 
    {
        if ($resultsperpage == 0) {
            // special case, no limits
            $limit = '';
        } else {
            $limit = ' LIMIT '
                . $start . ','
                . $resultsperpage;
        }
        return $limit;
    }

    protected function getPaginationLinks($list) 
    {
        $request = $this->_request;
        $count = count($list);
        $meta['count'] = $count; 
        $meta['this_page'] = $request->base . $request->path_info .'?' . http_build_query($request->paginationParameters);
        $next_params = $prev_params = $request->paginationParameters;

        if ($count > 1) {
            $next_params['start'] = $next_params['start'] + $next_params['resultsperpage'];
            $meta['next_page'] = $request->base . $request->path_info . '?' . http_build_query($next_params);
            if ($prev_params['start'] >= $prev_params['resultsperpage']) {
                $prev_params['start'] = $prev_params['start'] - $prev_params['resultsperpage'];
                $meta['prev_page'] = $request->base . $request->path_info . '?' . http_build_query($prev_params);
            }
        }
        return $meta;
    }

}
