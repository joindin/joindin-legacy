<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 
 * Simple statistic module to calculate statistics for a given talk
 *
 * @author David Soria Parra <dsp at experimentalworks dot net>
 */
class Statistic {
	private $db = null;
	private $tid = 0;

	public function __construct($params) {
		$this->CI=&get_instance();
		if (!isset($params['tid'])) {
			throw new InvalidArgumentException();
		}
		$this->tid = $params['tid'];
		$this->CI->load->database();
		$this->db = &$this->CI->db;
	}

	public function quantile($q = 0.9) {
		$res     = $this->getTalkAverages();
		$split   = round($q * count($res));
		$quantil = array_pop(array_slice($res, 0, $split));
		if ($quantil)
			return $quantil->tavg;

		return null;
	}

	private function getTalkAverages() {
		static $res = null;
		if (null == $res) {
			$sql = sprintf("select avg(tc.rating) as tavg
				from talk_comments tc,
				talks t1, talks t2
				where t1.id = %s
				and t1.event_id = t2.event_id
				and t2.id = tc.talk_id
				group by tc.talk_id
				order by tavg", $this->db->escape($this->tid));
			$q = $this->db->query($sql);
			$res = $q->result();
		}

		return $res;
	}
}

