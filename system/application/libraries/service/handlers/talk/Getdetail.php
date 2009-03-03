<?php

/** ServiceHandler */
require_once BASEPATH . 'application/libraries/service/ServiceHandler.php';
/** ServiceXmlReponse */
require_once BASEPATH . 'application/libraries/service/ServiceXmlResponse.php';
/** Talks_model */
require_once BASEPATH . 'application/models/talks_model.php';

class Getdetail extends ServiceHandler
{
    
    public function isAuthorizedRequest()
    {
        return true;
    }
    
    public function handle()
    {
        
        $talkId = $this->_xmlData->action->talk_id;
        
        $dao = new Talks_model();
        $talks = $dao->getTalks($talkId);
        $talkData = $talks[0];
        
        $xmlResponse = new ServiceXmlResponse();
        $xmlResponse->addArray($talkData, 'talk');
        
        return $xmlResponse->getResponse();
    }
}