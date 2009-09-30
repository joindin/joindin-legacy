<?php

class Talks_model extends Model {

	function Talks_model(){
		parent::Model();
	}
	//---------------
	function deleteTalk($id){
		$this->db->delete('talks',array('ID'=>$id));
	}
	function isTalkClaimed($tid){
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
	function getTalks($tid=null,$latest=false){
		if($tid){
			// See if we have any comments to exclude
			$uids=$this->_findExcludeComments($tid);
			$addl=(!empty($uids)) ? 'and user_id not in ('.implode(',',$uids).')': '';
			$tc_sql=sprintf('
			    (select
				floor(avg(tc.rating))
			    from
				talk_comments tc
			    where
				tc.talk_id=talks.ID %s) as tavg,
			',$addl);
			$sql=sprintf('
				select
					talks.*,
					talks.ID tid,
					events.ID eid,
					events.event_name,
					events.event_tz,
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
					) tcid
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
				$wh=' talks.date_given<='.time().' and ';
				$ob=' order by talks.date_given desc';
			}else{ $wh=''; $ob=''; }
			$sql=sprintf('
				select
					talks.*,
					talks.ID tid,
					events.ID eid,
					events.event_name,
					events.event_tz,
					lang.lang_name,
					lang.lang_abbr,
					count(talk_comments.ID) as ccount,
					(select floor(avg(rating)) from talk_comments where talk_id=talks.ID) as tavg
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
		return $q->result();
	}
	function getTalkComments($tid){
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
				(select username from user where user.ID=tc.user_id) uname
			from
				talk_comments tc
			where
				tc.active=1 and
				tc.talk_id=%s
			order by tc.date_made asc
		',$tid);
		$q=$this->db->query($sql);
		return $q->result();
	}
	function getPopularTalks($len=7){
		$sql=sprintf('
			select
				t.talk_title,
				t.ID,
				count(tc.ID) as ccount,
				(select floor(avg(rating)) from talk_comments where talk_id=t.ID) as tavg,
				e.ID eid,
				e.event_name,
				e.event_tz
			from
				talks t,
				talk_comments tc,
				events e
			where
				tc.talk_id=t.ID and
				e.ID=t.event_id and
				t.active=1
			group by
				t.ID
			order by 
				ccount desc
			limit
				' . $len . '
		');
		$q=$this->db->query($sql);
		return $q->result();
	}
	function getUserTalks($uid){
		$talks=array();
		//select rid from user_admin where uid=$uid and rtype='talks'
		$this->db->select('*');
		$this->db->from('user_admin');
		$this->db->where('uid',$uid);
		$this->db->where('rtype','talk');
		$this->db->where('rcode !=','pending');
		
		$q=$this->db->get();
		//$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rtype'=>'talk'));
		$ret=$q->result();
		foreach($ret as $k=>$v){ 
			$t=$this->getTalks($v->rid);
			if(isset($t[0])){ $talks[]=$t[0]; }
		}
		return $talks;
	}
	function getUserComments($uid){
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
	function getTalkByCode($code){
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
	function linkUserRes($uid,$rid,$type,$code=null){		
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
	function search($term,$start,$end){
		$this->db->select('talks.*, count(talk_comments.ID) as ccount, (select floor(avg(rating)) from talk_comments where talk_id=talks.ID) as tavg, events.ID eid, events.event_name, events.event_tz');
	    $this->db->from('talks');
	    
	    $this->db->join('talk_comments', 'talk_comments.talk_id=talks.ID', 'left');
		$this->db->join('events', 'events.ID=talks.event_id', 'left');
	    
		if($start>0){ $this->db->where('date_given>='.$start); }
		if($end>0){ $this->db->where('date_given<='.$end); }
		
		$this->db->like('talk_title',$term);
		$this->db->or_like('talk_desc',$term);
		$this->db->or_like('speaker',$term);
		$this->db->limit(10);
		$this->db->group_by('talks.ID');
		$q=$this->db->get();
		return $q->result();
	}
	//---------------
	function _findExcludeComments($tid){
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
}
?>