<?php

class Talks_model extends Model {

	function Talks_model(){
		parent::Model();
	}
	//---------------
	public function deleteTalk($id){
		$this->db->delete('talks',array('ID'=>$id));
	}
	public function isTalkClaimed($tid){
		$sql=sprintf('
			select
				u.username,
				u.email,
				ua.uid,
				ua.rid,
				ua.rcode,
				u.ID userid,
				t.talk_title,
				t.event_id,
				t.speaker
			from
				user u,
				user_admin ua,
				talks t
			where
				u.ID=ua.uid and
				ua.rid=%s and
				ua.rcode!=\'pending\' and
				t.ID=ua.rid
		',$tid);
		$q=$this->db->query($sql);
		$ret=$q->result();
		//echo '<pre>'; print_r($ret); echo '</pre>';
		foreach($ret as $k=>$v){
			$codes=array(); $speakers=array();
			foreach(explode(',',$v->speaker) as $ik=>$iv){
				$codes[]=buildCode($v->rid,$v->event_id,$v->talk_title,trim($iv));
				$speakers[]=trim($iv);
			}
			$ret[$k]->codes=$codes;
			$ret[$k]->speakers=$speakers;
		}
		return $ret;
	}
	
	//---------------
	// Check to see if user has already made that sort of 
	// comment on the talk
	public function hasUserCommented($tid,$uid,$type=null){
		$arr=array('user_id'=>$uid,'talk_id'=>$tid);
		if($type){ $arr['comment_type']=$type; }
		$q=$this->db->get_where('talk_comments',$arr);
		$ret=$q->result();
		return (isset($ret[0])) ? true : false;
	}
	
	//---------------
	public function getTalks($tid=null,$latest=false){
		$this->load->helper("events");
		$this->load->helper("talk");
		if($tid){
			// See if we have any comments to exclude
			$uids=$this->_findExcludeComments($tid);
			$addl=(!empty($uids)) ? 'and user_id not in ('.implode(',',$uids).')': '';
			$tc_sql=sprintf('
			    (select
				round(avg(tc.rating))
			    from
				talk_comments tc
			    where
				tc.talk_id=talks.ID %s) as tavg,
			',$addl);
			$sql=sprintf('
				select
					talks.*,
					CASE 
						WHEN (((talks.date_given - 86400) < '.mktime(0,0,0).') and (talks.date_given + (3*30*3600*24)) > '.mktime(0,0,0).') THEN 1
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
					count(talk_comments.ID) as ccount,
					%s
					(select 
						cat.cat_title
					from 
						talk_cat tac,categories cat
					where 
						tac.talk_id=talks.ID and tac.cat_id=cat.ID
					) tcid,
					(select max(date_made) from talk_comments where talk_id=talks.ID) last_comment_date
				from
					talks
				left join talk_comments on (talk_comments.talk_id = talks.ID)
				inner join events on (events.ID = talks.event_id)
				inner join lang on (lang.ID = talks.lang)
				where
					talks.ID=%s and
					talks.active=1
				group by
					talks.ID
			',$tc_sql,$tid);
			$q=$this->db->query($sql);
		}else{
			if($latest){ 
				$wh=' talks.date_given<='.mktime(0,0,0).' and ';
				$ob=' order by talks.date_given desc';
			}else{ $wh=''; $ob=''; }
			$sql=sprintf('
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
					(select 
						round(avg(rating)) 
					from 
						talk_comments 
					where talk_id=talks.ID) as tavg,
					(select max(date_made) from talk_comments where talk_id=talks.ID) last_comment_date
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
			',$wh,$ob);
			$q=$this->db->query($sql);
		}
		$res = $q->result();
		
		$CI=&get_instance();
		$CI->load->model('talk_speaker_model','tsm');
		foreach($res as $k=>$talk){
			$res[$k]->speaker=$CI->tsm->getTalkSpeakers($talk->ID);
		}

		return $res;
	}
	/**
	* Gets the comments for a session/talk
	* $tid Talk ID
	* $cid [optional] Comment ID (if you want to get only one comment)
	*/
	public function getTalkComments($tid,$cid=null,$private=false){
		$c_addl	= ($cid) ? ' and tc.ID='.$cid : '';
		$priv	= (!$private) ? ' and tc.private=0' : '';
		$sql=sprintf('
			select
				tc.talk_id,
				tc.rating,
				tc.comment,
				tc.date_made,
				tc.ID,
				tc.private,
				tc.active,
				tc.user_id,
				(select username from user where user.ID=tc.user_id) uname,
				tc.comment_type
			from
				talk_comments tc
			where
				tc.active=1 and
				tc.talk_id=%s %s %s
			order by tc.date_made asc
		',$tid,$c_addl,$priv);
		$q=$this->db->query($sql);
		return $q->result();
	}
	
	public function getPopularTalks($len=7){
		$sql=sprintf('
			select
				t.talk_title,
				t.ID,
				count(tc.ID) as ccount,
				round(avg(tc.rating)) as tavg,
				e.ID eid,
				e.event_name
			from
				talks t
			JOIN talk_comments tc
			ON tc.talk_id=t.ID
			JOIN events e
			ON e.ID=t.event_id
			where
				t.active=1
			group by
				t.ID
			order by
				ccount desc
			limit '.$len.'
		');
		$q=$this->db->query($sql);
		return $q->result();
	}
	
	public function getRecentTalks(){
		$sql=sprintf("
			select
			  DISTINCT t.ID,
			  t.talk_title,
			  count(tc.ID) as ccount,
			  round(avg(tc.rating)) as tavg,
			  e.ID eid,
			  e.event_name,
			  e.event_start
			from
			  talks t
			  JOIN events e
			    ON e.ID=t.event_id
			  JOIN talk_comments tc
			    ON tc.talk_id=t.ID
			  INNER JOIN user_admin ua
			    ON t.ID = ua.rid
			WHERE
			    e.event_start > %s
			  and
			    ua.rtype = 'talk'
			  and
				ua.rcode != 'pending'
			group by
			  t.ID
			having
			  tavg>3 and ccount>3
		",strtotime('-3 months'));
		$q=$this->db->query($sql);
		return $q->result();
	}
	
	public function getUserTalks($uid){
		$talks=array();
		//select rid from user_admin where uid=$uid and rtype='talks'
		$this->db->select('*');
		$this->db->from('user_admin');
		$this->db->join('talks','talks.id=user_admin.rid');
		$this->db->where('uid',$uid);
		$this->db->where('rtype','talk');
		$this->db->where('rcode !=','pending');
		$this->db->order_by('talks.date_given desc');
		
		$q=$this->db->get();
		//$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rtype'=>'talk'));
		$ret=$q->result();
		foreach($ret as $k=>$v){ 
			$t=$this->getTalks($v->rid);
			if(isset($t[0])){ $talks[]=$t[0]; }
		}
		return $talks;
	}
	
	public function getUserComments($uid){
		$sql=sprintf('
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
		',$uid);
		$q=$this->db->query($sql);
		return $q->result();
	}
	
	public function getTalkEvent($tid){
		$q	 = $this->db->query('select event_id from talks where id='.$tid);
		$ret = $q->result();
		return (isset($ret['event_id'])) ? $ret['event_id'] : false;
	}
	
	/**
	 * Find the other events where the session was given
	 *
	 * @param $tid integer Talk ID
	 * @return array Details on the events (event ID, talk ID, event name)
	 */
	public function talkAlsoGiven($tid,$eid){
		$ret		= array();
		$talk_detail= $this->getTalks($tid);
		
		$speakers=array();
		foreach($talk_detail[0]->speaker as $speaker){
			$speakers[]=strtolower($speaker->speaker_name);
		}
		
		$this->db->select('event_id eid, talks.ID as tid, talk_title, event_name');
	    $this->db->from('talks');
		$this->db->join('events','events.id=talks.event_id','left');
	    $this->db->where('talk_title',$talk_detail[0]->talk_title);
		$this->db->where_in('lower(speaker)',$speakers);
		$this->db->where('event_id !='.$eid);
	    $q=$this->db->get();
	    return $q->result();
	}
	
	public function getTalkByCode($code){
		//$str='ec'.str_pad($v->ID,2,0,STR_PAD_LEFT).str_pad($v->event_id,2,0,STR_PAD_LEFT);
		//$str.=substr(md5($v->talk_title),5,5);
		
		$sql=sprintf("
			select 
				talk_title,
				ID,
				concat('ec',lpad(ID,2,'0'),lpad(event_id,2,'0'),substr(md5(talk_title),6,5)) code 
			from 
				talks 
			having
				code='%s'
		",$code); //echo $sql;
		$q=$this->db->query($sql);
		return $q->result();
	}
	
	/**
	 * Find users with popular talks that are also in upcoming events
	 */
	public function getPopularUpcomingTalks($rating=4,$rand=true){
		$this->CI=&get_instance();
		$this->CI->load->model('event_model','em');
		$this->CI->load->model('talks_model','tm');
		$events = $this->CI->em->getUpcomingEvents(null);
		$ret 	= array();
		
		foreach($events as $e){
			$sql=sprintf('
				select
					u.ID
				from
					user u
				where
					u.ID in (
						select
							ua.uid
						from
							talks t, user_admin ua
						where
							t.event_id=%s and ua.rid=t.ID
					)
			',$e->ID);
			$q=$this->db->query($sql);
			$claimed_users=$q->result();
			//var_dump($claimed_users);
			
			// Now, for these users, lets find ones that have good ratings
			foreach($claimed_users as $u){
				$sql=sprintf("
					select 
						(select 
							round(avg(tcs.rating)) rate 
						from 
							talk_comments tcs 
						where 
							tcs.talk_id=t.ID
						having rate>=%s) rating,
						t.ID,
						t.talk_title,
						t.speaker
					from 
						talks t
					where 
						t.ID in (
							select
								ua.rid
							from
								user_admin ua
							where
								ua.uid=%s and ua.rcode!='pending'
						)
					having
						rating>=%s
				",$rating,$u->ID,$rating);
				$q=$this->db->query($sql);
				$ratings=$q->result();
				foreach($ratings as $v){ $ret[]=$v; }
			}			
		}
		if($rand){ 
			$tmp=array();
			if(count($ret)>0){
				$max=(count($ret)<5) ? count($ret)-1 : 5;
				$rand=array_rand($ret,$max);
				foreach($rand as $r){ $tmp[]=$ret[$r]; }
			}
			return $tmp;
		}else{ return $ret; }
	}
	
	public function linkUserRes($uid,$rid,$type,$code=null){		
		$arr=array(
			'uid'	=> $uid,
			'rid'	=> $rid,
			'rtype'	=> $type
		);
		if($code){ $arr['rcode']=$code; }
		
		//check to be sure its not already claimed first...
		$q=$this->db->get_where('user_admin',$arr);
		$ret=$q->result();
		if(empty($ret)){
			$this->db->insert('user_admin',$arr);
			return true;
		}else{ return false; }
	}

	//---------------
	public function search($term,$start,$end){
		$this->db->select('talks.*, count(talk_comments.ID) as ccount, (select round(avg(rating)) from talk_comments where talk_id=talks.ID) as tavg, events.ID eid, events.event_name');
	    $this->db->from('talks');
	    
	    $this->db->join('talk_comments', 'talk_comments.talk_id=talks.ID', 'left');
		$this->db->join('events', 'events.ID=talks.event_id', 'left');
	    
		if($start>0){ $this->db->where('date_given >='.$start); }
		if($end>0){ $this->db->where('date_given <='.$end); }
		
		$this->db->like('talk_title',$term);
		$this->db->or_like('talk_desc',$term);
		$this->db->or_like('speaker',$term);
		$this->db->limit(10);
		$this->db->group_by('talks.ID');
		$q=$this->db->get();
		return $q->result();
	}
	//---------------
	public function _findExcludeComments($tid){
	    $uid=array();
	    
	    // See if there's any speaker claims for the talk
	    $this->db->select('uid,rid,ID');
	    $this->db->from('user_admin');
	    $this->db->where('rid',$tid);
	    $this->db->where('rtype','talk');
	    $q=$this->db->get();
	    $ret=$q->result();
	    if($ret){ foreach($ret as $k=>$v){ $uid[]=$v->uid; } }

	    return $uid;
	}


	/**
	 * setDisplayFields 
	 *
	 * Method to set the date (and potentially some other fields later) for
	 * correct display.  Timezone calculations are needed - call this from a 
	 * controller before passing data to the view
	 * 
	 * @param array $det the array returned by getTalks
	 * @access public
	 * @return the amended array with additional fields
	 */
	public function setDisplayFields($det) {
		$retval = array();

		foreach($det as $talk) {
			// create datetime object
			$talk_datetime = new DateTime("@{$talk->date_given}");

			// if a timezone is specified, adjust times
			if(!empty($talk->event_tz_cont) && !empty($talk->event_tz_place)) {
				$event_timezone = new DateTimeZone($talk->event_tz_cont . '/' . $talk->event_tz_place);
			} else {
				$event_timezone = new DateTimeZone('UTC');
			}
			$talk_datetime->setTimezone($event_timezone);


			// How much wrong will ->format("U") be if I do it now, due to DST changes?
			// Only needed until PHP Bug #51051 delivers a better method
			$unix_offset1 = $event_timezone->getOffset($talk_datetime);
			$unix_offset2 = $event_timezone->getOffset(new DateTime());
			$unix_correction = $unix_offset1 - $unix_offset2;


			// create datetime object corrected for DST offset
			$timestamp = $talk->date_given + $unix_correction;
			$talk_datetime = new DateTime("@{$timestamp}");
			$talk_datetime->setTimezone($event_timezone);



			// set a datetime string, ignoring talks at midnight and assuming they are without times
			if($talk_datetime->format('H') != '0') { 
				$date_string = 'M j, Y \a\t H:i'; 
			} else { 
				$date_string = 'M j, Y'; 
			}

			// set date, time, and datetime display variables
			$talk->display_date = $talk_datetime->format('M j, Y');
			$talk->display_datetime = $talk_datetime->format($date_string);
			$talk->display_time = $talk_datetime->format('H:i');

			$retval[] = $talk;
		}
		return $retval;
	}
}
?>
