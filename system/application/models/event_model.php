<?php

class Event_model extends Model {

	function Event_model(){
		parent::Model();
	}
	//---------------------
	function deleteEvent($id){
		//we don't actually delete them...just make them inactive
		//get the event
		$this->db->where('ID',$id);
		$this->db->update('events',array('active'=>0));
		
		$this->deleteEventTalks($id);
		$this->deleteTalkComments($id);
	}
	function deleteEventTalks($eid){
		$this->db->where('event_id',$eid);
		$this->db->update('talks',array('active'=>0));
	}
	function deleteTalkComments($eid){
		$talks=$this->getEventTalks($eid);
		foreach($talks as $k=>$v){
			$this->db->where('talk_id',$v->ID);
			$this->db->update('talk_comments',array('active'=>0));
		}
	}
	//---------------------
	function getEventDetail($id=null){
		if($id){
			$q=$this->db->get_where('events',array('ID'=>$id,'active'=>1));
		}else{
			$q=$this->db->get_where('events',array('active'=>1));
		}
		return $q->result();
	}
	function getEventTalks($id){
		$sql=sprintf('
			select
				talk_title,
				speaker,
				slides_link,
				date_given,
				event_id,
				ID,
				talk_desc,
				(select floor(avg(rating)) from talk_comments where talk_id=talks.ID) rank,
				(select 
					cat.cat_title
				from 
					talk_cat tac,categories cat 
				where 
					tac.talk_id=talks.ID and tac.cat_id=cat.ID
				) tcid
			from
				talks
			where
				event_id=%s and
				active=1
			order by
				date_given desc
		',$id);
		$q=$this->db->query($sql);
		return $q->result();
	}
	function getUpcomingEvents($inc_curr=false){
		$this->db->from('events');
		$this->db->where('event_start>=',time());
		if($inc_curr){ $this->db->or_where('event_end>=',time()); }
		$this->db->order_by('event_start','desc');
		$this->db->limit(10);
		$q=$this->db->get();
		return $q->result();
	}
	function getEventIdByName($name){
		$q=$this->db->get_where('events',array('event_stub'=>$name));
		return $q->result();
	}
	function getClaimedTalks($eid){
		$ids=array();
		$ret=$this->getEventTalks($eid); //echo '<pre>'; print_r($ret); echo '</pre>';
		foreach($ret as $k=>$v){ $ids[]=$v->ID; }
		
		$uids=implode(',',$ids);
		if(empty($uids)){ return array(); }
		$sql=sprintf('
			select
				ua.uid,
				ua.rid,
				ua.rtype,
				ua.ID,
				u.email
			from
				user_admin ua,
				user u
			where
				ua.uid=u.ID and 
				ua.rid in (%s)
		',$uids);
		$q=$this->db->query($sql);
		return $q->result();
	}
	//----------------------
	function search($term,$start,$end){
		$arr=array();
		
		//if we have the dates, limit by them
		
		$this->db->from('events');
		if($start>0){ $this->db->where('event_start>='.$start); }
		if($end>0){ $this->db->where('event_start<='.$end); }
		
		$this->db->like('event_name',$term);
		$this->db->or_like('event_desc',$term);
		$this->db->limit(10);
		$q=$this->db->get();
		return $q->result();
	}
}

?>