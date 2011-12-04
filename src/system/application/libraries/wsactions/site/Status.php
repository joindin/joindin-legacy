<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Status extends BaseWsRequest {
    
    var $CI	= null;
    var $xml= null;
    
    public function Status($xml) {
        $this->CI=&get_instance();
        $this->xml=$xml;
    }
    public function checkSecurity() {
        //public function!
        return true;
    }
    //-----------------------
    public function run() {
        $arr=array(
            'data'=>array(
                'items'=>array(
                    'dt'=>date('r', time())
                )
            ),
            'output'=>'json'
        );
        // If they give us a test string, echo it back to them
        if (isset($this->xml->action->test_string)) {
            // cast this to string, otherwise we get the whole SimpleXMLElement object in there
            $arr['data']['items']['test_string']=(string) $this->xml->action->test_string;
        }
        return $arr;
    }
    
}
?>
