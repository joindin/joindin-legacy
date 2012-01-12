<?php
/* 
 * Import CSV values for events and talks
 */

class Csvimport {

    private $_ci	    = null;

    public function __construct() {
        $this->CI=&get_instance();
        $this->CI->load->database();
    }

    public function import($file, $event_id) {
        $this->CI->load->library('timezone');
        $this->_importEvent($file, $event_id);
    }
    //---------------
    private function _importEvent($file, $event_id) {
        $fp = fopen($file, 'r');
        $this->_event_id = $event_id;

        // check event exists, get event data
        $events_where = array('ID' => $event_id);
        $events_query = $this->CI->db->get_where('events', $events_where);
        if (is_array($events_query->result())) {
            $this->_event = array_shift($events_query->result());
        } else {
            throw new Exception("Invalid event ID " . $event_id);
            return;
        }

        // check required fields and work out which columns are where
        $title_row = fgetcsv($fp);
        foreach ($title_row as $index => $column) {
            switch(strtolower($column)) {
                case 'title':
                    $this->_title_index = $index;
                    break;
                case 'description':
                    $this->_description_index = $index;
                    break;
                case 'date':
                    $this->_date_index = $index;
                    break;
                case 'time':
                    $this->_time_index = $index;
                    break;
                case 'language':
                    $this->_language_index = $index;
                    break;
                case 'speaker':
                    $this->_speaker_index = $index;
                    break;
                case 'track':
                    $this->_track_index = $index;
                    break;
                case 'type':
                    $this->_type_index = $index;
                    break;
                default:
                    throw new Exception("<p>field " . $column . " ignored</p>\n");
                    break;
            }
        }

        if (!isset($this->_title_index)) {
            throw new Exception("Title is a required field");
        }
        if (!isset($this->_description_index)) {
            throw new Exception("Description is a required field");
        }
        if (!isset($this->_date_index)) {
            throw new Exception("Date is a required field");
        }
        if (!isset($this->_time_index)) {
            throw new Exception("Time is a required field");
        }
        if (!isset($this->_speaker_index)) {
            throw new Exception("Speaker is a required field");
        }
        
        // get the talk categories
        $categories_query = $this->CI->db->get('categories');
        $categories_result = $categories_query->result();
        if (is_array($categories_result)) {
            foreach ($categories_result as $cat) {
                $this->_categories[$cat->cat_title] = $cat;
            }
        }

        // pull a list of languages
        $languages_query = $this->CI->db->get('lang');
        $languages_result = $languages_query->result();
        if (is_array($languages_result)) {
            foreach ($languages_result as $lang) {
                $this->_languages[$lang->lang_abbr] = $lang;
            }
        }

        // get the talk tracks
        $tracks_where = array('event_id' => $event_id);
        $tracks_query = $this->CI->db->get_where('event_track', $tracks_where);
        $tracks_result = $tracks_query->result();
        if (is_array($tracks_result)) {
            foreach ($tracks_result as $track) {
                $this->_tracks[$track->track_name] = $track;
            }
        } else {
            $this->_tracks = array();
        }

        // FINALLY, actually import each row
        while($row = fgetcsv($fp)) {
            $this->_importSession($row);
        }
        return true;
    }
    private function _importSession($row) {
        $talk_data=array(
            'talk_title'  => $row[$this->_title_index],
            'slides_link' => '',
            'event_id'	  => $this->_event_id,
            'talk_desc'	  => trim($row[$this->_description_index]),
            'active'	  => 1,
            'lang'		  => 8 // default to UK English
        );

        // is there a language in this import, how about in this row?
        if (isset($this->_language_index) && isset($row[$this->_language_index])) {
            // if there's a language, is it valid?
            if (isset($this->_languages[$row[$this->_language_index]])) {
                $talk_data['lang'] = $this->_languages[$row[$this->_language_index]]->ID;
            } else {
                 throw new Exception("Language " . $row[$this->_language_index] . " not supported");
            }
        }

        // handle date and time, this requires event tz to be set correctly
        
        $second = 0;
        $time = explode(':', $row[$this->_time_index]);
        $hour = $time[0];
        $minute = $time[1];

        // Date required in ISO EN18601 (YYYY-MM-DD)
        $date = explode('-', $row[$this->_date_index]);
        $day = $date[2];
        $month = $date[1];
        $year = $date[0];

        $tz = $this->_event->event_tz_cont . '/' . $this->_event->event_tz_place;

        $talk_data['date_given'] = $this->CI->timezone->UnixtimeForTimeInTimezone($tz, $year, $month, $day, $hour, $minute, $second);

        // save talk detail
        $this->CI->db->insert('talks', $talk_data);
        $talk_id = $this->CI->db->insert_id();
        
        // handle the category - figure out which it is, then save it
        $cat_id = $this->_categories['Talk']->ID;
        if (isset($this->_type_index)) {
            if (isset($this->_categories[$row[$this->_type_index]])) {
                $cat_id = $this->_categories[$row[$this->_type_index]]->ID;
            } else {
                throw new Exception("Cannot create session of type " . $row[$this->_type_index]);
            }
        }
        $this->CI->db->insert('talk_cat', array("talk_id" => $talk_id, "cat_id" => $cat_id));

        // Import the speakers
        if (empty($row[$this->_speaker_index])) {
            throw new Exception("Speaker is a required field (Talk: " . $row[$this->_title_index] . ')');
        }
        $speakers = explode(',', $row[$this->_speaker_index]);
        foreach ($speakers as $speaker) {
            $this->CI->db->insert('talk_speaker', array("talk_id" => $talk_id, "speaker_name" => $speaker));
        }

        // handle the track - figure out which it is, then save it
        if (isset($this->_track_index) && !empty($row[$this->_track_index])) {
            $tracks = explode(',', $row[$this->_track_index]);
            foreach ($tracks as $track) {
                if (isset($this->_tracks[$track])) {
                    $track_id = $this->_tracks[$track]->ID;
                    $this->CI->db->insert('talk_track', array("talk_id" => $talk_id, "track_id" => $track_id));
                } else {
                    throw new Exception("Track " . $track . " is not recognized");
                }
            }
        }
    }
}
