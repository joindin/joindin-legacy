<?php

class Event_model extends Model {

	function Event_model(){
		parent::Model();
	}
	/**
	 * Match all data given against the events table to see 
	 * is there's anything matching
	 */
	function isUnique($data){
		$q=$this->db->get_where('events',$data);
		$ret=$q->result();
		return (empty($ret)) ? true : false;
	}
	/**
	 * Check the given string to see if it already exists
	 * $pid is an optional event ID
	 */
	function isUniqueStub($str,$eid=null){
		$this->db->select('ID')
			->from('events')
			->where('event_stub',$str);
		if($eid){ $this->db->where('ID !=',$eid); }
		
		$q=$this->db->get();
		$ret=$q->result();
		return (empty($ret)) ? true : false;
	}
	//---------------------
	function deleteEvent($id){
		//we don't actually delete them...just make them inactive
		//get the event
		//$this->db->where('ID',$id);
		//$this->db->update('events',array('active'=>0,'pending'=>0));
		
		// No mercy!
		$this->db->delete('events',array('ID'=>$id));
		
		$this->deleteEventTalks($id);
		$this->deleteTalkComments($id);
	}
	/**
	 * Remove the talks related to an event ID
	 */
	function deleteEventTalks($eid){
		$this->db->where('event_id',$eid);
		$this->db->update('talks',array('active'=>0));
	}
	/**
	 * Remove the comments related to all of the talks on an event
	 * (useful for cleanup)
	 */
	function deleteTalkComments($eid){
		$talks=$this->getEventTalks($eid);
		foreach($talks as $k=>$v){
			$this->db->where('talk_id',$v->ID);
			$this->db->update('talk_comments',array('active'=>0));
		}
	}
	//---------------------
	
	/**
	 * Sets the Active and Pending statuses to make the event show correctly
	 */
	function approvePendingEvent($id){
		$arr=array(
			'active'	=> 1,
			'pending'	=> 0
		);
		$this->db->where('ID',$id);
		$this->db->update('events',$arr);
	}
	
	//---------------------

	function getDayEventCounts($year, $month)
	{
    	$start	= mktime(0,  0, 0, $month, 1,                 $year);
		$end	= mktime(23,59,59, $month, date('t', $start), $year);

		$events = $this->getEventDetail(null, $start, $end);

	    $dates = array();

        foreach ($events as $v) {
        	$tsStart = mktime(0, 0, 0, date('m', $v->event_start), date('d', $v->event_start), date('Y', $v->event_start));
        	$tsEnd   = mktime(0, 0, 0, date('m', $v->event_end), date('d', $v->event_end), date('Y', $v->event_end));
        	$secDay = 60*60*24;

        	for ($i = $tsStart;$i <= $tsEnd && $i <= $end;$i += $secDay) {
        	    $d = date('Y-m-d', $i);
        	    if (!isset($dates[$d])) {
        	        $dates[$d] = 0;
        	    }
        	    $dates[$d]++;
        	}
        }

        return $dates;
	}

