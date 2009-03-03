<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/** ServiceHandler */
require_once BASEPATH . 'application/libraries/service/ServiceHandler.php';
/** ServiceXmlReponse */
require_once BASEPATH . 'application/libraries/service/ServiceXmlResponse.php';
/** Talks_model */
require_once BASEPATH . 'application/models/talks_model.php';

class Getcomments extends ServiceHandler 
{

    public function isAuthorizedRequest()
    {
        return true;
    }
    
	function handle(){
		$talkId = $this->_xmlData->action->talk_id;
		
		$dao = new Talks_model();
		$comments = $dao->getTalkComments($talkId);

		$xmlResponse = new ServiceXmlResponse();
		
		foreach($comments as $comment) {
		    $xmlResponse->addArray($comment, 'comment');
		}
		
		return $xmlResponse->getResponse();
	}
}