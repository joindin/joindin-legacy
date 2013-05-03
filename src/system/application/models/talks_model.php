<?php
/**
 * Talks model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */

/**
 * Talks model
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Configuration
 * @copyright 2009 - 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class Talks_model extends Model
{
    /**
     * Deletes a talk by its id
     *
     * @param integer $id Talk id
     *
     * @return void
     */
    public function deleteTalk($id)
    {
        $this->db->delete('talks', array('ID' => $id));
    }

    /**
     * Find details on claims of a talk
     *
     * @param integer $tid      Talk ID
     * @param boolean $show_all Switch to show/hide
     *
     * @return array $talks Talk claim data
     */
    public function talkClaimDetail($tid, $show_all = false)
    {
        $this->load->helper("events");

        $sql = sprintf(
            '
            select
                u.username,
                u.email,
                ts.speaker_id uid,
                ts.talk_id rid,
                u.ID userid,
                t.talk_title,
                t.event_id,
                ts.speaker_name speaker
            from
                user u,
                talks t,
                talk_speaker ts
            where
                u.ID = ts.speaker_id and
                ts.talk_id = t.ID and
                t.ID = %s
        ', $this->db->escape($tid)
        );

        if (!$show_all) {
            $sql .= " and (ts.status != 'pending' OR ts.status IS NULL)";
        }

        $query = $this->db->query($sql);
        $talks = $query->result();

        //echo '<pre>'; print_r($ret); echo '</pre>';
        foreach ($talks as $k => $talk) {
            $codes    = array();
            $speakers = array();
            foreach (explode(',', $talk->speaker) as $ik => $iv) {
                $codes[]    = buildCode(
                    $talk->rid,
                    $talk->event_id,
                    $talk->talk_title,
                    trim($iv)
                );
                $speakers[] = trim($iv);
            }
            $talks[$k]->codes    = $codes;
            $talks[$k]->speakers = $speakers;
        }

        return $talks;
    }

    /**
     * Takes in the talk information and the speaker data to see if it's unique
     * Checks the "talks" table with the data
     *
     * @param string $talk_data Information about a talk
     * @param array  $speakers  Speakers
     *
     * @return boolean
     */
    public function isTalkDataUnique($talk_data, $speakers)
    {
        $talk_speakers = array();
        $q             = $this->db->get_where('talks', $talk_data);
        $ret           = $q->result();

        if (count($ret) > 0) {
            $CI =& get_instance();
            $CI->load->model('talk_speaker_model', 'talkSpeaker');

            // We have a match, lets see if the speakers match too
            // For each of the speakers we're given, see if they're in the talk data
            foreach ($ret as $talk) {
                $tid       = $talk->ID;
                $tspeakers = $CI->talkSpeaker->getSpeakerByTalkId($tid);

                foreach ($tspeakers as $tsp) {
                    $talk_speakers[] = $tsp->speaker_name;
                }
                foreach ($speakers as $sp) {
                    if (in_array($sp, $talk_speakers)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Checks if the user already made a particular type of
     * comment on a talk
     *
     * @param integer $tid  Talk Id
     * @param integer $uid  User id
     * @param string  $type Type
     *
     * @return bool
     */
    public function hasUserCommented($tid, $uid, $type = null)
    {
        $arr = array('user_id' => $uid, 'talk_id' => $tid);
        if ($type) {
            $arr['comment_type'] = $type;
        }
        $q   = $this->db->get_where('talk_comments', $arr);
        $ret = $q->result();

        return (isset($ret[0])) ? true : false;
    }

    /**
     * Retrieves talks
     *
     * @param integer $tid    Talk id (optional)
     * @param bool    $latest Retrieve latest talks
     *
     * @return array
     */
    public function getTalks($tid = null, $latest = false)
    {
        $this->load->helper("events");
        $this->load->helper("talk");
        if ($tid) {
            if (!ctype_digit((string)$tid)) {
                // It's not an integer for some reason...
                return array();
            }

            $sql = sprintf(
                '
                select
                    talks.*,
                    CASE 
                        WHEN (((talks.date_given - 86400) < ' .
                mktime(0, 0, 0) .
                ') and (talks.date_given + (3*30*3600*24)) > ' .
                mktime(0, 0, 0) . ') THEN 1
                        ELSE 0
                        END as allow_comments,
                    talks.ID tid,
                    events.ID eid,
                    events.event_name,
                    events.event_start,
                    events.event_end,
                    events.event_tz_cont,
                    events.event_tz_place,
                    events.private,
                    lang.lang_name,
                    lang.lang_abbr,
                    lang.id as lang_id,
                    count(talk_comments.ID) as ccount,
                    get_talk_rating(talks.ID) as tavg,
                    (select 
                        cat.cat_title
                    from 
                        talk_cat tac, categories cat
                    where 
                        tac.talk_id=talks.ID and tac.cat_id=cat.ID
                    ) tcid,
                    (select max(date_made) from talk_comments
                        where talk_id=talks.ID) last_comment_date
                from
                    talks
                left join talk_comments on
                    (talk_comments.talk_id = talks.ID AND talk_comments.private = 0)
                inner join events on (events.ID = talks.event_id)
                inner join lang on (lang.ID = talks.lang)
                where
                    talks.ID=%s and
                    talks.active=1
                group by
                    talks.ID
            ', $this->db->escape($tid)
            );
            $q   = $this->db->query($sql);
        } else {
            if ($latest) {
                $wh = ' talks.date_given<=' . mktime(0, 0, 0) . ' and ';
                $ob = ' order by talks.date_given desc';
            } else {
                $wh = '';
                $ob = '';
            }
            $sql = sprintf(
                '
                select
                    talks.*,
                    talks.ID tid,
                    events.ID eid,
                    events.event_name,
                    events.event_tz_cont,
                    events.event_tz_place,
                    events.event_start,
                    events.event_end,
                    events.private,
                    lang.lang_name,
                    lang.lang_abbr,
                    count(talk_comments.ID) as ccount,
                    get_talk_rating(talks.ID) as tavg,
                    (select max(date_made) from talk_comments
                      where talk_id=talks.ID) last_comment_date
                from
                    talks
                left join talk_comments on (talk_comments.talk_id = talks.ID)
                inner join events on (events.ID = talks.event_id)
                inner join lang on (lang.ID = talks.lang)
                where
                    %s
                    talks.active=1
                group by
                    talks.ID
                %s
            ', $wh, $ob
            );
            $q   = $this->db->query($sql);
        }
        $res = $q->result();

        $CI =& get_instance();
        $CI->load->model('talk_speaker_model', 'tsm');
        foreach ($res as $k => $talk) {
            $res[$k]->speaker = $CI->tsm->getTalkSpeakers($talk->ID);
        }

        return $res;
    }

    /**
     * Gets the comments for a session/talk
     *
     * @param integer $tid     Talk ID
     * @param integer $cid     [optional] Comment ID
     *                         (if you want to get only one comment)
     * @param boolean $private Whether to include private comments
     *
     * @return array The comments, from database
     */
    public function getTalkComments($tid, $cid = null, $private = false)
    {
        $this->load->library('gravatar');

        $c_addl   = ($cid) ? ' and tc.ID=' . $this->db->escape($cid) : '';
        $priv     = (!$private) ? ' and tc.private=0' : '';
        $sql      = sprintf(
            '
            select
                tc.talk_id,
                tc.rating,
                tc.comment,
                tc.date_made,
                tc.ID,
                tc.private,
                tc.active,
                tc.user_id,
                u.username uname,
                u.full_name,
                u.twitter_username twitter_username,
                tc.comment_type,
                tc.source
            from
                talk_comments tc
            left join
                user u on u.ID = tc.user_id
            where
                tc.active=1 and
                tc.talk_id=%s %s %s
            order by tc.date_made asc
        ', $this->db->escape($tid), $c_addl, $priv
        );
        $q        = $this->db->query($sql);
        $comments = $q->result();
        foreach ($comments as $k => $comment) {
            $comments[$k]->gravatar
                = $this->gravatar->displayUserImage($comment->user_id, null, 45);
        }

        return $comments;
    }

    /**
     * Retrieves popular talks
     *
     * @param int $len Number of talks to retrieve
     *
     * @return mixed
     * @throws Exception
     */
    public function getPopularTalks($len = 7)
    {
        if (!ctype_digit((string)$len)) {
            throw new Exception('Expected length to be a number, received ' . $len);
        }

        $sql   = sprintf(
            '
            select
                t.talk_title,
                t.ID,
                count(tc.ID) as ccount,
                e.ID eid,
                e.event_name
            from
                talks t
            JOIN talk_comments tc
            ON tc.talk_id=t.ID AND tc.private = 0
            JOIN events e
            ON e.ID=t.event_id
            where
                t.active=1
                and (tc.user_id != 0 and tc.rating != 0)
            group by
                t.ID
            order by
                ccount desc
            limit ' . $len . '
        '
        );
        $query = $this->db->query($sql);
        $talks = $query->result();

        $CI =& get_instance();
        $CI->load->model('talk_speaker_model', 'tsm');
        foreach ($talks as $k => $talk) {
            $sql             = "select get_talk_rating(" . $talk->ID . ") as tavg";
            $rating_result   = $this->db->query($sql)->result();
            $rating          = $rating_result[0];
            $talks[$k]->tavg = $rating->tavg;

            $talks[$k]->speaker = $CI->tsm->getTalkSpeakers($talk->ID);
        }

        return $talks;
    }

    /**
     * Get recent talks from any and all events
     *
     * @return array Talk detail information
     */
    public function getRecentTalks()
    {
        $sql   = sprintf(
            "
            select
              DISTINCT t.ID,
              t.talk_title,
              t.date_given,
              t.duration,
              count(tc.ID) as ccount,
              get_talk_rating(t.ID) as tavg,
              e.ID eid,
              e.event_name,
              e.event_start
            from
              talks t
              JOIN events e
                ON e.ID=t.event_id
              JOIN talk_comments tc
                ON tc.talk_id=t.ID AND tc.private = 0
              INNER JOIN talk_speaker ts
                ON t.ID = ts.talk_id
            WHERE
                e.event_start > %s
              and
                (ts.status != 'pending' OR ts.status is null)
              and (tc.user_id != 0 and tc.rating != 0)
            group by
              t.ID
            having
              tavg>3 and ccount>3
        ", strtotime('-3 months')
        );
        $query = $this->db->query($sql);

        return $query->result();
    }

    /**
     * Get the talks successfully claimed by the user
     * Results include talk information
     *
     * @param integer $uid     User ID
     * @param boolean $showAll Show talks without a claim
     *
     * @return array $talks Talk detail information
     */
    public function getUserTalks($uid, $showAll = false)
    {
        $talks   = array();
        $claimed = $this->getSpeakerTalks($uid);

        foreach ($claimed as $index => $claim) {
            // remove if pending
            if ($claim->status != null && $showAll === false) {
                continue;
            }

            $talk = $this->getTalks($claim->talk_id);
            if (isset($talk[0])) {
                $talks[] = $talk[0];
            }
        }

        return $talks;
    }

    /**
     * Get successfully claimed talks by speaker. Optionally collect the name
     * and date of each talk's event.
     *
     * @param integer $speakerId        User ID
     * @param boolean $includeEventInfo Whether to include event information
     *
     * @return array
     */
    public function getSpeakerTalks($speakerId, $includeEventInfo = false)
    {
        $talks = array();

        if ($includeEventInfo) {
            $this->db->select(
                'talk_speaker.*, talks.*, events.event_name,
                events.event_start, events.event_end'
            );
            $this->db->from('talk_speaker');
            $this->db->join('talks', 'talks.id=talk_speaker.talk_id');
            $this->db->join('events', 'talks.event_id = events.ID');
        } else {
            $this->db->select('*');
            $this->db->join('talks', 'talks.id=talk_speaker.talk_id');
            $this->db->from('talk_speaker');
        }
        $this->db->where('speaker_id', $speakerId);
        $this->db->order_by('talks.date_given desc');


        $query = $this->db->get();
        $talks = $query->result();

        // the RID isn't set like the other talk info - lets set it!
        foreach ($talks as $index => $talk) {
            $talks[$index]->rid = $talk->talk_id;
        }

        return $talks;
    }

    /**
     * Retrieves a user's comments
     *
     * @param integer $uid User id
     *
     * @return mixed
     */
    public function getUserComments($uid)
    {
        $sql = sprintf(
            '
            select
                tc.talk_id,
                tc.rating,
                tc.comment,
                tc.date_made,
                tc.active,
                tc.private,
                t.talk_title,
                tc.ID
            from
                talk_comments tc,
                talks t
            where
                tc.talk_id=t.ID and
                tc.user_id=%s
        ', $uid
        );
        $q   = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Find the other events where the session was given
     *
     * @param integer $tid Talk ID
     * @param integer $eid Event id
     *
     * @return array Details on the events (event ID, talk ID, event name)
     */
    public function talkAlsoGiven($tid, $eid)
    {
        $ret         = array();
        $talk_detail = $this->getTalks($tid);

        $speakers = array();
        foreach ($talk_detail[0]->speaker as $speaker) {
            $speakers[] = strtolower($speaker->speaker_name);
        }

        $this->db->select('event_id eid, talks.ID as tid, talk_title, event_name');
        $this->db->from('talks');
        $this->db->join('events', 'events.id=talks.event_id', 'left');
        $this->db->where('talk_title', $talk_detail[0]->talk_title);
        $this->db->where_in('lower(speaker)', $speakers);
        $this->db->where('event_id !=', $eid);
        $q = $this->db->get();

        return $q->result();
    }

    /**
     * Retrieves a talk by its code
     *
     * @param string $code Code
     *
     * @return mixed
     */
    public function getTalkByCode($code)
    {
        $sql = sprintf(
            "
            select 
                talk_title,
                ID,
                concat('ec',
                lpad(ID,2,'0'),
                lpad(event_id,2,'0'),
                substr(md5(talk_title),6,5)) code
            from 
                talks 
            having
                code='%s'
        ", $this->db->escape($code)
        ); //echo $sql;
        $q   = $this->db->query($sql);

        return $q->result();
    }

    /**
     * Links a user to a thing
     *
     * @param integer $uid  User id
     * @param integer $rid  Resource id
     * @param string  $type Resource type
     * @param string  $code Code
     *
     * @return bool
     */
    public function linkUserRes($uid, $rid, $type, $code = null)
    {
        $arr = array(
            'uid'   => $uid,
            'rid'   => $rid,
            'rtype' => $type
        );
        if ($code) {
            $arr['rcode'] = $code;
        }

        //check to be sure its not already claimed first...
        $q   = $this->db->get_where('user_admin', $arr);
        $ret = $q->result();
        if (empty($ret)) {
            $this->db->insert('user_admin', $arr);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Searches for a term
     *
     * @param string  $term  Term to search on
     * @param integer $start Start date
     * @param integer $end   End date
     *
     * @return mixed
     */
    public function search($term, $start, $end)
    {
        $ci = & get_instance();
        $ci->load->model('talk_speaker_model', 'talkSpeaker');
        $term = mysql_real_escape_string($term);

        $this->db->select(
            'talks.*, count(talk_comments.ID) as ccount,
            get_talk_rating(talks.ID) as tavg, events.ID eid, events.event_name'
        );
        $this->db->from('talks');

        $this->db->join('talk_comments', 'talk_comments.talk_id=talks.ID', 'left');
        $this->db->join('events', 'events.ID=talks.event_id', 'left');

        if ($start > 0) {
            $this->db->where('date_given >=', $start);
        }
        if ($end > 0) {
            $this->db->where('date_given <=', $end);
        }

        $term = '%' . $term . '%';
        $this->db->where(
            sprintf(
                '(talk_title LIKE %1$s OR talk_desc LIKE %1$s OR speaker LIKE %1$s)',
                $this->db->escape($term)
            )
        );

        $this->db->limit(10);
        $this->db->group_by('talks.ID');
        $query   = $this->db->get();
        $results = $query->result();

        foreach ($results as $key => $talk) {
            $results[$key]->speaker = $ci->talkSpeaker
                ->getSpeakerByTalkId($talk->ID);
        }

        return $results;
    }

    /**
     * setDisplayFields
     *
     * Method to set the date (and potentially some other fields later) for
     * correct display.  Timezone calculations are needed - call this from a
     * controller before passing data to the view
     *
     * @param array $det the array returned by getTalks
     *
     * @return array the amended array with additional fields
     */
    public function setDisplayFields($det)
    {
        $this->load->library('timezone');

        $retval = array();

        foreach ($det as $talk) {

            // if a timezone is specified, adjust times
            if (!empty($talk->event_tz_cont) && !empty($talk->event_tz_place)) {
                $a = $talk->event_tz_cont . '/' . $talk->event_tz_place;
            } else {
                $a = 'UTC';
            }

            $talk_datetime = $this->timezone
                ->getDatetimeFromUnixtime($talk->date_given, $a);

            // set a datetime string, ignoring talks at midnight and
            // assuming they are without times
            if ($talk_datetime->format('H') != '0') {
                $date_string = 'd.M.Y \a\t H:i';
            } else {
                $date_string = 'd.M.Y';
            }

            // set date, time, and datetime display variables
            $talk->display_date     = $talk_datetime->format('d.m.Y');
            $talk->display_datetime = $talk_datetime->format($date_string);
            $talk->display_time     = $talk_datetime->format('H:i');
            
            // set duration display
            $talk->display_duration = "";
            if ($talk->duration > 0) {
                $hr = floor($talk->duration / 60);
                $min = $talk->duration % 60;
                if ($hr > 0) $talk->display_duration .= "${hr} hr ";
                $talk->display_duration .= "${min} min";
            }

            $retval[] = $talk;
        }

        return $retval;
    }

    /**
     * Determines if the user has claimed a talk
     *
     * @param integer $talk_id Talk id
     * @param integer $user_id Optional user id
     *
     * @return bool
     */
    public function hasUserClaimed($talk_id, $user_id = null)
    {
        if ($user_id == null) {
            $user_id = $this->session->userdata('ID');
        }

        $query  = $this->db
            ->get_where(
                'pending_talk_claims',
                array(
                     'talk_id'    => $talk_id,
                     'speaker_id' => $user_id
                    )
            );
        $claims = $query->result();

        return count($claims) > 0 ? true : false;
    }
}