	/**
	 * Returns the details for a specific event, a series within a given date range, or all events if
	 * no arguments have been provided.
	 *
	 * @param integer $id
	 * @param integer $start_dt
	 * @param integer $end_dt
	 * @param bool 		$pending	Show only pending events, or only active
	 *
	 * @return stdClass[]
	 */
	function getEventDetail($id = null, $start_dt = null, $end_dt = null, $pending = false){
		$this->load->helper("events");

		// get the current date (with out time)
		$now 											= mktime(0, 0, 0);
		$day_in_seconds 					= 86400;
		$closing_days_in_seconds 	= 90 * $day_in_seconds;

		/** @var CI_DB_active_record $db  */
		$db = $this->db;

		// select all events, return whether they are allowed to comment (start -1 days till start + 90 days) and count
		// attendees and comments
		$db->select(<<<SQL
			events.*,
			IF((((events.event_start - $day_in_seconds) < $now) AND ((events.event_start + $closing_days_in_seconds) > $now)), 1, 0) AS allow_comments,
			COUNT(DISTINCT user_attend.ID) AS num_attend,
			COUNT(DISTINCT event_comments.ID) AS num_comments
SQL
				, false)->
			from('events')->
			join('user_attend', 'user_attend.eid=events.ID', 'left')->
			join('event_comments', 'event_comments.event_id=events.ID', 'left')->
			group_by('events.ID');

		// if the user is not an admin or $id is not null, limit the results based on the pending state
		if(!$this->user_model->isSiteAdmin() || ($id !== null)) {
			if ($pending) {
				$db->where('(events.active', 0)->
					where('events.pending', 1)->
					ar_where[] = ')';
			} else {
				$db->where('(events.active', 1)->
					where('(events.pending', null)->
					or_where('events.pending', 0)->
					ar_where[] = '))';
			}
		}

		// determine the selection criteria, if $id is a number use that, otherwise limit based on start and end date
		if (is_numeric($id)) {
			$db->where('events.ID', $id);
		} elseif (($end_dt !== null) && ($start_dt !== null)) {
			// check whether the event start and end overlaps with the given date range
			$db->where('events.event_start <=', $end_dt);
			$db->where('events.event_end >=', $start_dt);
			$db->order_by('events.event_start', 'DESC');
		}

		// retrieve the resultset
		$q 		= $db->get();
		$res	= $q->result();

		// Decorate results with "event is on now" flag
		if (is_array($res)) {
			foreach($res as &$event) {
				if (!is_object($event)) {
					continue;
				}

				$event->now 						= (event_isNowOn($event->event_start, $event->event_end)) ? "now" : "";
				$event->timezoneString	= $event->event_tz_cont.'/'.$event->event_tz_place;
			}
		}

		return $res;
	}

