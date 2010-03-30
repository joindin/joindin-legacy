<?php
/* 
 * Import XML values for events and talks
 */

class Xmlimport {

    private $_schema_dir    = null;
    private $_ci	    = null;

    public function __construct(){
		$this->CI=&get_instance();
		$this->_schema_dir=$_SERVER['DOCUMENT_ROOT'].'/inc/xml';
		$this->CI->load->database();
    }

    public function import($data,$type='event',$id){
		if($this->_validateSchema($data) && $type=='event'){
		    $this->_importEvent($id,$data);
		}else{ throw new Exception('XML data could not be verified!'); }
    }
    //---------------
    private function _validateSchema($data,$type='event'){
		// get the schema file and run the validation
		$p=$this->_schema_dir.'/schema_'.strtolower($type).'.xml';
		if(!is_file($p)){ throw new Exception('Schema file not found!'); }

		$xml=new DOMDocument();
		$xml->loadXML($data);
		return @$xml->schemaValidate($p);
    }
    private function _importEvent($eid,$data){
		// load the XML
		$xml=simplexml_load_string($data);

		// get the talk categories
		$categories_query = $this->CI->db->get('categories');
		$this->_categories = $categories_query->result();

		// get the talk tracks
		$tracks_where = array('event_id' => $eid);
		$tracks_query = $this->CI->db->get_where('event_track', $tracks_where);
		$this->_tracks = $tracks_query->result();

		foreach($xml->sessions->session as $k=>$ses){
		    $this->_importSession($eid,$ses);
		}
    }
    private function _importSession($eid,$data){

		$in=array(
		    'talk_title'    =>(string)$data->session_title,
		    'speaker'	    =>(string)$data->session_speaker,
		    'slides_link'   =>'',
		    'date_given'    =>$data->session_start,
		    'event_id'	    =>$eid,
		    'talk_desc'	    =>trim((string)$data->session_desc),
		    'active'	    =>1,
		    'owner_id'	    =>null
		);

		// danger, hardcoded language  TODO include as import field
		$in['lang'] = 8;

		// save talk detail
		$this->CI->db->insert('talks',$in);
		$talk_id = $this->CI->db->insert_id();

		// handle the category - figure out which it is, then save it
		if(isset($data->session_type)) {
			$cat_id = false;
			foreach($this->_categories as $cat) {
				if($cat->cat_title == $data->session_type) {
					$cat_id = $cat->ID;
				}
			}
			if($cat_id) {
				$this->CI->db->insert('talk_cat',array("talk_id" => $talk_id, "cat_id" => $cat_id));
			}
		}

		// handle the track - figure out which it is, then save it
		if(isset($data->session_track)) {
			$track_id = false;
			foreach($this->_tracks as $track) {
				if($track->track_name== $data->session_track) {
					$track_id = $track->ID;
				}
			}
			if($track_id) {
				$this->CI->db->insert('talk_track',array("talk_id" => $talk_id, "track_id" => $track_id));
			}
		}

    }
}

?>
