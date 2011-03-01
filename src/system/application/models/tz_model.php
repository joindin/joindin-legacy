<?php

class Tz_model extends CI_Model {
	//--------------------
	public function getContInfo($cid=null){
		$this->db->select('cont')
			->distinct()
			->from('tz')
			->order_by('cont asc');
		$q=$this->db->get();
		return $q->result();
	}
	public function getAreaInfo($cont){
		$this->db->select('area')
			->distinct()
			->where("cont='".$cont."'")
			->from('tz')
			->order_by('cont asc');
		$q=$this->db->get();
		return $q->result();
	}
	public function getOffsetInfo(){
		$this->db->select('offset')
			->distinct()
			->from('tz')
			->order_by('offset asc');
		$q=$this->db->get();
		return $q->result();
	}
	public function getTzInfo($tid=null){
		
	}
}