	function getEventTalks($id,$includeEventRelated = true, $includePrivate = false) {
		$this->load->helper("events");
		$this->load->helper("talk");
		$private=($includePrivate) ? '' : ' and private!=1';
		$sql='
			select
				talks.talk_title,
				talks.speaker,
				talks.slides_link,
				talks.date_given,
				talks.event_id,
				talks.ID,
				talks.talk_desc,
				events.event_tz_cont,
				events.event_tz_place,
				events.event_start,
				events.event_end,
				(select l.lang_abbr from lang l where talks.lang=l.ID) lang,
				(select round(avg(rating)) from talk_comments where talk_id=talks.ID) rank,
				(select count(rating) from talk_comments where talk_id=talks.ID '.$private.') comment_count,
				ifnull(categories.cat_title, \'Talk\') tcid
			from
				talks
			inner join lang on (lang.ID = talks.lang)
			inner join events on events.ID = talks.event_id
			left join talk_cat on talks.ID = talk_cat.talk_id
			left join categories on talk_cat.cat_id = categories.ID
			where
				';
		if(!$includeEventRelated) {
			$sql .= 'categories.cat_title <> "Event Related" and
			';
		}
		$sql .= sprintf('
				event_id=%s and
				talks.active=1
			order by
				talks.date_given asc, talks.speaker asc
		',$id);
		$q=$this->db->query($sql);
		$res = $q->result();

		// Loop through the talks deciding if they are currently on
		if (is_array($res) && count($res) > 0 && is_object($res[0]) && event_isNowOn($res[0]->event_start, $res[0]->event_end)) {
			$res = talk_listDecorateNowNext($res);
		}
		
		$CI=&get_instance();
		$CI->load->model('talk_speaker_model','tsm');
		foreach($res as $k=>$talk){
			$res[$k]->speaker=$CI->tsm->getTalkSpeakers($talk->ID);
		}

		return $res;
	}

	function getEventsOfType($type, $limit = NULL) {
		$where = NULL;
		$order_by = NULL;

		if($type == "hot") {
			$order_by = "(num_attend - score) desc";
		}

		if($type == "upcoming") {
			$order_by = "events.event_start asc";
			$where = '(events.event_start>='.mktime(0,0,0).')';
		}

		if($type == "past") {
			$where = '(events.event_end < '.mktime(0,0,0).')';
			$order_by = "events.event_start desc";
		}

		$result = $this->getEvents($where, $order_by, $limit);
		return $result;
	}

	public function getEvents($where=NULL, $order_by = NULL, $limit = NULL) {
		$sql = 'SELECT * ,
			(select count(*) from user_attend where user_attend.eid = events.ID) as num_attend,
			(select count(*) from event_comments where event_comments.event_id = events.ID) as num_comments, abs(0) as user_attending, '
		  			.' abs(datediff(from_unixtime(events.event_start), from_unixtime('.mktime(0,0,0).'))) as score,
              CASE 
                WHEN (((events.event_start - 86400) < '.mktime(0,0,0).') and (events.event_start + (3*30*3600*24)) > '.mktime(0,0,0).') THEN 1
                ELSE 0
                END as allow_comments
			FROM events
			WHERE active = 1 AND (pending = 0 OR pending = NULL)';

		if($where) {
			$sql .= ' AND (' . $where . ')';
		}

		if($order_by) {
			$sql .= ' ORDER BY ' . $order_by;
		}

		if($limit) {
			$sql .= ' LIMIT ' . $limit;
		}

	    $query = $this->db->query($sql);
	    return $query->result();
	}

    function getHotEvents($limit = null){
		$result = $this->getEventsOfType("hot", $limit);
		return $result;
	}

	function getUpcomingEvents($limit = null, $inc_curr = false){
		// inc_curr not handled

		$result = $this->getEventsOfType("upcoming", $limit);
		return $result;
	}
	
    function getPastEvents($limit = null){
		$result = $this->getEventsOfType("past", $limit);
		return $result;
	}

	function getEventAdmins($eid){
	    $sql=sprintf("
		select
		    u.username,
			u.full_name,
		    u.email,
			u.ID
		from
		    user_admin ua,
		    user u,
		    events e
		where
		    e.ID=%s and
		    ua.rtype='event' and
		    ua.rid=e.ID and
		    u.ID=ua.uid
	    ",$eid);
	    $q=$this->db->query($sql);
	    return $q->result();
	}

	function getLatestComment($eid){
	    $sql=sprintf("
		select
		    max(tc.date_made) max_date,
		    tc.ID
		from
		    talks t,
		    talk_comments tc
		where
		    t.event_id=%s and
		    tc.talk_id=t.ID
		group by
		    t.event_id
	    ",$eid);
	    $q=$this->db->query($sql);
	    return $q->result();
	}
	
	function getEventIdByName($name){
		$q=$this->db->get_where('events',array('event_stub'=>$name));
		return $q->result();
	}
	function getEventIdByTitle($title){
		$this->db->select('id');
		$this->db->from('events');
		$this->db->where("lower(event_name)",strtolower($title));
		$q=$this->db->get();
		return $q->result();
	}
	
	function getEventClaims($event_id){
		$sql=sprintf('
			select
				t.id as talk_id,
				t.talk_title,
				ua.uid as user_id,
				ua.rid,
				u.full_name,
				ua.rcode
			from
				user_admin ua,
				events e,
				talks t,
				user u
			where
				ua.rid=t.id and
				e.id=t.event_id and
				u.id=ua.uid and
				e.id = %s
		',$event_id);
		$q=$this->db->query($sql);
		$ret=$q->result();
		
		return $ret;
	}
	
	function getClaimedTalks($eid, $talks = null){
		$this->load->helper('events');
		$ids	= array();
		$tdata	= array();

		// Find all of the talks for the event...
		if ($talks === null) {
			$ret = $this->getEventTalks($eid); //echo '<pre>'; print_r($ret); echo '</pre>';
		} else {
			$ret = $talks;
		}
		foreach($ret as $k=>$v){
			$codes=array();
			/*
			$p=explode(',',$v->speaker);
			$codes=array();
			foreach($p as $ik=>$iv){
				$codes[]=buildCode($v->ID,$v->event_id,$v->talk_title,trim($iv));
			}
			*/
			
			$tdata[$v->ID]=array(
				'talk_title'=> $v->talk_title,
				'event_id'	=> $v->event_id,
				'speaker'	=> $v->speaker,
				'codes'		=> $codes
			);
			$ids[]=$v->ID; 
		}

		// Now find the users that are in the user_admin take
		// and try to match them up...
		$uids=implode(',',$ids);
		if(empty($uids)){ return array(); }
		$sql=sprintf('
			select
				ua.uid,
				ua.rid,
				ua.rtype,
				ua.ID,
				ua.rcode,
				u.email
			from
				user_admin ua,
				user u
			where
				ua.uid=u.ID and 
				ua.rid in (%s)
		',$uids);
		$q=$this->db->query($sql);
		$ret=$q->result();
		foreach($ret as $k=>$v){ 
			$ret[$k]->tdata=$tdata[$v->rid];
		}

		// This gives us a return array of all of the claimed talks
		// for the this event
		return $ret;
	}
	function getEventFeedback($eid, $order_by = NULL){
		// handle the ordering
		if($order_by == 'tc.date_made' || $order_by == 'tc.date_made DESC') {
			// fine, sensible options that we'll allow
		} else {
			// if null, or indeed anything else, order by talk id (the original default)
			$order_by = 't.ID';
		}

		$sql=sprintf('
			select
				t.talk_title,
				t.speaker,
				t.date_given,
				tc.date_made,
				tc.rating,
				tc.comment,
				u.full_name
			from
				talks t,
				talk_comments tc
			LEFT JOIN user u ON (u.ID = tc.user_id)
			where
				tc.private <> 1 AND
				t.ID=tc.talk_id AND
				t.event_id=%s
			order by
		'.$order_by,$eid);
		$q=$this->db->query($sql);
		return $q->result();
	}
	function getEventRelatedSessions($id) {
		$sql=sprintf('
			select
				talks.talk_title,
				talks.speaker,
				talks.slides_link,
				talks.date_given,
				talks.event_id,
				talks.ID,
				talks.talk_desc,
				events.event_tz_cont,
				events.event_tz_place,
				(select l.lang_abbr from lang l where talks.lang=l.ID) lang,
				(select round(avg(rating)) from talk_comments where talk_id=talks.ID) rank,
				(select count(rating) from talk_comments where talk_id=talks.ID) comment_count,
				ifnull(categories.cat_title, \'Talk\') tcid
			from
				talks
			inner join lang on (lang.ID = talks.lang)
			inner join events on events.ID = talks.event_id
			left join talk_cat on talks.ID = talk_cat.talk_id
			left join categories on talk_cat.cat_id = categories.ID
			where
				categories.cat_title = "Event Related" and
				event_id=%s and
				talks.active=1
			order by
				talks.date_given asc, talks.speaker asc
		',$id);
		$q=$this->db->query($sql);
		return $q->result();
	}

	//----------------------
	function search($term,$start,$end){
		$arr=array();
		
		//if we have the dates, limit by them
		$attend = '(SELECT COUNT(*) FROM user_attend WHERE eid = events.ID AND uid = ' . (int)$this->session->userdata('ID') . ')as user_attending';

		$this->db->select('events.*, COUNT(user_attend.ID) AS num_attend, COUNT(event_comments.ID) AS num_comments, ' . $attend);
		$this->db->from('events');
		$this->db->join('user_attend', 'user_attend.eid = events.ID', 'left');
		$this->db->join('event_comments', 'event_comments.event_id = events.ID', 'left');
		
		if($start>0){ $this->db->where('event_start >='.$start); }
		if($end>0){ $this->db->where('event_start <='.$end); }

		$this->db->like('event_name',$term);
		$this->db->or_like('event_desc',$term);
		$this->db->limit(10);
		$this->db->group_by('events.ID');

		$q=$this->db->get();
		return $q->result();
	}
}

?>
