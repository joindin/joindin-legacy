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
				u.ID userid
			from
				user u,
				user_admin ua
			where
				u.ID=ua.uid and
				ua.rid=%s
		',$tid);
		$q=$this->db->query($sql);
		return $q->result();
	}
	//---------------
	function getTalks($tid=null,$latest=false){
		if($tid){
			$sql=sprintf('
				select
					t.talk_title,
					t.speaker,
					t.ID as tid,
					e.ID eid,
					t.slides_link,
					t.date_given,
					t.event_id,
					t.talk_desc,
					l.lang_name,
					l.lang_abbr,
					t.lang,
					e.event_name,
					e.event_tz,
					(select floor(avg(tc.rating)) from talk_comments tc where tc.talk_id=t.ID) as tavg,
					(select 
						cat.cat_title
					from 
						talk_cat tac,categories cat
					where 
						tac.talk_id=t.ID and tac.cat_id=cat.ID
					) tcid
				from
					talks t,
					events e,
					lang l
				where
					t.ID=%s and
					e.ID=t.event_id and
					t.active=1 and
					l.ID=t.lang
			',$tid);
			$q=$this->db->query($sql);
		}else{
			if($latest){ 
				$wh=' date_given<='.time().' and ';
				$ob=' order by date_given desc';
			}else{ $wh=''; $ob=''; }
			$sql=sprintf('
				select
					talk_title,
					speaker,
					slides_link,
					date_given,
					event_id,
					ID,
					talk_desc,
					lang_name,
					lang_abbr,
					lang,
					(select floor(avg(rating)) from talk_comments where talk_id=talks.ID) as tavg,
					(select event_name from events where events.ID=talks.event_id) as ename
				from
					talks,lang
				where
					%s
					lang.ID=talks.ID and
					active=1
				%s
			',$wh,$ob);
			$q=$this->db->query($sql);
		}
		return $q->result();
	}
	function getTalkComments($tid){
		/*
		$this->db->from('talk_comments');
		$this->db->where('talk_id',$tid);
		$this->db->where('active','1');
		$this->db->order_by('date_made','desc');
		$q=$this->db->get();
		*/
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
	function getPopularTalks($len=5){
		$sql=sprintf('
			select
				t.talk_title,
				t.ID,
				count(tc.ID) as ccount,
				(select floor(avg(rating)) from talk_comments where talk_id=t.ID) as tavg
			from
				talks t,
				talk_comments tc
			where
				tc.talk_id=t.ID and
				t.active=1
			group by
				t.ID
			order by 
				ccount desc
			limit
				7
		');
		$q=$this->db->query($sql);
		return $q->result();
	}
	function getUserTalks($uid){
		$talks=array();
		//select rid from user_admin where uid=$uid and rtype='talks'
		$q=$this->db->get_where('user_admin',array('uid'=>$uid,'rtype'=>'talk'));
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
	function linkUserRes($uid,$rid,$type){
		$arr=array(
			'uid'	=> $uid,
			'rid'	=> $rid,
			'rtype'	=> $type
		);
		$this->db->insert('user_admin',$arr);
	}
	//---------------
	function search($term,$start,$end){
		$this->db->from('talks');
		if($start>0){ $this->db->where('date_given>='.$start); }
		if($end>0){ $this->db->where('date_given<='.$end); }
		
		$this->db->like('talk_title',$term);
		$this->db->or_like('talk_desc',$term);
		$this->db->or_like('speaker',$term);
		$this->db->limit(10);
		$q=$this->db->get();
		return $q->result();
	}
}
?>