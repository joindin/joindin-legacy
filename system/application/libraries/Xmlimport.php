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
		$xml=simplexml_load_string($data);
		//var_dump($xml);

		foreach($xml->sessions->session as $k=>$ses){
		    $this->_importSession($eid,$ses);
		}
    }
    private function _importSession($eid,$data){
		// Check to see if it exists
		$arr=array(
		  'talk_title'	=> (string)$data->session_title,
		  'date_given'	=> strtotime((string)$data->session_start),
		  'speaker'	=> (string)$data->session_speaker,
		  'event_id'	=> $eid
		);
		$q=$this->CI->db->get_where('talks',$arr);
		$ret=$q->result();

		$in=array(
		    'talk_title'    =>(string)$data->session_title,
		    'speaker'	    =>(string)$data->session_speaker,
		    'slides_link'   =>'',
		    'date_given'    =>strtotime((string)$data->session_start),
		    'event_id'	    =>$eid,
		    'talk_desc'	    =>trim((string)$data->session_desc),
		    'active'	    =>1,
		    'owner_id'	    =>null,
		    'lang'	    =>3
		);

		if(!empty($ret)){
		    //we're updating data based on our three keys
		    $this->CI->db->where('ID',$ret[0]->ID);
		    $this->CI->db->update('talks',$in);
		}else{
		    //new session! add the information!
		    $this->CI->db->insert('talks',$in);
		}
    }
}

?>
