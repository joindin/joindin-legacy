<?php
/**
 * Class Getdetail
 */

/** ServiceHandler */
require_once BASEPATH . 'application/libraries/service/ServiceHandler.php';
/** ServicePublicAuth */
require_once BASEPATH . 'application/libraries/service/ServiceAuth.php';
/** Talks_model */
require_once BASEPATH . 'application/models/talks_model.php';

/**
 * Returns the details for a talk.
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Getdetail extends ServiceHandler
{
    
    public function isAuthorizedRequest()
    {
        return ServiceAuth::publicAuth($this->_queryString);
    }
    
    public function handle()
    {
        $talkId = $this->_xmlData->action->talk_id;
        
        $dao = new Talks_model();
        $talks = $dao->getTalks($talkId);
        $talkData = $talks[0];
        
        if($this->_outputType == 'json') {
            /** ServiceReponseJson */
            require_once BASEPATH . 'application/libraries/service/ServiceResponseJson.php';
            
            $response = new ServiceResponseJson($talkData);
        } else {
            /** ServiceReponseXml */
            require_once BASEPATH . 'application/libraries/service/ServiceResponseXml.php';
            
            $response = new ServiceResponseXml();
            $response->addArray($talkData, 'talk');
        }
        
        return $response->getResponse();
    }
}